<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class TokenCounter
{
  /**
   * Count tokens using Gemini API
   */
  public static function countTokens($text, $apiKey, $aimodel)
  {
    if (empty($aimodel) || strpos($aimodel, 'gemini') !== 0) {
      return [
        'success' => false,
        'error'   => 'Invalid Gemini model name'
      ];
    }

    try {
      $url = "https://generativelanguage.googleapis.com/v1beta/models/{$aimodel}:countTokens";

      $payload = [
        "contents" => [
          [
            "parts" => [
              [
                "text" => $text
              ]
            ]
          ]
        ]
      ];

      $response = Http::withHeaders([
        'x-goog-api-key' => $apiKey,
        'Content-Type' => 'application/json',
      ])->post($url, $payload);

      if ($response->successful()) {
        $data = $response->json();
        return [
          'success' => true,
          'total_tokens' => $data['totalTokens'] ?? 0,
        ];
      }

      return [
        'success' => false,
        'error' => 'Failed to count tokens'
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

  /**
   * Estimate tokens (approximate - for quick calculation)
   */
  public static function estimateTokens($text)
  {
    // Rough estimation: ~4 characters = 1 token for English
    $characters = strlen($text);
    return ceil($characters / 4);
  }

  /**
   * Check if text exceeds token limit
   */
  // public static function exceedsLimit($text, $limit = 30000, $apiKey = null)
  // {
  //   if ($apiKey) {
  //     $result = self::countTokens($text, $apiKey);
  //     if ($result['success']) {
  //       return $result['total_tokens'] > $limit;
  //     }
  //   }

  //   // Fallback to estimation
  //   $estimated = self::estimateTokens($text);
  //   return $estimated > $limit;
  // }

  /**
   * Format token count for display
   */
  public static function formatTokens($count)
  {
    if ($count >= 1000000) {
      return number_format($count / 1000000, 2) . 'M';
    } elseif ($count >= 1000) {
      return number_format($count / 1000, 2) . 'K';
    }
    return number_format($count);
  }
  
}
