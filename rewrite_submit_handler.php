<?php

$file = 'resources/views/admin/blog/blog/edit.blade.php';
$content = file_get_contents($file);

// Encontrar e substituir todo o handler do submitBtn
$pattern = '/#submitBtn.*?click.*?\}\);/s';

$newHandler = <<<'JS'
$('#submitBtn').click(function(e) {
        e.preventDefault();
        
        const form = $('#ajaxForm')[0];
        const formData = new FormData(form);
        
        // DEBUG: Mostrar todos os dados
        console.log("=== DADOS SENDO ENVIADOS ===");
        for (let [key, value] of formData.entries()) {
            console.log(key + " = " + value);
        }
        console.log("============================");
        
        $.ajax({
            url: $(form).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Atualizando...');
            },
            success: function(res) {
                console.log("Resposta:", res);
                console.log("Tipo:", typeof res);
                
                if(typeof res === 'object') {
                    console.log("Erros de validação:", res);
                    let errorMsg = 'Erros:\n';
                    for(let field in res) {
                        if(Array.isArray(res[field])) {
                            errorMsg += field + ': ' + res[field].join(', ') + '\n';
                        } else {
                            errorMsg += field + ': ' + res[field] + '\n';
                        }
                    }
                    alert(errorMsg);
                    $('#submitBtn').prop('disabled', false).html('Atualizar');
                } else if(res == 'success') {
                    window.location.href = '{{ route("admin.blog.index", ["language" => request("language")]) }}';
                } else {
                    alert('Resposta: ' + res);
                    $('#submitBtn').prop('disabled', false).html('Atualizar');
                }
            },
            error: function(xhr) {
                console.log("Erro AJAX:", xhr);
                alert('Erro: ' + xhr.status);
                $('#submitBtn').prop('disabled', false).html('Atualizar');
            }
        });
    });
JS;

// Usar regex para encontrar e substituir
if (preg_match('/(    #submitBtn.*?click.*?function.*?\{.*?\n    \}\);)/s', $content, $matches)) {
    $content = str_replace($matches[0], $newHandler, $content);
    file_put_contents($file, $content);
    echo "✅ Handler reescrito!\n";
} else {
    echo "❌ Não encontrou o handler\n";
}
