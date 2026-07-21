<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$siteStructure = file_get_contents($root . '/tools/check_site_structure.php') ?: '';
$filenames = file_get_contents($root . '/tools/check_filenames.php') ?: '';
$favicon = file_get_contents($root . '/tools/check_favicon.php') ?: '';

$errors = [];

foreach ([
    "\$file->getFilename() === 'php-qrcode'",
] as $marker) {
    if (!str_contains($siteStructure, $marker)) {
        $errors[] = "Missing site structure exception marker: {$marker}";
    }
}

foreach ([
    "\$file->getExtension() === 'patch'",
    "'php-qrcode'",
] as $marker) {
    if (!str_contains($filenames, $marker)) {
        $errors[] = "Missing filename ignore marker: {$marker}";
    }
}

foreach ([
    'preg_match',
    'viewBox',
] as $marker) {
    if (!str_contains($favicon, $marker)) {
        $errors[] = "Missing favicon check marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Repository artifact ignores check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Repository artifact ignores check: OK\n";
