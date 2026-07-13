<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$css = file_get_contents($site . '/style.css') ?: '';
$js = file_get_contents($site . '/app.js') ?: '';
$menu = file_get_contents($site . '/menu.php') ?: '';
$header = file_get_contents($site . '/header.php') ?: '';

$errors = [];

foreach ([
    'v0.9.6.1: authoritative CSS dropdown menu',
    '.menu-dropdown-anchor',
    'display: none !important',
    'position: absolute !important',
    'left: 0',
    'right: auto',
    '.drawer-menu.is-open',
] as $marker) {
    if (!str_contains($css, $marker)) {
        $errors[] = "Missing CSS marker: {$marker}";
    }
}

foreach ([
    'CSS dropdown menu',
    'const menu = document.getElementById',
    'const toggle = document.querySelector',
    'setOpen',
    'aria-expanded',
    "event.key === 'Escape'",
] as $marker) {
    if (!str_contains($js, $marker)) {
        $errors[] = "Missing JS marker: {$marker}";
    }
}

foreach ([
    'data-menu-toggle',
    'aria-controls="drawer-menu"',
    'aria-expanded="false"',
    'menu-dropdown-anchor',
] as $marker) {
    if (!str_contains($header, $marker)) {
        $errors[] = "Missing header marker: {$marker}";
    }
}

if (str_contains($menu, 'drawer-close')) {
    $errors[] = 'Drawer close button should not be present in dropdown menu.';
}

if ($errors !== []) {
    fwrite(STDERR, "Menu dropdown check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Menu dropdown check: OK\n";
