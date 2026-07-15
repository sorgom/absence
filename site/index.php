<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\AbsenceCleanup;
use AbsenceApp\AbsenceRepository;
use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\ReasonRepository;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();

$db = Database::getConnection();
AbsenceCleanup::run($db);
$auth = new Auth($db);
$error = null;
$message = null;

/*
 * Unified login.
 *
 * All persons start at index.php. The persons.is_staff flag determines the
 * target after login.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$auth->isLoggedIn()) {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        $id = trim((string) ($_POST['id'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($auth->login($id, $password)) {
            if ($auth->isFirstLogin()) {
                header('Location: /change_password.php');
                exit;
            }

            if ($auth->currentRole() === Auth::ROLE_STAFF) {
                $absences = new AbsenceRepository($db);
                $target = $absences->activeForPerson((string) $auth->currentUserId()) !== null
                    ? '/index.php'
                    : '/personal.php';

                header('Location: ' . $target);
                exit;
            }

            header('Location: /index.php');
            exit;
        }

        $error = 'Login fehlgeschlagen.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

if ($auth->isLoggedIn() && $auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

/*
 * Absence actions for every logged-in person, patient and staff.
 */
if ($auth->isLoggedIn() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        $absences = new AbsenceRepository($db);
        $personId = (string) $auth->currentUserId();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'start') {
            $absences->start($personId, (int) ($_POST['reason_id'] ?? 0));
            $message = 'Ausgang wurde gestartet.';
        } elseif ($action === 'end') {
            $absences->end($personId);
            $message = 'Rückkehr wurde gespeichert.';
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = $auth->isLoggedIn() ? 'Abwesenheit' : 'Login';
require __DIR__ . '/header.php';

if ($auth->isLoggedIn()):
    $absences = new AbsenceRepository($db);
    $active = $absences->activeForPerson((string) $auth->currentUserId());
    ?>
    <section class="card">
        <h1>Abwesenheit</h1>

        <?php if ($message): ?>
            <p class="alert success"><?= Utils::h($message) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="alert error"><?= Utils::h($error) ?></p>
        <?php endif; ?>

        <?php if ($active): ?>
            <h2>Rückkehr</h2>
            <p><strong>Grund / Ziel:</strong> <?= Utils::h((string) $active['reason_name']) ?></p>
            <p><strong>Aufbruch:</strong> <?= Utils::localTimeElement((string) $active['departure_time']) ?></p>

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
    $headline = 'Login';
    $idLabel = 'ID';
    $action = '/index.php';
    require __DIR__ . '/login.php';
endif;

require __DIR__ . '/footer.php';
