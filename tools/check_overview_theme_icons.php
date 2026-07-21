<?php
declare(strict_types=1);

$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    'background-image: url("/trash_b.svg")',
    '@media (prefers-color-scheme: dark)',
    'background-image: url("/trash_w.svg")',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing theme icon marker: {$marker}";
    }
}

foreach ([
    'background-image: url("/trash.svg")',
    'tr.is-active td:first-child',
    'border-left: 0.25rem solid var(--accent)',
] as $forbidden) {
    if (str_contains($style, $forbidden)) {
        $errors[] = "Forbidden overview style still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Overview theme icons check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Overview theme icons check: OK\n";
