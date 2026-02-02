<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Adicionar debug antes da validação
$old = '        $rules = [
            \'title\' => \'required|max:255\',
            \'category\' => \'required\',';

$new = '        // DEBUG: Ver o que está sendo enviado
        \Log::info("Update Request Data:", $request->all());
        \Log::info("Category value:", ["category" => $request->category]);
        
        $rules = [
            \'title\' => \'required|max:255\',
            \'category\' => \'required\',';

$content = str_replace($old, $new, $content);

file_put_contents($file, $content);

echo "✅ Debug adicionado no controller!\n";
