<?php
declare(strict_types=1);

$passwordService = file_get_contents(dirname(__DIR__) . '/site/password_service.php') ?: '';
$seed = file_get_contents(dirname(__DIR__) . '/tools/seed_expired_absences.php') ?: '';
$errors = [];

if (str_contains($seed, '->hash(') && !str_contains($passwordService, 'function hash(')) {
    $errors[] = 'Seed tool calls PasswordService::hash(), but that method does not exist.';
}

if (str_contains($passwordService, 'function hashPassword') && !str_contains($seed, '->hashPassword(')) {
    $errors[] = 'Seed tool should use PasswordService::hashPassword().';
}

foreach ([
    'cleanup_done_expired',
    'cleanup_done_fresh',
    'cleanup_active_old',
] as $marker) {
    if (!str_contains($seed, $marker)) {
        $errors[] = "Missing seed marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Cleanup seed runtime check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Cleanup seed runtime check: OK\n";
