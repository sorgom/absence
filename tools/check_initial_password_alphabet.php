<?php
declare(strict_types=1);

$repository = file_get_contents(dirname(__DIR__) . '/site/person_repository.php') ?: '';
$errors = [];

foreach ([
    "private function generatePin(bool \$isStaff): string",
    "\$alphabet = 'abcdefghijkmnopqrstuvwxyz23456789';",
    "PasswordService::minLengthForRole",
    "random_int(0, strlen(\$alphabet) - 1)",
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing initial password alphabet marker: {$marker}";
    }
}

foreach ([
    "abcdefghijkmnopqrstuvwxyz0123456789",
    "0123456789",
    "generatePassword",
    "str_pad((string) random_int(0, 9999), 4",
] as $forbidden) {
    if (str_contains($repository, $forbidden)) {
        $errors[] = "Forbidden old password marker still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Initial password alphabet check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Initial password alphabet check: OK\n";
