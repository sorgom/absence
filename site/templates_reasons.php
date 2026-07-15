<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var array<int,array{id:int,name:string}> $reasons */
/** @var string|null $error */
/** @var string|null $success */
?>
<section class="card">
    <h1>Gründe und Ziele</h1>

    <?php if ($error): ?>
        <p class="alert error"><?= Utils::h($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="alert success"><?= Utils::h($success) ?></p>
    <?php endif; ?>

    <form method="post" action="/reasons.php" class="stack-form">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="add">

        <label for="name">Neuer Grund / neues Ziel</label>
        <input id="name" name="name" type="text" autocomplete="off" required autofocus>

        <button type="submit">Hinzufügen</button>
    </form>

    <hr class="section-divider">

    <?php if ($reasons === []): ?>
        <p class="alert notice">Es sind keine Gründe / Ziele vorhanden.</p>
    <?php else: ?>
        <form data-confirm-message="Möchten Sie diesen Grund / dieses Ziel wirklich löschen?" data-confirm-template="Möchten Sie „{value}“ wirklich löschen?" data-confirm-value-source="#reason_id" method="post" action="/reasons.php" class="stack-form">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="delete">

            <label for="reason_id">Vorhandene Einträge</label>
            <select id="reason_id" name="reason_id" required>
                <?php foreach ($reasons as $reason): ?>
                    <option value="<?= (int) $reason['id'] ?>"><?= Utils::h((string) $reason['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="danger-button">Löschen</button>
        </form>
    <?php endif; ?>
<p class="hint">Gelöschte Gründe verschwinden aus der Auswahl für neue Abwesenheiten. In bestehenden Abwesenheiten bleiben sie sichtbar.</p>
</section>
