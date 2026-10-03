<?php

$p = 'public/images/6a6ccb7d12d3e_1785514877.webp';

if (!file_exists($p)) {
    die("FICHYE PA EXISTE\n");
}

$d = file_get_contents($p);

echo "SIZE=" . strlen($d) . PHP_EOL;
echo "HEADER=" . bin2hex(substr($d, 0, 20)) . PHP_EOL;
echo "TEXT=" . substr($d, 0, 20) . PHP_EOL;