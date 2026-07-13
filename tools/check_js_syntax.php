<?php
declare(strict_types=1);

$js = file_get_contents(dirname(__DIR__) . '/site/app.js') ?: '';
$errors = [];

foreach ([
    'Password visibility toggle',
    'CSS dropdown menu',
    'Design confirmation modal',
    'data-confirm-message',
    'requestSubmit',
] as $marker) {
    if (!str_contains($js, $marker)) {
        $errors[] = "Missing JS marker: {$marker}";
    }
}

foreach ([
    'window.confirm',
    'native confirm',
] as $forbidden) {
    if (str_contains($js, $forbidden)) {
        $errors[] = "Forbidden JS marker found: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "JS syntax/static check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "JS syntax/static check: OK\n";
