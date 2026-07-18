<?php
declare(strict_types=1);
use AbsenceApp\AbsenceRepository;
use AbsenceApp\Auth;
use AbsenceApp\Config;
use AbsenceApp\Database;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();
$title = $title ?? Config::get('app_name');
$role = Session::get('role');
$userId = (string) Session::get('user_id', '');
$hasActiveAbsence = false;

if ($userId !== '') {
    try {
        $db = Database::getConnection();
        $hasActiveAbsence = (new AbsenceRepository($db))->activeForPerson($userId) !== null;
    } catch (\Throwable) {
        $hasActiveAbsence = false;
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Utils::h((string) $title) ?></title>
    <link rel="stylesheet" href="/style.css">
    <script src="/app.js" defer></script>
</head>
<body>
<header class="app-header">
    <div class="header-left">
        <div class="menu-dropdown-anchor">
            <button class="menu-toggle" type="button" data-menu-toggle aria-controls="drawer-menu" aria-expanded="false" aria-label="Menü öffnen">☰</button>
            <?php require __DIR__ . '/menu.php'; ?>
        </div>
    </div>

    <?php if (Session::has('user_id')): ?>
        <div class="header-right" aria-label="Aktueller Login">
            <span class="login-id"><?= Utils::h($userId) ?></span>
            <span
                class="status-indicator <?= $hasActiveAbsence ? 'is-active' : 'is-inactive' ?>"
                aria-label="<?= $hasActiveAbsence ? 'Abwesenheit aktiv' : 'Keine aktive Abwesenheit' ?>"
                title="<?= $hasActiveAbsence ? 'Abwesenheit aktiv' : 'Keine aktive Abwesenheit' ?>"
            ></span>
        </div>
    <?php endif; ?>
</header>
<main class="container">
