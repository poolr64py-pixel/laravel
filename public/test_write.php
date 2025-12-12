<?php
$dir = dirname(__DIR__) . '/storage/app/purifier/HTML';
echo "Usuário PHP Web: " . posix_getpwuid(posix_geteuid())['name'] . "<br>";
echo "Gravável: " . (is_writable($dir) ? 'SIM' : 'NÃO') . "<br>";
echo "Owner: " . posix_getpwuid(fileowner($dir))['name'] . "<br>";
$test = @file_put_contents($dir . '/test.txt', 'ok');
echo "Teste: " . ($test ? 'OK' : 'FALHOU');
