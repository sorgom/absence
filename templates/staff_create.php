<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var string|null $error */
/** @var string|null $success */
/** @var string|null $generatedPassword */
/** @var string|null $createdStaffId */
?>
<section class="card">
    <h1>Personal-Member anlegen</h1>

    <?php if ($error): ?>
        <p class="alert error"><?= Utils::h($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="alert success"><?= Utils::h($success) ?></p>
    <?php endif; ?>

    <?php if ($generatedPassword !== null && $createdStaffId !== null): ?>
        <div class="result-box">
            <p><strong>Personal-ID:</strong> <?= Utils::h($createdStaffId) ?></p>
            <p><strong>Initiales Passwort:</strong> <code><?= Utils::h($generatedPassword) ?></code></p>
            <p class="muted">Das Passwort wird nur jetzt angezeigt. Beim ersten Login muss ein eigenes Passwort festgelegt werden.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="/staff_create.php" class="stack-form">
        <?= Csrf::field() ?>
        <label for="staff_id">Personal-ID</label>
        <input id="staff_id" name="staff_id" type="text" autocomplete="off" required autofocus>

        <button type="submit">Anlegen</button>
    </form>
</section>
