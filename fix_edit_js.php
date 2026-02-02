<?php

$file = 'resources/views/admin/blog/blog/edit.blade.php';
$content = file_get_contents($file);

// Melhorar o tratamento de resposta
$oldJs = "success: function(res) { console.log(\"Resposta:\", res); if(res == 'success') location.href = '{{ route(\"admin.blog.index\", [\"language\" => request(\"language\")]) }}'; else alert(res); }";

$newJs = "success: function(res) { 
                console.log(\"Resposta:\", res); 
                console.log(\"Tipo:\", typeof res);
                
                if(typeof res === 'object') {
                    console.log(\"Erros de validação:\", res);
                    let errorMsg = 'Erros de validação:\\n';
                    for(let field in res) {
                        errorMsg += field + ': ' + res[field] + '\\n';
                    }
                    alert(errorMsg);
                    $('#submitBtn').prop('disabled', false).html('Atualizar');
                } else if(res == 'success') {
                    location.href = '{{ route(\"admin.blog.index\", [\"language\" => request(\"language\")]) }}';
                } else {
                    alert('Resposta inesperada: ' + res);
                    $('#submitBtn').prop('disabled', false).html('Atualizar');
                }
            }";

$content = str_replace($oldJs, $newJs, $content);

file_put_contents($file, $content);

echo "✅ JavaScript melhorado para mostrar erros!\n";
