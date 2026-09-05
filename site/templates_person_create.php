<section class="card">
    <h1>Person anlegen</h1>

    <?php if ($error): ?>
        <p class="alert error"><?= \AbsenceApp\Utils::h($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/person_create.php" class="stack-form">
        <?= \AbsenceApp\Csrf::field() ?>

        <fieldset class="radio-group radio-group-compact">
            <legend>Rolle</legend>

            <label>
                <input type="radio" name="person_type" value="patient" checked>
                Patient/-in
            </label>

            <label>
                <input type="radio" name="person_type" value="staff">
                Team
            </label>
        </fieldset>

        <label for="id">Neue ID</label>
        <input id="id" name="id" type="text" autocomplete="off" required>

        <button type="submit">Anlegen</button>
    </form>
</section>
