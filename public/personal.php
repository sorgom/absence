<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/Config.php';
require dirname(__DIR__) . '/src/Database.php';
require dirname(__DIR__) . '/src/Session.php';
require dirname(__DIR__) . '/src/Utils.php';
require dirname(__DIR__) . '/src/Auth.php';
use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\Session;
Session::start();
$auth = new Auth(Database::getConnection());
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim((string)($_POST['id'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($auth->login(Auth::ROLE_STAFF, $id, $password)) {
        header('Location: /personal.php');
        exit;
    }
    $error = 'Login fehlgeschlagen.';
}
$title = 'Personal-Login';
require dirname(__DIR__) . '/templates/header.php';
if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_STAFF): ?>
    <section class="card"><h1>Personalbereich</h1><p>Login erfolgreich.</p><p><strong>Aktueller Login:</strong> <?= \AbsenceApp\Utils::h((string)$auth->currentUserId()) ?></p><?php if ($auth->isFirstLogin()): ?><p class="alert notice">Erster Login erkannt. Die Passwortänderung folgt in v0.3.</p><?php endif; ?></section>
<?php else:
    $headline = 'Personal-Login'; $idLabel = 'Personal-ID'; $action = '/personal.php';
    require dirname(__DIR__) . '/templates/login.php';
endif;
require dirname(__DIR__) . '/templates/footer.php';
