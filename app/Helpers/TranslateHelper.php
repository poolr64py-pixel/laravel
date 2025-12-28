<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class TranslateHelper
{
    /**
     * Traduz texto com cache de 7 dias
     */
    public static function translate($text, $from = 'pt', $to = 'en')
    {
        if ($from === $to || empty(trim($text))) {
            return $text;
        }

        // Criar chave única para o cache
        $cacheKey = 'translation_' . md5($text . $from . $to);
        
        // Verificar se já existe no cache
        return Cache::remember($cacheKey, now()->addDays(7), function() use ($text, $from, $to) {
            try {
                $cleanText = strip_tags($text);
                $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl="
                    . urlencode($from) . "&tl=" . urlencode($to)
                    . "&dt=t&q=" . urlencode($cleanText);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                
                $response = curl_exec($ch);
                curl_close($ch);

                if ($response) {
                    $result = json_decode($response, true);
                    if (isset($result[0]) && is_array($result[0])) {
                        $translated = '';
                        foreach ($result[0] as $sentence) {
                            if (isset($sentence[0])) {
                                $translated .= $sentence[0];
                            }
                        }
                        return !empty($translated) ? $translated : $text;
                    }
                }
                return $text;
            } catch (\Exception $e) {
                \Log::warning('Translation error: ' . $e->getMessage());
                return $text;
            }
        });
    }
}
