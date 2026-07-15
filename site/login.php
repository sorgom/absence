<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

$headline = $headline ?? 'Login';
$idLabel = $idLabel ?? 'ID';
$submitLabel = $submitLabel ?? 'Login';
$action = $action ?? '';
$error = $error ?? null;
$safeAction = Utils::h((string) $action);
?>
<section class="card login-card">
    <h1><?= Utils::h((string) $headline) ?></h1>

    <?php if ($error): ?>
        <p class="alert error"><?= Utils::h((string) $error) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= $safeAction ?>">
        <?= Csrf::field() ?>

        <label for="id"><?= Utils::h((string) $idLabel) ?></label>
        <input id="id" name="id" type="text" autocomplete="username" required autofocus>

        <label for="password">Passwort</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <button type="submit"><?= Utils::h((string) $submitLabel) ?></button>
    </form>
</section>
