<?php

$file = 'resources/views/admin/blog/blog/edit.blade.php';
$content = file_get_contents($file);

// Corrigir: remover selected da opção placeholder
$old = '<option value="" selected disabled>{{__("Select a category")}}</option>';
$new = '<option value="" disabled>{{__("Select a category")}}</option>';

$content = str_replace($old, $new, $content);

file_put_contents($file, $content);

echo "✅ Select de categoria corrigido!\n";
