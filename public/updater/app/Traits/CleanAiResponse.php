<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait CleanAiResponse
{
     protected function decodeAndCleanOutput($output, $lang = 'en')
    {
        // Clean up unwanted wrappers or BOM characters
        $output = trim($output);
        $output = preg_replace('/^[\xEF\xBB\xBF]+/', '', $output); // remove UTF-8 BOM
        $output = preg_replace('/^```(json)?/i', '', $output);
        $output = preg_replace('/```$/', '', $output);
        $output = trim($output);

        // Sometimes model adds escaped backslashes (\" => "). so unescape if double-encoded
        $temp = json_decode($output, true);
        if ($temp === null && preg_match('/\\\\"/', $output)) {
            $output = stripslashes($output);
        }

        // Try to extract the JSON block only if entire string not decodable
        $decoded = json_decode($output, true);
        if ($decoded === null && preg_match('/\{(?:[^{}]|(?R))*\}/s', $output, $match)) {
            $decoded = json_decode($match[0], true);
        }

        // Handle null decoded
        if ($decoded === null) {
            Log::warning('AI output returned null/invalid JSON.', [
                'raw_output' => $output,
                'language' => $lang,
            ]);
            return null; // Return null to indicate failure
        }
        
        return $decoded;
    }
}
