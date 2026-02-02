<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Remover TODAS as linhas duplicadas de PropertySeoController e rotas SEO
$lines = explode("\n", $content);
$cleanLines = [];
$skipUntil = -1;

for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    
    // Pular blocos duplicados
    if (strpos($line, 'ROTAS SEO') !== false || 
        strpos($line, 'PropertySeoController') !== false ||
        strpos($line, 'ROTAS DE IMÓVEIS') !== false) {
        // Marcar para pular até o fechamento do grupo
        if (strpos($line, 'ROTAS DE IMÓVEIS') !== false) {
            // Manter este bloco
            $cleanLines[] = $line;
        } else {
            // Pular até encontrar });
            while ($i < count($lines) && strpos($lines[$i], '});') === false) {
                $i++;
            }
            if ($i < count($lines)) {
                $i++; // Pular o });
            }
        }
        continue;
    }
    
    $cleanLines[] = $line;
}

file_put_contents($file, implode("\n", $cleanLines));
echo "✅ Rotas duplicadas removidas!\n";
