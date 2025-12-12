<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bcategory;
use App\Models\Blog;

class CopyBlogContent extends Command
{
    protected $signature = 'blog:copy-content';
    protected $description = 'Copia categorias e posts do inglês para português e espanhol';

    public function handle()
    {
        $idIngles = 176;
        $idEspanhol = 178;
        $idPortugues = 179;

        $this->info("=== COPIANDO CATEGORIAS ===\n");

        $categoriasEN = Bcategory::where('language_id', $idIngles)->get();

        foreach ($categoriasEN as $catEN) {
            // Para Português
            $existePT = Bcategory::where('language_id', $idPortugues)
                ->where('name', $catEN->name)->first();
            
            if (!$existePT) {
                Bcategory::create([
                    'language_id' => $idPortugues,
                    'name' => $catEN->name,
                    'status' => $catEN->status,
                    'serial_number' => $catEN->serial_number,
                ]);
                $this->line("✓ Categoria copiada para PT: {$catEN->name}");
            } else {
                $this->line("- Categoria já existe em PT: {$catEN->name}");
            }
            
            // Para Espanhol
            $existeES = Bcategory::where('language_id', $idEspanhol)
                ->where('name', $catEN->name)->first();
            
            if (!$existeES) {
                Bcategory::create([
                    'language_id' => $idEspanhol,
                    'name' => $catEN->name,
                    'status' => $catEN->status,
                    'serial_number' => $catEN->serial_number,
                ]);
                $this->line("✓ Categoria copiada para ES: {$catEN->name}");
            } else {
                $this->line("- Categoria já existe em ES: {$catEN->name}");
            }
        }

        $this->info("\n=== COPIANDO POSTS ===\n");

        $postsEN = Blog::where('language_id', $idIngles)->get();

        foreach ($postsEN as $postEN) {
            $catEN = Bcategory::find($postEN->bcategory_id);
            
            if (!$catEN) {
                $this->warn("⚠ Post sem categoria: {$postEN->title}");
                continue;
            }
            
            // Para Português
            $catPT = Bcategory::where('language_id', $idPortugues)
                ->where('name', $catEN->name)->first();
            
            if ($catPT) {
                $existePT = Blog::where('language_id', $idPortugues)
                    ->where('title', $postEN->title)->first();
                
                if (!$existePT) {
                    Blog::create([
                        'language_id' => $idPortugues,
                        'bcategory_id' => $catPT->id,
                        'title' => $postEN->title,
                         'slug' => $postEN->slug . '-pt',
                        'main_image' => $postEN->main_image,
                        'content' => $postEN->content,
                        'meta_keywords' => $postEN->meta_keywords,
                        'meta_description' => $postEN->meta_description,
                        'serial_number' => $postEN->serial_number,
                    ]);
                    $this->line("✓ Post copiado para PT: {$postEN->title}");
                } else {
                    $this->line("- Post já existe em PT: {$postEN->title}");
                }
            }
            
            // Para Espanhol
            $catES = Bcategory::where('language_id', $idEspanhol)
                ->where('name', $catEN->name)->first();
            
            if ($catES) {
                $existeES = Blog::where('language_id', $idEspanhol)
                    ->where('title', $postEN->title)->first();
                
                if (!$existeES) {
                    Blog::create([
                        'language_id' => $idEspanhol,
                        'bcategory_id' => $catES->id,
                        'title' => $postEN->title,
                        'slug' => $postEN->slug . '-es',
                         'main_image' => $postEN->main_image,
                        'content' => $postEN->content,
                        'meta_keywords' => $postEN->meta_keywords,
                        'meta_description' => $postEN->meta_description,
                        'serial_number' => $postEN->serial_number,
                    ]);
                    $this->line("✓ Post copiado para ES: {$postEN->title}");
                } else {
                    $this->line("- Post já existe em ES: {$postEN->title}");
                }
            }
        }

        $this->info("\n✅ PROCESSO CONCLUÍDO!");
        return 0;
    }
}
