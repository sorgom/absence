<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var string|null $error */
/** @var string|null $success */
/** @var string|null $generatedPin */
/** @var string|null $createdPatientId */
?>
<section class="card">
    <h1>Patient anlegen</h1>

    <?php if ($error): ?>
        <p class="alert error"><?= Utils::h($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="alert success"><?= Utils::h($success) ?></p>
    <?php endif; ?>

    <?php if ($generatedPin !== null && $createdPatientId !== null): ?>
        <div class="result-box">
            <p><strong>Patienten-ID:</strong> <?= Utils::h($createdPatientId) ?></p>
            <p><strong>Initiale PIN:</strong> <code><?= Utils::h($generatedPin) ?></code></p>
            <p class="muted">Die PIN wird nur jetzt angezeigt. Beim ersten Login muss der Patient ein eigenes Passwort festlegen.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="/patient_create.php" class="stack-form">
        <?= Csrf::field() ?>
        <label for="patient_id">Patienten-ID</label>
        <input id="patient_id" name="patient_id" type="text" autocomplete="off" required autofocus>

        <button type="submit">Anlegen</button>
    </form>
</section>
