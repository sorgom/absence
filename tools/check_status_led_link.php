<?php
declare(strict_types=1);

$header = file_get_contents(dirname(__DIR__) . '/site/header.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '<a',
    'class="status-indicator <?= $hasActiveAbsence ? \'is-active\' : \'is-inactive\' ?>"',
    'href="/index.php"',
    'Ausgang öffnen:',
    '</a>',
] as $marker) {
    if (!str_contains($header, $marker)) {
        $errors[] = "Missing status LED link marker: {$marker}";
    }
}

foreach ([
    '.status-indicator',
    'text-decoration: none',
    '.status-indicator:focus',
    'outline: none',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing status LED style marker: {$marker}";
    }
}

if (str_contains($header, 'title="<?= $hasActiveAbsence ? \'Abwesenheit aktiv\'')) {
    $errors[] = 'Old non-link status indicator title is still present.';
}

if (preg_match('/<span\s+[^>]*class="status-indicator/s', $header) === 1) {
    $errors[] = 'Status indicator must be rendered as a link, not as a span.';
}

if ($errors !== []) {
    fwrite(STDERR, "Status LED link check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Status LED link check: OK\n";
