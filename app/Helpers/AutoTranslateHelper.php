<?php

namespace App\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cache;

class AutoTranslateHelper
{
    /**
     * Traduz um texto automaticamente
     * 
     * @param string $text Texto a ser traduzido
     * @param string $fromLang Idioma de origem (ex: 'en')
     * @param string $toLang Idioma de destino (ex: 'es', 'pt')
     * @return string Texto traduzido
     */
    public static function translate($text, $fromLang, $toLang)
    {
        // Se o idioma de origem e destino são iguais, retorna o texto original
        if ($fromLang === $toLang) {
            return $text;
        }

        // Cria uma chave única para cache
        $cacheKey = "translate_{$fromLang}_{$toLang}_" . md5($text);

        // Verifica se a tradução já está em cache (válido por 30 dias)
        return Cache::remember($cacheKey, 60 * 24 * 30, function () use ($text, $fromLang, $toLang) {
            try {
                $tr = new GoogleTranslate();
                $tr->setSource($fromLang);
                $tr->setTarget($toLang);
                
                return $tr->translate($text);
            } catch (\Exception $e) {
                \Log::error("Translation error: " . $e->getMessage());
                return $text; // Retorna o texto original se houver erro
            }
        });
    }

    /**
     * Traduz múltiplos campos de um array
     * 
     * @param array $data Array com os dados
     * @param array $fields Campos a serem traduzidos
     * @param string $fromLang Idioma de origem
     * @param string $toLang Idioma de destino
     * @return array Array com os campos traduzidos
     */
    public static function translateFields($data, $fields, $fromLang, $toLang)
    {
        $translatedData = $data;

        foreach ($fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $translatedData[$field] = self::translate($data[$field], $fromLang, $toLang);
            }
        }

        return $translatedData;
    }

    /**
     * Remove HTML antes de traduzir e restaura depois
     * Útil para traduzir conteúdo de blog com HTML
     */
    public static function translateHTML($html, $fromLang, $toLang)
    {
        if ($fromLang === $toLang) {
            return $html;
        }

        // Remove tags HTML temporariamente
        $text = strip_tags($html);
        
        // Traduz o texto
        $translated = self::translate($text, $fromLang, $toLang);
        
        // Se o HTML original tinha tags, mantém a estrutura básica
        if ($html !== $text) {
            // Traduz mantendo a estrutura HTML
            try {
                $tr = new GoogleTranslate();
                $tr->setSource($fromLang);
                $tr->setTarget($toLang);
                return $tr->translate($html);
            } catch (\Exception $e) {
                return $translated;
            }
        }
        
        return $translated;
    }
}
