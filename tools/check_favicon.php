<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$header = file_get_contents($site . '/header.php') ?: '';
$icon = file_get_contents($site . '/icon.svg') ?: '';
$errors = [];

if (!is_file($site . '/icon.svg')) {
    $errors[] = 'Missing site/icon.svg.';
}

foreach ([
    'rel="icon"',
    'type="image/svg+xml"',
    'href="/icon.svg"',
] as $marker) {
    if (!str_contains($header, $marker)) {
        $errors[] = "Missing favicon header marker: {$marker}";
    }
}

foreach ([
    '<svg',
    'viewBox="0 0 64 64"',
] as $marker) {
    if (!str_contains($icon, $marker)) {
        $errors[] = "Missing icon marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Favicon check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Favicon check: OK\n";
