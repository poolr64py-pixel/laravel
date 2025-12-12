<?php
$dir = __DIR__ . '/storage/app/purifier/HTML';
echo "Gravável: " . (is_writable($dir) ? 'SIM' : 'NÃO') . "\n";
echo "Usuário PHP: " . posix_getpwuid(posix_geteuid())['name'] . "\n";
$test = file_put_contents($dir . '/test.txt', 'test');
echo "Resultado: " . ($test ? 'OK' : 'FALHOU') . "\n";
