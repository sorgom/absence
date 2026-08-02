<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\AbsenceCleanup;
use AbsenceApp\AbsenceRepository;
use AbsenceApp\Auth;
use AbsenceApp\Config;
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

// isLoggedIn() also invalidates the session if the account behind it no
// longer exists (e.g. deleted while the person was still logged in). If
// that just happened, send them back to a clean login page.
if (Session::has('user_id') && !$auth->isLoggedIn()) {
    Utils::redirect('/index.php');
}

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
        $error = Utils::safeMessage($exception);
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
        } elseif ($action === 'end') {
            $absences->endOrDeleteShort(
                $personId,
                (int) Config::get('short_absence_delete_minutes')
            );
        }
    } catch (Throwable $exception) {
        $error = Utils::safeMessage($exception);
    }
}

$title = $auth->isLoggedIn() ? 'Ausgang' : 'Login';
require __DIR__ . '/header.php';

if ($auth->isLoggedIn()):
    $absences = new AbsenceRepository($db);
    $active = $absences->activeForPerson((string) $auth->currentUserId());
    ?>
    <section class="card">
        <?php if ($active): ?>
            <h1 class="active-outing-title">Ausgang <span>aktiv</span></h1>
        <?php else: ?>
            <h1>Ausgang</h1>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="alert error"><?= Utils::h($error) ?></p>
        <?php endif; ?>

        <?php if ($active): ?>
            <p><strong>Grund / Ziel:</strong> <?= Utils::h((string) $active['reason_name']) ?></p>
            <p><strong>Aufbruch:</strong> <?= Utils::localTimeElement((string) $active['departure_time']) ?></p>

            <form method="post" action="/index.php">
                <?= Csrf::field() ?>
                <input type="hidden" name="action" value="end">
                <button type="submit">Ausgang beenden</button>
            </form>
        <?php else: ?>
            <?php $reasons = (new ReasonRepository($db))->listAll(); ?>

            <?php if ($reasons === []): ?>
                <p class="alert notice">Es sind noch keine Gründe / Ziele angelegt.</p>
            <?php else: ?>
                <form method="post" action="/index.php" class="stack-form">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="start">

                    <label for="reason_id">Grund / Ziel auswählen:</label>
                    <select id="reason_id" name="reason_id" required>
                        <?php foreach ($reasons as $reason): ?>
                            <option value="<?= (int) $reason['id'] ?>">
                                <?= Utils::h((string) $reason['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Ausgang starten</button>
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
