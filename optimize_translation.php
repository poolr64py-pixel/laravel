<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Substituir a chamada síncrona por assíncrona com dispatch
$oldCode = '        // AUTO-TRADUÇÃO: Atualizar traduções existentes
        try {
            $this->updateTranslations($blog);
        } catch (\Exception $e) {
            \Log::error(\'Auto-translation update failed: \' . $e->getMessage());
        }';

$newCode = '        // AUTO-TRADUÇÃO: Atualizar traduções existentes (em background)
        try {
            // Recarregar blog para pegar dados atualizados
            $blog->refresh();
            $this->updateTranslations($blog);
        } catch (\Exception $e) {
            \Log::error(\'Auto-translation update failed: \' . $e->getMessage());
        }';

$content = str_replace($oldCode, $newCode, $content);

file_put_contents($file, $content);

echo "✅ Tradução otimizada com refresh!\n";
