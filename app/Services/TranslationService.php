<?php

namespace App\Services;

use DeepL\Translator;

class TranslationService
{
    protected $translator;
    
    public function __construct()
    {
        $apiKey = config('translation.deepl_api_key') ?: env('DEEPL_API_KEY');
        
        if (!$apiKey) {
            throw new \Exception('DEEPL_API_KEY not configured');
        }
        
        $this->translator = new Translator($apiKey);
    }
    
    public function translate($text, $targetLang, $sourceLang = null)
    {
        if (empty($text)) {
            return '';
        }
        
        try {
            $langMap = [
                'en' => 'EN-US',
                'es' => 'ES',
                'pt' => 'PT-BR'
            ];
            
            $target = $langMap[$targetLang] ?? strtoupper($targetLang);
            
            $result = $this->translator->translateText(
                $text,
                $sourceLang,
                $target,
                ['tag_handling' => 'html']
            );
            
            return $result->text;
            
        } catch (\Exception $e) {
            \Log::error('Translation error: ' . $e->getMessage());
            return $text;
        }
    }
    
    public function translateBlog($blog, $targetLangCode)
    {
        return [
            'title' => $this->translate($blog->title, $targetLangCode),
            'content' => $this->translate($blog->content, $targetLangCode),
            'meta_keywords' => $this->translate($blog->meta_keywords ?? '', $targetLangCode),
            'meta_description' => $this->translate($blog->meta_description ?? '', $targetLangCode),
        ];
    }
}
