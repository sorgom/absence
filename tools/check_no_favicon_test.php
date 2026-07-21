<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$header = file_get_contents($root . '/site/header.php') ?: '';
$siteStructure = file_get_contents($root . '/tools/check_site_structure.php') ?: '';
$runner = file_get_contents($root . '/tools/run_all_checks.php') ?: '';

$errors = [];

if (is_file($root . '/tools/check_favicon.php')) {
    $errors[] = 'tools/check_favicon.php must not exist.';
}

foreach ([$siteStructure, $runner] as $content) {
    if (str_contains($content, 'check_favicon.php')) {
        $errors[] = 'check_favicon.php is still referenced by checks.';
    }
}

foreach ([
    'rel="icon"',
    'type="image/svg+xml"',
    'href="/icon.svg"',
] as $marker) {
    if (!str_contains($header, $marker)) {
        $errors[] = "Favicon link missing from header: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "No favicon test check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "No favicon test check: OK\n";
