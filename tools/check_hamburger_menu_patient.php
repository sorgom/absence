<?php
declare(strict_types=1);

$menu = file_get_contents(dirname(__DIR__) . '/site/menu.php') ?: '';
$errors = [];

$patientStart = strpos($menu, "ROLE_PATIENT");
$staffStart = strpos($menu, "ROLE_STAFF");
$patientSection = $patientStart === false
    ? ''
    : substr($menu, $patientStart, $staffStart === false ? null : $staffStart - $patientStart);

foreach ([
    '<a href="/index.php">Ausgang</a>',
    '<a href="/change_password.php">Passwort ändern</a>',
    '<a href="/logout.php">Logout</a>',
] as $marker) {
    if (!str_contains($patientSection, $marker)) {
        $errors[] = "Missing patient menu marker: {$marker}";
    }
}

if (str_contains($patientSection, '>Abwesenheit</a>')) {
    $errors[] = 'Patient menu must use Ausgang instead of Abwesenheit.';
}

if ($errors !== []) {
    fwrite(STDERR, "Hamburger patient menu check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Hamburger patient menu check: OK\n";
