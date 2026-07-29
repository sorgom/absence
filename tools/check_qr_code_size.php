<?php
declare(strict_types=1);

$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '.qr-placeholder dd',
    'height: 18rem',
    'width: 18rem',
    'padding: 0',
    'max-width: 100%',
    'height: 80mm',
    'width: 80mm',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing QR size marker: {$marker}";
    }
}

foreach ([
    'height: 9rem',
    'width: 9rem',
    'height: 40mm',
    'width: 40mm',
] as $forbidden) {
    if (str_contains($style, $forbidden)) {
        $errors[] = "Old QR size marker still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "QR code size check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "QR code size check: OK\n";
