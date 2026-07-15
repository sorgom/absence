<section class="card">
    <h1>Person löschen</h1>

    <?php if ($success): ?>
        <p class="alert success"><?= \AbsenceApp\Utils::h($success) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="alert error"><?= \AbsenceApp\Utils::h($error) ?></p>
    <?php endif; ?>

    <form method="get" action="/person_delete.php" class="stack-form">
        <fieldset class="radio-group">
            <legend>Personentyp</legend>

            <label>
                <input type="radio" name="person_type" value="patient" <?= $selectedType === 'patient' ? 'checked' : '' ?> onchange="this.form.submit()">
                Patient
            </label>

            <label>
                <input type="radio" name="person_type" value="staff" <?= $selectedType === 'staff' ? 'checked' : '' ?> onchange="this.form.submit()">
                Personal
            </label>
        </fieldset>
    </form>

    <?php if ($ids === []): ?>
        <p class="alert notice">Es sind keine Personen dieses Typs vorhanden.</p>
    <?php else: ?>
        <form method="post" action="/person_delete.php" class="stack-form" data-confirm-message="Möchten Sie diese Person wirklich löschen?" data-confirm-template="Möchten Sie Person {value} wirklich löschen?" data-confirm-value-source="#id">
            <?= \AbsenceApp\Csrf::field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="person_type" value="<?= \AbsenceApp\Utils::h($selectedType) ?>">

            <label for="id">Person auswählen</label>
            <select id="id" name="id" required>
                <?php foreach ($ids as $id): ?>
                    <option value="<?= \AbsenceApp\Utils::h($id) ?>">
                        <?= \AbsenceApp\Utils::h($id) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="danger">Löschen</button>
        </form>
    <?php endif; ?>
</section>
