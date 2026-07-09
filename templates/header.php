<?php
declare(strict_types=1);
use AbsenceApp\Config; use AbsenceApp\Session; use AbsenceApp\Utils;
Session::start(); $title = $title ?? Config::get('app_name');
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= Utils::h((string)$title) ?></title><link rel="stylesheet" href="/assets/css/style.css"><script src="/assets/js/app.js" defer></script></head>
<body><header class="app-header"><a class="brand" href="/">☰ <?= Utils::h((string)Config::get('app_name')) ?></a><?php if (Session::has('user_id')): ?><nav class="top-nav"><span>Angemeldet als <?= Utils::h((string)Session::get('user_id')) ?></span><a href="/change_password.php">Passwort ändern</a><a href="/logout.php">Logout</a></nav><?php endif; ?></header><main class="container">
