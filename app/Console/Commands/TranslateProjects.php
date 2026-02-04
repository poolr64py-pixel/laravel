<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User\Project\ProjectContent;
use App\Models\User\Language;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class TranslateProjects extends Command
{
    protected $signature = 'translate:projects {--project_id=}';
    protected $description = 'Translate projects to all languages using DeepL';

    public function handle()
    {
        $deeplKey = env('DEEPL_API_KEY');
        if (!$deeplKey) {
            $this->error('DEEPL_API_KEY não configurada no .env');
            return 1;
        }

        // Pegar idiomas
        $ptLang = Language::where('code', 'pt')->first();
        $esLang = Language::where('code', 'es')->first();
        $enLang = Language::where('code', 'en')->first();

        if (!$ptLang || !$esLang || !$enLang) {
            $this->error('Idiomas PT, ES ou EN não encontrados');
            return 1;
        }

        // Pegar projetos em português que não têm tradução
        $query = ProjectContent::where('language_id', $ptLang->id);
        
        if ($this->option('project_id')) {
            $query->where('project_id', $this->option('project_id'));
        }
        
        $ptProjects = $query->get();

        $this->info("Encontrados {$ptProjects->count()} projetos em português");

        foreach ($ptProjects as $ptContent) {
            $this->info("\nProcessando: {$ptContent->title}");

            // Verificar se já existe em ES
            $existsES = ProjectContent::where('project_id', $ptContent->project_id)
                ->where('language_id', $esLang->id)
                ->exists();

            if (!$existsES) {
                $this->info("  → Traduzindo para ES...");
                $this->translateAndSave($ptContent, $esLang, $deeplKey);
            } else {
                $this->warn("  → Já existe em ES");
            }

            // Verificar se já existe em EN
            $existsEN = ProjectContent::where('project_id', $ptContent->project_id)
                ->where('language_id', $enLang->id)
                ->exists();

            if (!$existsEN) {
                $this->info("  → Traduzindo para EN...");
                $this->translateAndSave($ptContent, $enLang, $deeplKey);
            } else {
                $this->warn("  → Já existe em EN");
            }
        }

        $this->info("\n✅ Tradução concluída!");
        return 0;
    }

    private function translateAndSave($sourceContent, $targetLang, $deeplKey)
    {
        $targetCode = strtoupper($targetLang->code);
        if ($targetCode == 'PT') $targetCode = 'PT-BR';
        if ($targetCode == 'EN') $targetCode = 'EN-US';

        try {
            // Traduzir título
            $titleResponse = Http::post('https://api-free.deepl.com/v2/translate', [
                'auth_key' => $deeplKey,
                'text' => $sourceContent->title,
                'source_lang' => 'PT',
                'target_lang' => $targetCode,
            ]);

            if (!$titleResponse->successful()) {
                $this->error("    Erro ao traduzir título");
                return;
            }

            $translatedTitle = $titleResponse->json()['translations'][0]['text'];

            // Traduzir descrição
            $descResponse = Http::post('https://api-free.deepl.com/v2/translate', [
                'auth_key' => $deeplKey,
                'text' => strip_tags($sourceContent->description),
                'source_lang' => 'PT',
                'target_lang' => $targetCode,
            ]);

            if (!$descResponse->successful()) {
                $this->error("    Erro ao traduzir descrição");
                return;
            }

            $translatedDesc = $descResponse->json()['translations'][0]['text'];

            // Criar slug
            $baseSlug = Str::slug($translatedTitle);
            $slug = $baseSlug . '-' . $targetLang->code;
            $count = 1;
            while (ProjectContent::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $targetLang->code . '-' . $count;
                $count++;
            }

            // Salvar
            ProjectContent::create([
                'project_id' => $sourceContent->project_id,
                'language_id' => $targetLang->id,
                'title' => $translatedTitle,
                'slug' => $slug,
                'address' => $sourceContent->address, // Manter endereço original
                'description' => '<p>' . $translatedDesc . '</p>',
                'meta_keyword' => $translatedTitle,
                'meta_description' => Str::limit($translatedDesc, 155),
            ]);

            $this->info("    ✅ Traduzido: {$translatedTitle}");
            
            // Aguardar para não sobrecarregar API
            sleep(1);

        } catch (\Exception $e) {
            $this->error("    Erro: " . $e->getMessage());
        }
    }
}
