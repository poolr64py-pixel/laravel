<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Traits\PropertyPromptService;
use App\Traits\CleanAiResponse;
use Illuminate\Http\Request;
use App\Services\AiService;
use App\Helpers\TokenCounter;
use Illuminate\Support\Facades\Auth;

class GenerateContentController extends Controller
{
    use PropertyPromptService, CleanAiResponse;

    /**
     * Generate property content with AI.
     *
     * @param Request $request
     * @param AiService $aiServices
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateContent(Request $request, AiService $aiServices)
    {
        
        $validatedData = $this->validateRequest($request);
        $languages = $request->input('ai_language', [$request->input('lang_code', 'en')]);

        $tenantId = null;
        if (Auth::guard('agent')->check() && Auth::guard('agent')->user()) {
            $tenantId = Auth::guard('agent')->user()->user_id;
        }

        if (!$tenantId) {
            return response()->json([
                'status' => 'error',
                'message' => __('Session expired or unauthorized. Please login again.')
            ], 401);
        }

        // ====================================
        // Step 1: Check Token Availability
        // ====================================
        $tokenInfo = $aiServices->getUserTokenInfo($tenantId);


        if (!$tokenInfo['has_membership']) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active membership found. Please purchase a package to use AI features.'
            ], 403);
        }

        if ($tokenInfo['remaining'] <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('You have exhausted your AI tokens') . '. ' . __('Please upgrade your package'),
                'token_info' => $tokenInfo
            ], 403);
        }

        // Estimate tokens needed
        $estimatedTokens = 500;

        if ($tokenInfo['remaining'] < $estimatedTokens) {
            return response()->json([
                'status' => 'warning',
                'message' => __('You have') . " {$tokenInfo['remaining']} " . __('tokens left. This action may require approximately') . " {$estimatedTokens} " . __('tokens.'),
                'token_info' => $tokenInfo
            ], 403);
        }

        // ====================================
        // Step 2: Generate Content
        // ====================================
        $allGeneratedData = [];
        $totalTokensUsed = 0;

        $systemPrompt = "You are an expert real estate content creator specializing in compelling property listings. Return ONLY valid JSON without markdown code blocks.";
        $translateSystemPrompt = "You are an expert translator. Translate the following JSON content accurately. Do not translate the JSON keys, only translate the string values. Maintain the original JSON structure. Return ONLY valid JSON without markdown code blocks.";

        try {
            // Generate for first language
            $sourceLang = $languages[0];
            $fieldType = $request->input('field_type');
            $prompt = $this->generatePropertyPrompt($sourceLang, $validatedData, $fieldType);

            // Generate content with tracking
            $result = $aiServices->generateContentWithTracking($prompt, $systemPrompt);

            if ($result['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to generate content'
                ], 500);
            }

            $sourceOutput = $result['content'];
            $totalTokensUsed += $result['tokens']['total'];

            // Clean AI response
            $sourceContent = $this->decodeAndCleanOutput($sourceOutput, $sourceLang);

            if ($sourceContent === null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'AI returned an invalid format. Please try again.',
                    'raw_response' => substr($sourceOutput, 0, 500)
                ], 500);
            }

            $allGeneratedData[$sourceLang] = $sourceContent;

            // ====================================
            // Step 3: Translate to Other Languages
            // ====================================
            if (count($languages) > 1) {
                $targetLanguages = array_slice($languages, 1);

                foreach ($targetLanguages as $lang) {
                    $translationPrompt = "Translate the following JSON content from English to {$lang}:\n\n" .
                        json_encode($sourceContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                    $translationResult = $aiServices->generateContentWithTracking(
                        $translationPrompt,
                        $translateSystemPrompt
                    );

                    if ($translationResult['status'] === 'error') {

                        // Fallback
                        $translatedContent = [];
                        foreach ($sourceContent as $key => $value) {
                            $translatedContent[$key] = "({$lang} translation failed) " . $value;
                        }
                    } else {
                        $translatedOutput = $translationResult['content'];
                        $totalTokensUsed += $translationResult['tokens']['total'];
                        $translatedContent = $this->decodeAndCleanOutput($translatedOutput, $lang);
                    }

                    if ($translatedContent === null) {
                        $translatedContent = [];
                        foreach ($sourceContent as $key => $value) {
                            $translatedContent[$key] = "({$lang} translation failed) " . $value;
                        }
                    }

                    $allGeneratedData[$lang] = $translatedContent;
                }
            }

            // ====================================
            // Step 4: Deduct Tokens
            // ====================================
            $deductionResult = $aiServices->deductTokens(
                $tenantId,
                $totalTokensUsed,
                'content_generation',
                [
                    'languages' => $languages,
                    'field_type' => $fieldType,
                    'content_keys' => array_keys($sourceContent)
                ]
            );

            // ====================================
            // Step 5: Return Response
            // ====================================
            $updatedTokenInfo = $aiServices->getUserTokenInfo($tenantId);

            return response()->json([
                'status' => 'success',
                'message' => __('Content generated successfully') . '!',
                'generated_content' => $allGeneratedData,
                'field_type' => $request->field_type,
                'tokens_used' => $totalTokensUsed,
                'token_info' => $updatedTokenInfo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during content generation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate content for projects
     *
     * @param Request $request
     * @param AiService $aiServices
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateContentForProject(Request $request, AiService $aiServices)
    {

        $validatedData = $this->validateRequestForProject($request);
        $languages = $request->input('ai_language', [$request->input('lang_code', 'en')]);
        // $user = auth()->user();
        if (Auth::guard('agent')->check() && Auth::guard('agent')->user()) {
            $tenantId = Auth::guard('agent')->user()->user_id;
        } elseif (Auth::guard('web')->check() && Auth::guard('web')->user()) {
            $tenantId = Auth::guard('web')->user()->id;
        }

        // ====================================
        // Step 1: Check Token Availability
        // ====================================
        $tokenInfo = $aiServices->getUserTokenInfo($tenantId);


        if (!$tokenInfo['has_membership']) {
            return response()->json([
                'status' => 'error',
                'message' => __('Please purchase a package to use AI features') . '.'
            ], 403);
        }

        if ($tokenInfo['remaining'] <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('You have exhausted your AI tokens') . '. ' . __('Please upgrade your package') . '.',
                'token_info' => $tokenInfo
            ], 403);
        }

        $estimatedTokens = 400;

        if ($tokenInfo['remaining'] < $estimatedTokens) {
            return response()->json([
                'status' => 'warning',
                'message' => __('You have') . " {$tokenInfo['remaining']} " . __('tokens left. This action may require approximately') . " {$estimatedTokens} " . __('tokens'),
                'token_info' => $tokenInfo
            ], 403);
        }

        // ====================================
        // Step 2: Generate Content
        // ====================================
        $allGeneratedData = [];
        $totalTokensUsed = 0;

        $systemPrompt = "You are an expert real estate content creator specializing in compelling property listings. Return ONLY valid JSON without markdown code blocks.";
        $translateSystemPrompt = "You are an expert translator. Translate the following JSON content accurately. Do not translate the JSON keys, only translate the string values. Maintain the original JSON structure. Return ONLY valid JSON without markdown code blocks.";

        try {
            // Generate for first language
            $sourceLang = $languages[0];
            $fieldType = $request->input('field_type');
            $prompt = $this->generateProjectPrompt($sourceLang, $validatedData, $fieldType);

            // Generate content with tracking
            $result = $aiServices->generateContentWithTracking($prompt, $systemPrompt);

            if ($result['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to generate content'
                ], 500);
            }

            $sourceOutput = $result['content'];
            $totalTokensUsed += $result['tokens']['total'];

            // Clean AI response
            $sourceContent = $this->decodeAndCleanOutput($sourceOutput, $sourceLang);

            if ($sourceContent === null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'AI returned an invalid format. Please try again.',
                    'raw_response' => substr($sourceOutput, 0, 500)
                ], 500);
            }

            $allGeneratedData[$sourceLang] = $sourceContent;

            // ====================================
            // Step 3: Translate to Other Languages
            // ====================================
            if (count($languages) > 1) {
                $targetLanguages = array_slice($languages, 1);

                foreach ($targetLanguages as $lang) {
                    $translationPrompt = "Translate the following JSON content from English to {$lang}:\n\n" .
                        json_encode($sourceContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                    $translationResult = $aiServices->generateContentWithTracking(
                        $translationPrompt,
                        $translateSystemPrompt
                    );

                    if ($translationResult['status'] === 'error') {

                        // Fallback
                        $translatedContent = [];
                        foreach ($sourceContent as $key => $value) {
                            $translatedContent[$key] = "({$lang} translation failed) " . $value;
                        }
                    } else {
                        $translatedOutput = $translationResult['content'];
                        $totalTokensUsed += $translationResult['tokens']['total'];
                        $translatedContent = $this->decodeAndCleanOutput($translatedOutput, $lang);
                    }

                    if ($translatedContent === null) {
                        $translatedContent = [];
                        foreach ($sourceContent as $key => $value) {
                            $translatedContent[$key] = "({$lang} translation failed) " . $value;
                        }
                    }

                    $allGeneratedData[$lang] = $translatedContent;
                }
            }

            // ====================================
            // Step 4: Deduct Tokens
            // ====================================
            $deductionResult = $aiServices->deductTokens(
                $tenantId,
                $totalTokensUsed,
                'project_content_generation',
                [
                    'languages' => $languages,
                    'field_type' => $fieldType,
                    'content_keys' => array_keys($sourceContent)
                ]
            );

            // ====================================
            // Step 5: Return Response
            // ====================================
            $updatedTokenInfo = $aiServices->getUserTokenInfo($tenantId);

            return response()->json([
                'status' => 'success',
                'message' => __('Content generated successfully') . '!',
                'generated_content' => $allGeneratedData,
                'field_type' => $request->field_type,
                'tokens_used' => $totalTokensUsed,
                'token_info' => $updatedTokenInfo
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during content generation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate request data for property.
     *
     * @param Request $request
     * @return array
     */
    private function validateRequest(Request $request)
    {
        return $request->only([
            'ai_property_type',
            'ai_purpose',
            'ai_content_prompt',
            'ai_category_name',
            'ai_country_name',
            'ai_amenities_names',
            'ai_area',
        ]);
    }

    /**
     * Validate request data for project.
     *
     * @param Request $request
     * @return array
     */
    private function validateRequestForProject(Request $request)
    {
        return $request->only([
            'ai_content_prompt',
            'ai_category_name',
            'ai_country_name',
        ]);
    }
}
