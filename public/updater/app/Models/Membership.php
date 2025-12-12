<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    public $table = "memberships";

    protected $fillable = [
        'package_price',
        'discount',
        'coupon_code',
        'price',
        'currency',
        'currency_symbol',
        'payment_method',
        'transaction_id',
        'status',
        'is_trial',
        'trial_days',
        'receipt',
        'transaction_details',
        'settings',
        'package_id',
        'user_id',
        'start_date',
        'expire_date',
        'conversation_id',
        'total_tokens',      
        'used_tokens',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function startDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value),
            get: fn($value) => Carbon::parse($value)->format('Y-m-d')
        );
    }

    public function expireDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value),
            get: fn($value) => Carbon::parse($value)->format('Y-m-d')
        );
    }
    public function isLifetimeMember(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->expire_date)->format('Y') == '9999' ? true : false,
        );
    }

    public function register($tenantId, $data)
    {
        $package = Package::findOrFail($data['package_id']);
        
        return self::create([
            'user_id' => $tenantId,
            'package_price' => $data['package_price'],
            'discount' => $data['discount'] ?? 0,
            'coupon_code' => $data['coupon_code'] ?? null,
            'price' => $data['price'],
            'currency' => $data['currency'],
            'currency_symbol' => $data['currency_symbol'],
            'payment_method' => $data["payment_method"],
            'transaction_id' => $data["transaction_id"],
            'status' => $data["status"],
            'is_trial' =>  $data["is_trial"],
            'trial_days' => $data["trial_days"],
            'receipt' => $data["receipt_name"] ?? null,
            'transaction_details' => $data['transaction_details'] ?? null,
            'settings' => $data['settings'] ?? null,
            'package_id' => $data['package_id'],
            'start_date' => $data['start_date'], // Automatically format by mutator (attribute)
            'expire_date' => $data['expire_date'], // Automatically format by mutator (attribute)
            'conversation_id' => $data['conversation_id'] ?? null,
            'total_tokens' => $package->ai_tokens,
            'used_tokens' => 0,
        ]);
    }

    /**
     * Get token usage history for this membership.
     */
    public function tokenUsages()
    {
        return $this->hasMany(AiTokenUsage::class);
    }

    /**
     * ====================================
     * Accessors & Attributes
     * ====================================
     */

    /**
     * Get remaining tokens.
     */
    public function getRemainingTokensAttribute()
    {
        return max(0, $this->total_tokens - $this->used_tokens);
    }

    /**
     * Get token usage percentage.
     */
    public function getTokenUsagePercentageAttribute()
    {
        if ($this->total_tokens == 0) {
            return 0;
        }

        return round(($this->used_tokens / $this->total_tokens) * 100, 2);
    }

    /**
     * Check if membership is expired.
     */
    public function getIsExpiredAttribute()
    {
        return $this->expire_date < now();
    }

    /**
     * ====================================
     * Token Management Methods
     * ====================================
     */

    /**
     * Check if has enough tokens.
     *
     * @param int $required
     * @return bool
     */
    public function hasEnoughTokens($required = 1)
    {
        return $this->remaining_tokens >= $required;
    }

    /**
     * Deduct tokens from membership.
     *
     * @param int $amount
     * @return $this
     * @throws \Exception
     */
    public function deductTokens($amount)
    {
        if (!$this->hasEnoughTokens($amount)) {
            throw new \Exception("Insufficient tokens. Required: {$amount}, Available: {$this->remaining_tokens}");
        }

        $this->increment('used_tokens', $amount);

        return $this;
    }

    /**
     * Add tokens (for refunds or bonuses).
     *
     * @param int $amount
     * @return $this
     */
    public function addTokens($amount)
    {
        $this->increment('total_tokens', $amount);

        return $this;
    }

    /**
     * Reset token usage (for new billing period).
     *
     * @return $this
     */
    public function resetTokens()
    {
        $this->update(['used_tokens' => 0]);

        return $this;
    }

    /**
     * Check if membership is active and has tokens.
     *
     * @return bool
     */
    public function isActiveWithTokens()
    {
        return $this->status == 1
            && !$this->is_expired
            && $this->hasEnoughTokens();
    }

}
