<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$site = $root . '/site';

$qrCode = file_get_contents($site . '/qr_code.php') ?: '';
$personCreated = file_get_contents($site . '/person_created.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';

$errors = [];

if (!is_file($site . '/qr_code.php')) {
    $errors[] = 'Missing site/qr_code.php.';
}

foreach ([
    "php-qrcode/qrcode.php",
    "class_exists('QRCode')",
    "new QRCode(\$appUrl",
    "header('Content-Type: image/png')",
    'render_image',
    'imagepng',
    'HTTP_X_FORWARDED_PROTO',
    'HTTP_HOST',
] as $marker) {
    if (!str_contains($qrCode, $marker)) {
        $errors[] = "Missing QR endpoint marker: {$marker}";
    }
}

if (str_contains($qrCode, '$_GET')) {
    $errors[] = 'QR endpoint must not accept arbitrary query data.';
}

foreach ([
    'src="/qr_code.php"',
    'class="qr-code-image"',
] as $marker) {
    if (!str_contains($personCreated, $marker)) {
        $errors[] = "Missing person_created QR marker: {$marker}";
    }
}

if (str_contains($personCreated, 'QR-Code folgt')) {
    $errors[] = 'person_created.php still contains QR-Code placeholder text.';
}

foreach ([
    '.qr-code-image',
    'image-rendering: pixelated',
    '@media print',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing QR style marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "QR code handout check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "QR code handout check: OK\n";
