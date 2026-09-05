<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$errors = [];

$files = [
    'templates_person_create.php',
    'templates_person_delete.php',
    'templates_person_password.php',
    'person_create.php',
    'person_password.php',
    'personal.php',
];

foreach ($files as $file) {
    $content = file_get_contents($site . '/' . $file) ?: '';

    foreach (['>Personal<', "'Personal'", '"Personal"', '>Patient<', "'Patient'", '"Patient"'] as $forbidden) {
        if (str_contains($content, $forbidden)) {
            $errors[] = "Old visible wording {$forbidden} found in {$file}.";
        }
    }
}

$requiredMarkers = [
    'templates_person_create.php' => ['Team', 'Patient/-in'],
    'templates_person_delete.php' => ['Team', 'Patient/-in'],
    'templates_person_password.php' => ['Team', 'Patient/-in'],
    'person_create.php' => ["\$createdType = 'Patient/-in'", "\$createdType = \$isStaff ? 'Team' : 'Patient/-in'"],
    'person_password.php' => ["'type' => \$isStaffSelection ? 'Team' : 'Patient/-in'"],
    'personal.php' => ["\$title = 'Teambereich';"],
];

foreach ($requiredMarkers as $file => $markers) {
    $content = file_get_contents($site . '/' . $file) ?: '';

    foreach ($markers as $marker) {
        if (!str_contains($content, $marker)) {
            $errors[] = "Missing wording marker in {$file}: {$marker}";
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Wording team/patient check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Wording team/patient check: OK\n";
