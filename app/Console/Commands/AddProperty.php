<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddProperty extends Command
{
    protected $signature = 'property:add';
    protected $description = 'Adicionar propriedade manualmente';

    public function handle()
    {
        $this->info("=== ADICIONAR PROPRIEDADE ===\n");

        $title = $this->ask('Titulo');
        $price = $this->ask('Preco (USD)');
        $beds = $this->ask('Quartos', 0);
        $baths = $this->ask('Banheiros', 0);
        $area = $this->ask('Area (m2)', 0);
        $address = $this->ask('Endereco', 'Asuncion, Paraguay');
        $description = $this->ask('Descricao (ou Enter para pular)') ?: $title;
        $imageUrl = $this->ask('URL da imagem (ou Enter para pular)');

        if (!$title || !$price) {
            $this->error('Titulo e preco sao obrigatorios!');
            return 1;
        }

        try {
            $propertyId = DB::table('user_properties')->insertGetId([
                'user_id' => 148,
                'purpose' => 'sale',  // ← ADICIONE ESTA LINHA
                'type' => 'house',
                'category_id' => 1,
                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 1,
                'price' => (float) $price,
                'beds' => (int) $beds,
                'bath' => (int) $baths,
                'area' => (int) $area,
                'latitude' => '-25.2831',     // ← ADICIONE
                'longitude' => '-57.4855',          
                'status' => 1,
                'featured' => 1,
                'approve_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $slug = Str::slug($title) . '-' . $propertyId;

            DB::table('user_property_contents')->insert([
                'property_id' => $propertyId,
                'language_id' => 176,
                'title' => $title,
                'slug' => $slug,
                'address' => $address,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($imageUrl) {
                $this->downloadImage($imageUrl, $propertyId);
            }

            $this->info("\nPROPRIEDADE ADICIONADA! ID: {$propertyId}");
            $this->info("URL: https://imoveis.terrasnoparaguay.com/{$slug}");

            if ($this->confirm('Adicionar outra propriedade?', true)) {
                return $this->handle();
            }

        } catch (\Exception $e) {
            $this->error('ERRO: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function downloadImage($url, $propertyId)
    {
        try {
            $imageData = file_get_contents($url);
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = uniqid() . '.' . $extension;
            $path = public_path('assets/img/properties/' . $filename);

            file_put_contents($path, $imageData);

            DB::table('user_properties')
                ->where('id', $propertyId)
                ->update(['featured_image' => $filename]);

            $this->info("Imagem baixada!");

        } catch (\Exception $e) {
            $this->warn('Erro ao baixar imagem: ' . $e->getMessage());
        }
    }
}
