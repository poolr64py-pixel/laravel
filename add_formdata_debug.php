<?php

$file = 'resources/views/admin/blog/blog/edit.blade.php';
$content = file_get_contents($file);

// Adicionar log do FormData antes de enviar
$old = '        $.ajax({

            url: $(form).attr(\'action\'),

            method: \'POST\',

            data: formData,';

$new = '        // Debug: ver o que está sendo enviado
        console.log("FormData contents:");
        for (let pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }
        
        $.ajax({

            url: $(form).attr(\'action\'),

            method: \'POST\',

            data: formData,';

$content = str_replace($old, $new, $content);

file_put_contents($file, $content);

echo "✅ Debug adicionado!\n";
