<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$config = file_get_contents($root . '/site/app_config.php') ?: '';
$header = file_get_contents($root . '/site/header.php') ?: '';
$footer = file_get_contents($root . '/site/footer.php') ?: '';
$errors = [];

foreach ([
    "'app_name' =>",
] as $marker) {
    if (!str_contains($config, $marker)) {
        $errors[] = "Missing app config marker: {$marker}";
    }
}

foreach ([
    '$appName = (string) Config::get(\'app_name\')',
    '<title><?= Utils::h($appName) ?></title>',
] as $marker) {
    if (!str_contains($header, $marker)) {
        $errors[] = "Missing header title marker: {$marker}";
    }
}

foreach ([
    'use AbsenceApp\Config;',
    'Config::get(\'app_name\')',
    'AppInfo::version()',
    ' · v',
] as $marker) {
    if (!str_contains($footer, $marker)) {
        $errors[] = "Missing footer marker: {$marker}";
    }
}

if (str_contains($footer, '<small>Abwesenheits-App · v')) {
    $errors[] = 'Footer must not hard-code the app name.';
}

if (str_contains($header, '<title><?= Utils::h((string) $title) ?></title>')) {
    $errors[] = 'Browser title must not use the page title.';
}

if ($errors !== []) {
    fwrite(STDERR, "App name title/footer check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "App name title/footer check: OK\n";
