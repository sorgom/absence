<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$js = file_get_contents($site . '/app.js') ?: '';
$template = file_get_contents($site . '/templates_change_password.php') ?: '';
$errors = [];

foreach ([
    'Password visibility toggle',
    '[data-show-passwords]',
    '[data-password-toggle]',
    '#show_passwords',
    'input[name="show_passwords"]',
    'syncPasswordVisibility',
    "input.type = toggle.checked ? 'text' : 'password'",
] as $marker) {
    if (!str_contains($js, $marker)) {
        $errors[] = "Missing password toggle JS marker: {$marker}";
    }
}

if (!str_contains($template, 'data-show-passwords')
    && !str_contains($template, 'id="show_passwords"')
    && !str_contains($template, 'name="show_passwords"')
    && !str_contains($template, 'data-password-toggle')) {
    $errors[] = 'Password change template has no supported password toggle marker.';
}

if ($errors !== []) {
    fwrite(STDERR, "Password toggle check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Password toggle check: OK\n";
