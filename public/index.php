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
    if ($auth->login(Auth::ROLE_PATIENT, $id, $password)) {
        header('Location: /index.php');
        exit;
    }
    $error = 'Login fehlgeschlagen.';
}
$title = 'Patienten-Login';
require dirname(__DIR__) . '/templates/header.php';
if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_PATIENT): ?>
    <section class="card"><h1>Patientenbereich</h1><p>Login erfolgreich.</p><?php if ($auth->isFirstLogin()): ?><p class="alert notice">Erster Login erkannt. Die Passwortänderung folgt in v0.3.</p><?php endif; ?></section>
<?php else:
    $headline = 'Patienten-Login'; $idLabel = 'Patienten-ID'; $action = '/index.php';
    require dirname(__DIR__) . '/templates/login.php';
endif;
require dirname(__DIR__) . '/templates/footer.php';
