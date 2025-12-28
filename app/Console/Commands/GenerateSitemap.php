<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\User\Property\Property;
use App\Models\User\Project\Project;
use App\Models\Blog;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Gerar sitemap do site';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Páginas estáticas
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/imoveis')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/projetos')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/blog')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        $sitemap->add(Url::create('/contact')->setPriority(0.7));
        $sitemap->add(Url::create('/faq')->setPriority(0.6));

        // Imóveis ativos
        Property::where('status', 1)->chunk(100, function($properties) use ($sitemap) {
            foreach($properties as $property) {
                $content = $property->contents()->first();
                if ($content && $content->slug) {
                    $sitemap->add(Url::create("/imoveis/{$content->slug}")->setPriority(0.8));
                }
            }
        });

        // Projetos ativos
        Project::where('complete_status', 1)->chunk(100, function($projects) use ($sitemap) {
            foreach($projects as $project) {
                $content = $project->contents()->first();
                if ($content && $content->slug) {
                    $sitemap->add(Url::create("/projetos/{$content->slug}")->setPriority(0.8));
                }
            }
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ Sitemap gerado com sucesso!');
    }
}
