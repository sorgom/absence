<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$site = $root . '/site';
$menu = file_get_contents($site . '/menu.php') ?: '';
$page = file_get_contents($site . '/person_password.php') ?: '';
$template = file_get_contents($site . '/templates_person_password.php') ?: '';
$repository = file_get_contents($site . '/person_repository.php') ?: '';
$structure = file_get_contents($root . '/tools/check_site_structure.php') ?: '';
$errors = [];

foreach ([
    '<a href="/person_create.php">Person anlegen</a>',
    '<a href="/person_password.php">Person Passwort</a>',
] as $marker) {
    if (!str_contains($menu, $marker)) {
        $errors[] = "Missing menu marker: {$marker}";
    }
}

if (strpos($menu, 'Person anlegen') === false || strpos($menu, 'Person Passwort') === false || !(strpos($menu, 'Person anlegen') < strpos($menu, 'Person Passwort'))) {
    $errors[] = 'Menu entry Person Passwort must be placed below Person anlegen.';
}

foreach ([
    '$title = \'Passwort zurücksetzen\';',
    '$repository->listIds($isStaffSelection)',
    '$repository->resetPassword($selectedId)',
    "Session::set('created_person'",
    "header('Location: /person_created.php')",
] as $marker) {
    if (!str_contains($page, $marker)) {
        $errors[] = "Missing password reset page marker: {$marker}";
    }
}

if (str_contains($page, 'listIdsExcept')) {
    $errors[] = 'Password reset list must include the current staff person and must not use listIdsExcept.';
}

foreach ([
    '<h1>Passwort zurücksetzen</h1>',
    '<legend>Rolle</legend>',
    'name="person_type" value="patient"',
    'name="person_type" value="staff"',
    'Person auswählen',
    'Zurücksetzen',
    'data-confirm-template="Möchten Sie das Passwort von Person {value} wirklich zurücksetzen?"',
] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing password reset template marker: {$marker}";
    }
}

foreach ([
    'public function resetPassword(string $id): string',
    'first_login = 1',
    'return $password;',
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing repository password reset marker: {$marker}";
    }
}

foreach ([
    "'person_password.php'",
    "'templates_person_password.php'",
] as $marker) {
    if (!str_contains($structure, $marker)) {
        $errors[] = "Missing site structure marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Person password reset check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Person password reset check: OK\n";
