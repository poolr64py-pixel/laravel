<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\AiTokenUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TokenService
{
  /**
   * Cache duration in seconds (10 minutes)
   */
  const CACHE_DURATION = 600;

  /**
   * Get user's currently active membership.
   *
   * @param int $userId
   * @return Membership|null
   */
  public function getActiveMembership($userId)
  {
    $cacheKey = "user_{$userId}_active_membership";

    return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userId) {
      return Membership::where('user_id', $userId)
        ->where('status', 1)
        ->where('expire_date', '>', now())
        ->whereRaw('used_tokens < total_tokens')
        ->first();
    });
  }

  /**
   * Get user's available tokens from active membership.
   *
   * @param int $userId
   * @return int
   */
  public function getAvailableTokens($userId)
  {
    $membership = $this->getActiveMembership($userId);

    return $membership ? $membership->remaining_tokens : 0;
  }

  /**
   * Check if user has enough tokens for an action.
   *
   * @param int $userId
   * @param int $required
   * @return bool
   */
  public function canUseTokens($userId, $required = 1)
  {
    $available = $this->getAvailableTokens($userId);

    return $available >= $required;
  }

  /**
   * Deduct tokens from user's active membership.
   *
   * @param int $userId
   * @param int $amount
   * @param string $action
   * @param array|null $details
   * @return array
   * @throws \Exception
   */
  public function deductTokens($userId, $amount, $action = 'content_generation', $details = null)
  {
    $membership = $this->getActiveMembership($userId);

    if (!$membership) {
      throw new \Exception('No active membership found. Please purchase a package.');
    }

    if (!$membership->hasEnoughTokens($amount)) {
      throw new \Exception("Insufficient tokens. Required: {$amount}, Available: {$membership->remaining_tokens}");
    }

    DB::beginTransaction();
    try {
      // Deduct tokens from membership
      $membership->deductTokens($amount);

      // Log token usage
      AiTokenUsage::create([
        'user_id' => $userId,
        'membership_id' => $membership->id,
        'tokens_used' => $amount,
        'action' => $action,
        'details' => is_array($details) || is_object($details)
          ? json_encode($details, JSON_UNESCAPED_UNICODE)
          : $details,
      ]);

      DB::commit();

      // Clear caches
      $this->clearUserCache($userId);

      return [
        'success' => true,
        'deducted' => $amount,
        'remaining' => $membership->fresh()->remaining_tokens,
        'total' => $membership->total_tokens,
        'used' => $membership->used_tokens
      ];
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
  }

  /**
   * Get comprehensive token usage statistics for user.
   *
   * @param int $userId
   * @return array
   */
  public function getUsageStats($userId)
  {
    $membership = $this->getActiveMembership($userId);

    if (!$membership) {
      return [
        'has_membership' => false,
        'total' => 0,
        'used' => 0,
        'remaining' => 0,
        'percentage' => 0,
        'is_expired' => true,
        'expires_at' => null
      ];
    }

    return [
      'has_membership' => true,
      'membership_id' => $membership->id,
      'package_name' => $membership->package->name ?? 'N/A',
      'total' => $membership->total_tokens,
      'used' => $membership->used_tokens,
      'remaining' => $membership->remaining_tokens,
      'percentage' => $membership->token_usage_percentage,
      'is_expired' => $membership->is_expired,
      'expires_at' => $membership->expire_date,
    ];
  }

  /**
   * Get token usage history for user.
   *
   * @param int $userId
   * @param int $limit
   * @return \Illuminate\Support\Collection
   */
  public function getUsageHistory($userId, $limit = 20)
  {
    return AiTokenUsage::where('user_id', $userId)
      ->with('membership')
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get();
  }

  

  /**
   * Add bonus tokens to user's membership.
   *
   * @param int $userId
   * @param int $amount
   * @param string $reason
   * @return array
   * @throws \Exception
   */
  public function addBonusTokens($userId, $amount, $reason = 'Admin bonus')
  {
    $membership = $this->getActiveMembership($userId);

    if (!$membership) {
      throw new \Exception('No active membership found.');
    }

    $membership->addTokens($amount);

    // Log bonus
    AiTokenUsage::create([
      'user_id' => $userId,
      'membership_id' => $membership->id,
      'tokens_used' => -$amount, 
      'action' => 'bonus_tokens',
      'details' => ['reason' => $reason]
    ]);

    $this->clearUserCache($userId);

    return [
      'success' => true,
      'added' => $amount,
      'new_total' => $membership->fresh()->total_tokens
    ];
  }

  /**
   * Clear all caches related to user's tokens.
   *
   * @param int $userId
   * @return void
   */
  private function clearUserCache($userId)
  {
    Cache::forget("user_{$userId}_active_membership");
    Cache::forget("user_{$userId}_available_tokens");
  }
}
