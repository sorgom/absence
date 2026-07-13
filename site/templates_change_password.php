<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var string|null $error */
/** @var string|null $success */
/** @var bool $isFirstLogin */
?>
<section class="card">
    <h1>Passwort ändern</h1>

    <?php if ($isFirstLogin): ?>
        <p class="alert notice">Dies ist der erste Login. Bitte legen Sie ein eigenes Passwort fest.</p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="alert error"><?= Utils::h($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="alert success"><?= Utils::h($success) ?></p>
    <?php endif; ?>

    <form method="post" action="/change_password.php" class="stack-form" data-password-form>
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="change">

        <label class="switch-row">
            <input class="checkbox" type="checkbox" data-toggle-passwords data-show-passwords>
            <span>Passwörter anzeigen</span>
        </label>

        <label for="new_password">Neues Passwort</label>
        <input id="new_password" name="new_password" type="password" autocomplete="new-password" required>

        <label for="repeat_password">Neues Passwort Wiederholung</label>
        <input id="repeat_password" name="repeat_password" type="password" autocomplete="new-password" required>

        <div class="button-row">
            <button type="submit">Ändern</button>

            <?php if (!$isFirstLogin): ?>
                <button
                    type="submit"
                    class="button-secondary"
                    name="action"
                    value="cancel"
                    formnovalidate
                >Abbruch</button>
            <?php endif; ?>
        </div>
    </form>
</section>
