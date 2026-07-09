<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var array<int,string> $patientIds */
/** @var string|null $error */
/** @var string|null $success */
?>
<section class="card">
    <h1>Patient löschen</h1>

    <?php if ($error): ?>
        <p class="alert error"><?= Utils::h($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="alert success"><?= Utils::h($success) ?></p>
    <?php endif; ?>

    <?php if ($patientIds === []): ?>
        <p class="alert notice">Es sind keine Patienten vorhanden.</p>
    <?php else: ?>
        <form method="post" action="/patient_delete.php" class="stack-form" data-confirm="Patient wirklich löschen? Zugehörige Abwesenheiten werden ebenfalls gelöscht.">
            <?= Csrf::field() ?>

            <label for="patient_id">Patienten-ID</label>
            <select id="patient_id" name="patient_id" required>
                <?php foreach ($patientIds as $patientId): ?>
                    <option value="<?= Utils::h($patientId) ?>"><?= Utils::h($patientId) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="danger-button">Löschen</button>
        </form>
    <?php endif; ?>
</section>
