<?php
declare(strict_types=1);

$index = file_get_contents(dirname(__DIR__) . '/site/index.php') ?: '';
$personal = file_get_contents(dirname(__DIR__) . '/site/personal.php') ?: '';
$errors = [];

foreach ([
    '$auth->isFirstLogin()',
    "header('Location: /change_password.php')",
    '$auth->currentRole() === Auth::ROLE_STAFF',
    '$absences = new AbsenceRepository($db)',
    "activeForPerson((string) \$auth->currentUserId())",
    "? '/index.php'",
    ": '/personal.php'",
    "header('Location: /index.php')",
] as $marker) {
    if (!str_contains($index, $marker)) {
        $errors[] = "Missing login flow marker: {$marker}";
    }
}

foreach ([
    '$auth->requireRole(Auth::ROLE_STAFF)',
    '$auth->isFirstLogin()',
    "header('Location: /change_password.php')",
] as $marker) {
    if (!str_contains($personal, $marker)) {
        $errors[] = "Missing personal guard marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Login start flow check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Login start flow check: OK\n";
