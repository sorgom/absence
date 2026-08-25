<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$header = file_get_contents($site . '/header.php') ?: '';
$menu = file_get_contents($site . '/menu.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';
$errors = [];

foreach ([
    '<a class="app-icon-link" href="/index.php"',
    '<img class="app-icon" src="/icon.svg" alt="">',
    '<div class="header-center">',
    '<div class="header-right">',
    'data-menu-toggle',
] as $marker) {
    if (!str_contains($header, $marker)) {
        $errors[] = "Missing redesigned header marker: {$marker}";
    }
}

if (str_contains($header, '<span class="login-id"')) {
    $errors[] = 'Login ID must no longer be shown directly in the header.';
}

$iconPosition = strpos($header, 'app-icon-link');
$ledPosition = strpos($header, 'status-indicator');
$menuPosition = strpos($header, 'data-menu-toggle');

if ($iconPosition === false || $ledPosition === false || $menuPosition === false || !($iconPosition < $ledPosition && $ledPosition < $menuPosition)) {
    $errors[] = 'Header order must be icon, status LED, hamburger menu.';
}

foreach ([
    '<p class="menu-login">Login: <?= Utils::h($userId) ?></p>',
    '<hr class="menu-separator">',
    '<a href="/protokoll.php">Protokoll</a>',
] as $marker) {
    if (!str_contains($menu, $marker)) {
        $errors[] = "Missing redesigned menu marker: {$marker}";
    }
}

$protocolPosition = strpos($menu, '<a href="/protokoll.php">Protokoll</a>');
$separatorPosition = strpos($menu, '<hr class="menu-separator">');
$staffLoginPosition = strrpos($menu, '<p class="menu-login">Login: <?= Utils::h($userId) ?></p>');
$outingPosition = strrpos($menu, '<a href="/index.php">Ausgang</a>');

if ($protocolPosition === false || $separatorPosition === false || $staffLoginPosition === false || $outingPosition === false || !($protocolPosition < $separatorPosition && $separatorPosition < $staffLoginPosition && $staffLoginPosition < $outingPosition)) {
    $errors[] = 'Staff menu order must be Protokoll, separator, Login, Ausgang.';
}

foreach ([
    'grid-template-columns: 1fr auto 1fr',
    '.app-icon-link',
    '.app-icon',
    '.header-center .status-indicator',
    'height: 1.55rem',
    '.header-right .drawer-menu',
    '.menu-separator',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing redesigned header/menu CSS marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Header/menu redesign check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Header/menu redesign check: OK\n";
