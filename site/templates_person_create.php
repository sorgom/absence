<section class="card">
    <h1>Person anlegen</h1>

    <?php if ($success): ?>
        <p class="alert success"><?= \AbsenceApp\Utils::h($success) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="alert error"><?= \AbsenceApp\Utils::h($error) ?></p>
    <?php endif; ?>

    <?php if ($createdId !== null && $generatedPassword !== null): ?>
        <div class="result-box">
            <p><strong>Typ:</strong> <?= \AbsenceApp\Utils::h($createdType) ?></p>
            <p><strong>ID:</strong> <?= \AbsenceApp\Utils::h($createdId) ?></p>
            <p><strong>Initiales Passwort:</strong> <?= \AbsenceApp\Utils::h($generatedPassword) ?></p>
            <p>Beim ersten Login muss ein eigenes Passwort festgelegt werden.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="/person_create.php" class="stack-form">
        <?= \AbsenceApp\Csrf::field() ?>

        <fieldset class="radio-group">
            <legend>Personentyp</legend>

            <label>
                <input type="radio" name="person_type" value="patient" checked>
                Patient
            </label>

            <label>
                <input type="radio" name="person_type" value="staff">
                Personal
            </label>
        </fieldset>

        <label for="id">Neue ID</label>
        <input id="id" name="id" type="text" autocomplete="off" required>

        <button type="submit">Anlegen</button>
    </form>
</section>
