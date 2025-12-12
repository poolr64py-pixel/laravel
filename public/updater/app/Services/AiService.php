<?php

namespace App\Services;

use App\Traits\AiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Helpers\TokenCounter;
use Illuminate\Support\Facades\Cache;

class AiService
{
    use AiResponseTrait;

    private $gemini_apikey, $ai_model, $gemini_model;
    protected $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $bs = DB::table('basic_settings')
            ->select('gemini_apikey', 'gemini_model')
            ->first();

        $this->ai_model = $bs->ai_model ?? "gemini";
        $this->gemini_apikey = $bs->gemini_apikey ?? "";
        $this->gemini_model = $bs->gemini_model ?? "gemini-2.0-flash";
        $this->tokenService = $tokenService;
    }

    /**
     * ====================================
     * Token Management Methods
     * ====================================
     */

    /**
     * Check if user can generate content.
     */
    public function canGenerateContent($userId, $estimatedTokens = 100)
    {
        return $this->tokenService->canUseTokens($userId, $estimatedTokens);
    }

    /**
     * Get user's token information.
     */
    public function getUserTokenInfo($userId)
    {
        return $this->tokenService->getUsageStats($userId);
    }

    /**
     * Deduct tokens after content generation.
     */
    public function deductTokens($userId, $tokensUsed, $action = 'content_generation', $details = null)
    {
        return $this->tokenService->deductTokens($userId, $tokensUsed, $action, $details);
    }

    /**
     * Count tokens before generating content
     */
    public function countPromptTokens($prompt)
    {
        return TokenCounter::countTokens($prompt, $this->gemini_apikey, $this->gemini_model);
    }

    /**
     *  Generate content with tracking
     */
    public function generateContentWithTracking($prompt, $systemPrompt = null)
    {
        try {
            // Count input tokens
            $inputTokens = $this->countPromptTokens($prompt);

            if (!$inputTokens['success']) {
                $inputTokens['total_tokens'] = TokenCounter::estimateTokens($prompt);
            }

            //Build proper messages
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt ?? 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ];

            //Generate content using Gemini API with full response
            $responseData = $this->geminiApiCallWithMetadata($messages);

            // Check if generation failed
            if (isset($responseData['error'])) {
                return [
                    'status' => 'error',
                    'message' => $responseData['error']
                ];
            }

            $content = $responseData['content'] ?? '';
            $outputTokens = $responseData['output_tokens'] ?? 0;

            if (empty($content)) {
                return [
                    'status' => 'error',
                    'message' => 'AI returned empty response'
                ];
            }

            return [
                'status' => 'success',
                'content' => $content,
                'tokens' => [
                    'input' => $inputTokens['total_tokens'],
                    'output' => $outputTokens,
                    'total' => $inputTokens['total_tokens'] + $outputTokens
                ]
            ];
        } catch (\Exception $e) {

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *  Direct Gemini API call with metadata
     */
    private function geminiApiCallWithMetadata($messages)
    {
        try {

            $contents = [];
            $systemPrompt = '';

            // Extract system prompt
            foreach ($messages as $msg) {
                if ($msg['role'] === 'system') {
                    $systemPrompt = $msg['content'] . "\n\n";
                }
            }

            // Add user messages
            foreach ($messages as $msg) {
                if ($msg['role'] === 'user') {
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [['text' => $systemPrompt . $msg['content']]]
                    ];
                    $systemPrompt = ''; 
                } elseif ($msg['role'] === 'assistant' || $msg['role'] === 'model') {
                    $contents[] = [
                        'role' => 'model',
                        'parts' => [['text' => $msg['content']]]
                    ];
                }
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->gemini_model}:generateContent";

            $payload = [
                "contents" => $contents,
                "generationConfig" => [
                    "temperature" => 0.7,
                    "topK" => 40,
                    "topP" => 0.95,
                    "maxOutputTokens" => 8192,
                ]
            ];

            $response = Http::withHeaders([
                'x-goog-api-key' => $this->gemini_apikey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, $payload);

            if ($response->failed()) {
                $error = $response->json();

                return [
                    'error' => $error['error']['message'] ?? 'API request failed'
                ];
            }

            $data = $response->json();

            // Extract content
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Extract token usage
            $outputTokens = $data['usageMetadata']['candidatesTokenCount'] ??
                TokenCounter::estimateTokens($content);

            return [
                'content' => $content,
                'output_tokens' => $outputTokens,
                'usage_metadata' => $data['usageMetadata'] ?? []
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     *  Keep this for backward compatibility (but don't use for new code)
     */
    public function generateAllPostConent($prompt, $system = null)
    {
        $messages = [
            ['role' => 'system', 'content' => $system ?? 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        try {
            $response = $this->geminiApiCallWithMetadata($messages);

            if (isset($response['error'])) {
                return [
                    'status' => 'error',
                    'error' => $response['error']
                ];
            }

            // Return just content for backward compatibility
            return $response['content'];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get user's token usage for current month
     */
    public function getUserTokenUsage($userId)
    {
        $cacheKey = "user_{$userId}_token_usage_" . date('Y-m');

        return Cache::remember($cacheKey, 600, function () use ($userId) {
            return DB::table('ai_token_usage')
                ->where('user_id', $userId)
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->sum('tokens_used') ?? 0;
        });
    }

    /**
     * Track token usage
     */
    public function trackTokenUsage($userId, $tokensUsed, $action = 'content_generation')
    {
        DB::table('ai_token_usage')->insert([
            'user_id' => $userId,
            'tokens_used' => $tokensUsed,
            'action' => $action,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Clear cache
        $cacheKey = "user_{$userId}_token_usage_" . date('Y-m');
        Cache::forget($cacheKey);
    }



    /**
     * Generate images using Pollinations AI
     * Flux → fallback safe API
     */
    public function generateImages($prompt, $numImages = 1, $size = '1024x1024')
    {
        try {
            list($width, $height) = explode('x', $size);

            $images = [];

            for ($i = 0; $i < $numImages; $i++) {

                // Primary — high quality FLUX
                $result = $this->generateWithPollinationsFlux($prompt, $width, $height, $i);

                // If failed → fallback (always works)
                if ($result['status'] === 'error') {
                    $result = $this->generateWithPollinationsFallback($prompt);
                }

                if ($result['status'] === 'error') {
                    throw new \Exception($result['message']);
                }

                $images[] = $result;

                usleep(300000); 
            }

            return $images;
        } catch (\Exception $e) {

            throw $e;
        }
    }



    /**
     * Pollinations Flux Model (High Quality)
     * Uses Cloudflare-Bypass curl
     */
    private function generateWithPollinationsFlux($prompt, $width, $height, $seed = 0)
    {
        try {
            $enhancedPrompt = $this->enhancePromptForRealEstate($prompt);
            $encoded = urlencode($enhancedPrompt);

            $url = "https://image.pollinations.ai/prompt/{$encoded}?" . http_build_query([
                'width' => $width,
                'height' => $height,
                'model' => 'flux',
                'enhance' => 'true',
                'nologo' => 'true',
                'seed' => time() + $seed
            ]);

            $imageContent = $this->cfBypassCurl($url);

            if (!$imageContent) {
                throw new \Exception("Flux endpoint returned empty response");
            }

            $info = @getimagesizefromstring($imageContent);
            if (!$info) {
                throw new \Exception("Flux returned invalid image data");
            }

            return [
                'status' => 'success',
                'base64Data' => base64_encode($imageContent),
                'mime_type' => $info['mime'],
                'width' => $info[0],
                'height' => $info[1],
            ];
        } catch (\Exception $e) {

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }



    /**
     * Pollinations Fallback without Cloudflare blocking
     */
    private function generateWithPollinationsFallback($prompt)
    {
        try {
            // Important: sanitize prompt for old endpoints
            $safePrompt = preg_replace('/[^A-Za-z0-9\-_\s]/', '', $prompt);
            $url = "https://pollinations.ai/p/" . urlencode($safePrompt);

            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_HTTPHEADER => [
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/123 Safari/537.36",
                    "Accept: image/*",
                ],
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($code !== 200) {
                throw new \Exception("Fallback returned HTTP $code - $error");
            }

            if (!$response || strlen($response) < 100) {
                throw new \Exception("Fallback returned empty or HTML instead of image");
            }

            $info = @getimagesizefromstring($response);

            if (!$info) {
                throw new \Exception("Fallback returned non-image data");
            }

            return [
                'status' => 'success',
                'base64Data' => base64_encode($response),
                'mime_type' => $info['mime'],
                'width' => $info[0],
                'height' => $info[1],
            ];
        } catch (\Exception $e) {
  
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }




    /**
     * Cloudflare Bypass cURL — Works in Laragon/Windows/Linux
     */
    private function cfBypassCurl($url)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER => [
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/123 Safari/537.36",
                "Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8",
                "Accept-Language: en-US,en;q=0.9",
                "Sec-Fetch-Dest: image",
                "Sec-Fetch-Mode: no-cors",
                "Sec-Fetch-Site: cross-site"
            ],
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($response === false || $code !== 200) {
            return false;
        }

        // Pollinations returns JSON when blocked
        if (str_starts_with(trim($response), "{")) {
            return false;
        }

        return $response;
    }



    /**
     * Enhance prompt specifically for real estate photography
     */
    private function enhancePromptForRealEstate($prompt)
    {
        $qualityModifiers = [
            'professional real estate photography',
            'high quality',
            'well-lit',
            'bright and airy',
            '4k resolution',
            'photorealistic',
            'architectural photography',
            'interior design photography',
            'wide angle',
            'HDR',
            'natural lighting'
        ];

        $modifier = $qualityModifiers[array_rand($qualityModifiers)];

        return "{$prompt}, {$modifier}, clean and modern, professional photo";
    }
}
