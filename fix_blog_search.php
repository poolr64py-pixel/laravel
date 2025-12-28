<?php
$file = 'app/Http/Controllers/Front/FrontendController.php';
$content = file_get_contents($file);

// Procurar o bloco da query de blogs
$old = <<<'OLD'
        $data['blogs'] = Blog::when($term, function ($query, $term) {
            return $query->where('title', 'like', '%' . $term . '%');
        })->when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->when($category_id, function ($query, $category_id) {
            return $query->where('bcategory_id', $category_id);
        })->orderBy('serial_number', 'ASC')
            ->paginate(15);
OLD;

$new = <<<'NEW'
        $data['blogs'] = Blog::where('language_id', $lang_id)
            ->when($term, function ($query, $term) {
                return $query->where('title', 'like', '%' . $term . '%');
            })
            ->when($category_id, function ($query, $category_id) {
                return $query->where('bcategory_id', $category_id);
            })
            ->orderBy('serial_number', 'ASC')
            ->paginate(15);
NEW;

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);

echo "✅ Busca corrigida! Agora sempre filtra por idioma primeiro.\n";
