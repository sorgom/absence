<?php
declare(strict_types=1);
use AbsenceApp\Auth;
use AbsenceApp\Config;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();
$title = $title ?? Config::get('app_name');
$role = Session::get('role');
$userId = (string) Session::get('user_id', '');
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Utils::h((string) $title) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
<header class="app-header">
    <div class="header-left">
        <?php require __DIR__ . '/menu.php'; ?>
        <button class="menu-toggle" type="button" data-menu-toggle aria-label="Menü öffnen">☰</button>
    <a class="brand" href="<?= $role === Auth::ROLE_STAFF ? '/personal.php' : '/index.php' ?>">
            <?= Utils::h((string) Config::get('app_name')) ?>
        </a>
    </div>

    <?php if (Session::has('user_id')): ?>
        <div class="login-info" aria-label="Aktueller Login">
            <?= $role === Auth::ROLE_STAFF ? 'Personal' : 'Patient' ?>:
            <?= Utils::h($userId) ?>
        </div>
    <?php endif; ?>
</header>
<main class="container">
