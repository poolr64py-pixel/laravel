<?php

$file = 'app/Http/Controllers/Admin/BlogController.php';
$content = file_get_contents($file);

// Adicionar log antes da tradução
$oldCode = '        // AUTO-TRADUÇÃO: Atualizar traduções existentes (em background)
        try {
            // Recarregar blog para pegar dados atualizados
            $blog->refresh();
            $this->updateTranslations($blog);
        } catch (\Exception $e) {
            \Log::error(\'Auto-translation update failed: \' . $e->getMessage());
        }

        Session::flash(\'success\', __(\'Updated successfully!\'));
        return "success";';

$newCode = '        \Log::info("Blog updated, starting translation for blog #{$blog->id}");
        
        // AUTO-TRADUÇÃO: Atualizar traduções existentes (em background)
        try {
            // Recarregar blog para pegar dados atualizados
            $blog->refresh();
            \Log::info("Blog refreshed, calling updateTranslations");
            $this->updateTranslations($blog);
            \Log::info("Translation completed successfully");
        } catch (\Exception $e) {
            \Log::error(\'Auto-translation update failed: \' . $e->getMessage());
            \Log::error($e->getTraceAsString());
        }

        Session::flash(\'success\', __(\'Updated successfully!\'));
        \Log::info("Returning success response");
        return "success";';

$content = str_replace($oldCode, $newCode, $content);

file_put_contents($file, $content);

echo "✅ Logs de debug adicionados!\n";
