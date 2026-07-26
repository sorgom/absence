<?php
declare(strict_types=1);

$menu = file_get_contents(dirname(__DIR__) . '/site/menu.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

$staffStart = strpos($menu, "ROLE_STAFF");
$staffSection = $staffStart === false ? '' : substr($menu, $staffStart);

foreach ([
    '<a href="/personal.php">Übersicht</a>',
    '<a href="/index.php">Ausgang</a>',
    '<a href="/change_password.php">Passwort ändern</a>',
] as $marker) {
    if (!str_contains($staffSection, $marker)) {
        $errors[] = "Missing staff menu marker: {$marker}";
    }
}

foreach ([
    'Aktueller Login:',
    'Start / Übersicht',
    '>Abwesenheit</a>',
] as $forbidden) {
    if (str_contains($staffSection, $forbidden)) {
        $errors[] = "Old staff hamburger menu text is still present: {$forbidden}";
    }
}

$positionPersonDelete = strpos($staffSection, 'Person löschen');
$positionAusgang = strpos($staffSection, '>Ausgang</a>');
$positionPassword = strpos($staffSection, 'Passwort ändern');

if ($positionPersonDelete === false || $positionAusgang === false || $positionPassword === false || !($positionPersonDelete < $positionAusgang && $positionAusgang < $positionPassword)) {
    $errors[] = 'Staff menu order must place Ausgang after Person löschen and before Passwort ändern.';
}

foreach ([
    '.menu-toggle:focus',
    '.menu-toggle:focus-visible',
    '.menu-toggle[aria-expanded="true"]',
    'border-color: var(--border)',
    '.drawer-menu',
    'border-radius: 0.75rem',
    'border: 2px solid var(--menu-border)',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing hamburger style marker: {$marker}";
    }
}

$menuToggleBlockStart = strpos($style, '.menu-toggle:hover');
if ($menuToggleBlockStart !== false) {
    $menuToggleBlock = substr($style, $menuToggleBlockStart, 250);

    if (str_contains($menuToggleBlock, 'border-color: var(--accent)')) {
        $errors[] = 'Hamburger menu button border must not switch to accent color.';
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Hamburger personal menu check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Hamburger personal menu check: OK\n";
