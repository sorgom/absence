<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\AbsenceRepository;
use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\ReasonRepository;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$error = null;
$message = null;

/*
 * Patient login.
 *
 * On successful login we redirect immediately to the correct next page. This
 * avoids the ambiguous "login accepted but login form is shown again" state.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$auth->isLoggedIn()) {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        $id = trim((string) ($_POST['id'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($auth->login(Auth::ROLE_PATIENT, $id, $password)) {
            header('Location: ' . ($auth->isFirstLogin() ? '/change_password.php' : '/index.php'));
            exit;
        }

        $error = 'Login fehlgeschlagen.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

/*
 * If another role is logged in on the patient URL, do not show patient data.
 */
if ($auth->isLoggedIn() && $auth->currentRole() !== Auth::ROLE_PATIENT) {
    header('Location: /personal.php');
    exit;
}

/*
 * First-login password change is mandatory for patients.
 */
if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_PATIENT && $auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

/*
 * Patient absence actions.
 */
if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_PATIENT && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        $absences = new AbsenceRepository($db);
        $patientId = (string) $auth->currentUserId();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'start') {
            $absences->start($patientId, (int) ($_POST['reason_id'] ?? 0));
            $message = 'Ausgang wurde gestartet.';
        } elseif ($action === 'end') {
            $absences->end($patientId);
            $message = 'Rückkehr wurde gespeichert.';
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Patientenbereich';
require dirname(__DIR__) . '/templates/header.php';

if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_PATIENT):
    $absences = new AbsenceRepository($db);
    $active = $absences->activeForPatient((string) $auth->currentUserId());
    ?>
    <section class="card">
        <h1>Patientenbereich</h1>

        <?php if ($message): ?>
            <p class="alert success"><?= Utils::h($message) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="alert error"><?= Utils::h($error) ?></p>
        <?php endif; ?>

        <?php if ($active): ?>
            <h2>Rückkehr</h2>
            <p><strong>Grund / Ziel:</strong> <?= Utils::h((string) $active['reason_name']) ?></p>
            <p><strong>Aufbruch:</strong> <?= Utils::h((string) $active['departure_time']) ?></p>

            <form method="post" action="/index.php">
                <?= Csrf::field() ?>
                <input type="hidden" name="action" value="end">
                <button type="submit">Ende</button>
            </form>
        <?php else: ?>
            <?php $reasons = (new ReasonRepository($db))->listAll(); ?>

            <h2>Ausgang starten</h2>

            <?php if ($reasons === []): ?>
                <p class="alert notice">Es sind noch keine Gründe / Ziele angelegt.</p>
            <?php else: ?>
                <form method="post" action="/index.php" class="stack-form">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="start">

                    <label for="reason_id">Grund / Ziel des Ausgangs</label>
                    <select id="reason_id" name="reason_id" required>
                        <?php foreach ($reasons as $reason): ?>
                            <option value="<?= (int) $reason['id'] ?>">
                                <?= Utils::h((string) $reason['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Start</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </section>
<?php
else:
    $headline = 'Patienten-Login';
    $idLabel = 'Patienten-ID';
    $action = '/index.php';
    require dirname(__DIR__) . '/templates/login.php';
endif;

require dirname(__DIR__) . '/templates/footer.php';
