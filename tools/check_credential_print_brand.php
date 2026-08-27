<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$page = file_get_contents($site . '/person_created.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';
$errors = [];

foreach ([
    'class="print-brand"',
    'class="print-brand-icon"',
    'src="/icon.svg"',
    'class="print-app-name"',
    "Config::get('app_name')",
] as $marker) {
    if (!str_contains($page, $marker)) {
        $errors[] = "Missing credential print brand page marker: {$marker}";
    }
}

$screenBrandPosition = strpos($style, ".print-brand {\n  display: none;");
$printMediaPosition = strpos($style, '@media print');
$printBrandPosition = strpos($style, ".print-brand {\n    align-items: center;\n    display: flex;", $printMediaPosition === false ? 0 : $printMediaPosition);

if ($screenBrandPosition === false) {
    $errors[] = 'Print brand must be hidden outside print media.';
}

if ($printMediaPosition === false || $printBrandPosition === false) {
    $errors[] = 'Print brand must be explicitly shown inside @media print.';
}

if ($screenBrandPosition !== false && $printMediaPosition !== false && $screenBrandPosition > $printMediaPosition) {
    $errors[] = 'Screen print-brand hiding rule must be outside and before @media print.';
}

foreach ([
    '.print-brand-icon',
    '.print-app-name',
    'height: 22mm',
    'font-size: 16pt',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing credential print brand style marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Credential print brand check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Credential print brand check: OK\n";
