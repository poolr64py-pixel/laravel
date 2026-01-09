<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportRemaxProperties extends Command
{
    protected $signature = 'import:remax {file=remax_links.txt}';
    protected $description = 'Importar propriedades do RE/MAX';

    public function handle()
    {
        $file = base_path($this->argument('file'));
        
        if (!file_exists($file)) {
            $this->error("Arquivo nao encontrado: {$file}");
            return 1;
        }

        $urls = array_filter(array_map('trim', file($file)));
        $total = count($urls);
        $this->info("Encontradas {$total} URLs para importar");

        $userId = 148;
        $languageId = 176;
        $categoryId = 1;
        $countryId = 1;
        $stateId = 1;
        $cityId = 1;

        foreach ($urls as $index => $url) {
            $num = $index + 1;
            $this->info("\n[{$num}/{$total}] Importando: {$url}");
            
            try {
                $html = Http::timeout(30)->get($url)->body();
                
                $title = $this->extractTitle($html);
                $price = $this->extractPrice($html);
                $description = $this->extractDescription($html);
                $address = $this->extractAddress($html);
                $images = $this->extractImages($html);
                $beds = $this->extractBeds($html);
                $baths = $this->extractBaths($html);
                $area = $this->extractArea($html);

                if (!$title || !$price) {
                    $this->warn("Dados incompletos, pulando...");
                    continue;
                }

                $propertyId = DB::table('user_properties')->insertGetId([
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                    'country_id' => $countryId,
                    'state_id' => $stateId,
                    'city_id' => $cityId,
                    'price' => $price,
                    'beds' => $beds ?? 0,
                    'bath' => $baths ?? 0,
                    'area' => $area ?? 0,
                    'status' => 1,
                    'featured' => 1,
                    'approve_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $slug = Str::slug($title) . '-' . $propertyId;
                
                DB::table('user_property_contents')->insert([
                    'property_id' => $propertyId,
                    'language_id' => $languageId,
                    'title' => $title,
                    'slug' => $slug,
                    'address' => $address ?? 'Asuncion, Paraguay',
                    'description' => $description ?? $title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (!empty($images)) {
                    $this->downloadImage($images[0], $propertyId);
                }

                $this->info("OK: {$title} - USD {$price}");

            } catch (\Exception $e) {
                $this->error("ERRO: " . $e->getMessage());
            }

            sleep(2);
        }

        $this->info("\nImportacao concluida!");
        return 0;
    }

    private function extractTitle($html)
    {
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $html, $matches)) {
            return trim(strip_tags($matches[1]));
        }
        if (preg_match('/<title>(.*?)<\/title>/s', $html, $matches)) {
            return trim(strip_tags($matches[1]));
        }
        return null;
    }

    private function extractPrice($html)
    {
        if (preg_match('/\$\s*[\d,\.]+/', $html, $matches)) {
            return (float) preg_replace('/[^\d.]/', '', $matches[0]);
        }
        return null;
    }

    private function extractDescription($html)
    {
        if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractAddress($html)
    {
        if (preg_match('/Asunción[^<]*/', $html, $matches)) {
            return trim($matches[0]);
        }
        return 'Asuncion, Paraguay';
    }

    private function extractImages($html)
    {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        return array_filter($matches[1], function($url) {
            return strpos($url, 'http') === 0 && strpos($url, 'logo') === false;
        });
    }

    private function extractBeds($html)
    {
        if (preg_match('/(\d+)\s*(dormitorio|habitacion|bedroom)/i', $html, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    private function extractBaths($html)
    {
        if (preg_match('/(\d+)\s*(baño|bath)/i', $html, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    private function extractArea($html)
    {
        if (preg_match('/(\d+)\s*m[²2]/i', $html, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    private function downloadImage($url, $propertyId)
    {
        try {
            $imageData = Http::timeout(30)->get($url)->body();
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = uniqid() . '.' . $extension;
            $path = public_path('assets/img/properties/' . $filename);
            
            file_put_contents($path, $imageData);
            
            DB::table('user_properties')
                ->where('id', $propertyId)
                ->update(['featured_image' => $filename]);
                
        } catch (\Exception $e) {
            $this->warn("Erro ao baixar imagem: " . $e->getMessage());
        }
    }
}
