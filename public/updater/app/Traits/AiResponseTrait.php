<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

trait AiResponseTrait
{
    /**
     * Gemini AI response for text generation
     */
    public function gemini_ai_response($gemini_apikey, $messages, $model = 'gemini-2.5-flash')
    {
        // Convert messages to Gemini-style contents
        $contents = [];
        foreach ($messages as $msg) {
            $role = $msg['role'] === 'system' ? 'user' : $msg['role'];
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg['content']]
                ]
            ];
        }
      
        // Make request to Gemini API
        $response = Http::withHeaders([
            'x-goog-api-key' => $gemini_apikey,
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
            "contents" => $contents
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
        } else {
            return "Error: " . $response->body();
        }
    }

}
