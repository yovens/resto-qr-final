<?php
/**
 * Vérifie que chaque vue appelée par le projet existe bien.
 * Lancer depuis la racine du projet :   php verifier-vues.php
 */
$root  = __DIR__;
$views = $root . '/resources/views/';
$errors = 0;
$checked = 0;

function viewPath(string $views, string $name): string {
    return $views . str_replace('.', '/', $name) . '.blade.php';
}

$sources = [];
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/app/Http/Controllers', FilesystemIterator::SKIP_DOTS)) as $f) {
    if (substr($f, -4) === '.php') $sources[] = (string) $f;
}
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views, FilesystemIterator::SKIP_DOTS)) as $f) {
    if (substr($f, -10) === '.blade.php') $sources[] = (string) $f;
}
if (is_file($root . '/routes/web.php')) $sources[] = $root . '/routes/web.php';

$pattern = '/(?:\bview\(|Route::view\([^,]+,|@extends\(|@include\()\s*[\'"]([a-zA-Z0-9_.\-]+)[\'"]/';

foreach ($sources as $file) {
    $code = file_get_contents($file);
    $code = preg_replace('/\{\{--.*?--\}\}/s', '', $code);          // ignore les commentaires Blade
    if (!preg_match_all($pattern, $code, $m, PREG_OFFSET_CAPTURE)) continue;
    foreach ($m[1] as [$name, $offset]) {
        $checked++;
        if (!is_file(viewPath($views, $name))) {
            $errors++;
            $line = substr_count(substr($code, 0, $offset), "\n") + 1;
            printf("MANQUANTE  %-35s appelée dans %s:%d\n", $name, str_replace($root . DIRECTORY_SEPARATOR, '', $file), $line);
        }
    }
}

echo $errors
    ? "\n$errors vue(s) introuvable(s) sur $checked appels. Renommez le fichier ou corrigez l'appel.\n"
    : "OK : les $checked appels de vues trouvent leur fichier.\n";
exit($errors ? 1 : 0);
