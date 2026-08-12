<?php
declare(strict_types=1);

use AbsenceApp\Csrf;
use AbsenceApp\Utils;

/** @var string $reasonsText */
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

    <form method="post" action="/reasons.php" class="stack-form reasons-editor-form" data-dirty-form data-dirty-message="Änderungen verwerfen?">
        <?= Csrf::field() ?>

        <textarea id="reasons_text" name="reasons_text" rows="14" autocomplete="off" aria-label="Gründe und Ziele" data-dirty-watch><?= Utils::h($reasonsText) ?></textarea>
        <p class="form-hint">Jede Zeile ist ein Eintrag. Leerzeilen werden ignoriert. Die Reihenfolge bestimmt die Auswahlliste.</p>

        <div class="form-actions two-actions">
            <button type="submit" data-dirty-save>Speichern</button>
            <a class="button-secondary" href="/personal.php" data-dirty-leave>Abbruch</a>
        </div>
    </form>
</section>
