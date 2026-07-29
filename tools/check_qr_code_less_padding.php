<?php
declare(strict_types=1);

$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

$qrBlockStart = strpos($style, '.qr-placeholder dd');
$qrBlock = $qrBlockStart === false ? '' : substr($style, $qrBlockStart, 450);

$imageBlockStart = strpos($style, '.qr-code-image');
$imageBlock = $imageBlockStart === false ? '' : substr($style, $imageBlockStart, 450);

foreach ([
    'padding: 0',
] as $marker) {
    if (!str_contains($qrBlock, $marker)) {
        $errors[] = "Missing QR box marker: {$marker}";
    }
}

foreach ([
    'height: calc(100% + 4rem)',
    'width: calc(100% + 4rem)',
    'margin: -2rem',
    'max-width: none',
] as $marker) {
    if (!str_contains($imageBlock, $marker)) {
        $errors[] = "Missing QR image marker: {$marker}";
    }
}

foreach ([
    'padding: 0.15rem',
    'padding: 0.35rem',
] as $forbidden) {
    if (str_contains($qrBlock, $forbidden)) {
        $errors[] = "Old QR box padding marker still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "QR code less-padding check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "QR code less-padding check: OK\n";
