<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var array<int,string> $staffIds */
/** @var string|null $error */
/** @var string|null $success */
?>
<section class="card">
    <h1>Personal-Member löschen</h1>

    <?php if ($error): ?>
        <p class="alert error"><?= Utils::h($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="alert success"><?= Utils::h($success) ?></p>
    <?php endif; ?>

    <?php if ($staffIds === []): ?>
        <p class="alert notice">Es sind keine löschbaren Personal-Member vorhanden.</p>
        <p class="muted">Der eigene Account wird aus Sicherheitsgründen nicht zum Löschen angeboten.</p>
    <?php else: ?>
        <form method="post" action="/staff_delete.php" class="stack-form" data-confirm="Personal-Member wirklich löschen?">
            <?= Csrf::field() ?>

            <label for="staff_id">Personal-ID</label>
            <select id="staff_id" name="staff_id" required>
                <?php foreach ($staffIds as $staffId): ?>
                    <option value="<?= Utils::h($staffId) ?>"><?= Utils::h($staffId) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="danger-button">Löschen</button>
        </form>
    <?php endif; ?>
</section>
