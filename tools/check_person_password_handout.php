<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$repository = file_get_contents($site . '/person_repository.php') ?: '';
$created = file_get_contents($site . '/person_created.php') ?: '';
$errors = [];

foreach ([
    '$password = $this->generatePin($isStaff);',
    'PasswordService::minLengthForRole',
    '$alphabet =',
    'random_int(0, strlen($alphabet) - 1)',
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing password marker: {$marker}";
    }
}

foreach (['generatePassword', 'str_pad((string) random_int(0, 9999), 4'] as $forbidden) {
    if (str_contains($repository, $forbidden)) {
        $errors[] = "Old password generation marker still present: {$forbidden}";
    }
}

foreach ([
    'Das Passwort muss beim nächsten Login geändert werden.',
    'password-change-hint',
] as $marker) {
    if (!str_contains($created, $marker)) {
        $errors[] = "Missing handout marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Person password handout check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Person password handout check: OK\n";
