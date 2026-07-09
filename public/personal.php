<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\AbsenceRepository;
use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$auth->isLoggedIn()) {
    $id = trim((string) ($_POST['id'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($auth->login(Auth::ROLE_STAFF, $id, $password)) {
        header('Location: /personal.php');
        exit;
    }

    $error = 'Login fehlgeschlagen.';
}

if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_STAFF && $auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

$title = 'Personalbereich';
require dirname(__DIR__) . '/templates/header.php';

if ($auth->isLoggedIn() && $auth->currentRole() === Auth::ROLE_STAFF) {
    $activeOnly = (string) ($_GET['view'] ?? 'active') !== 'all';
    $sort = (string) ($_GET['sort'] ?? 'departure');
    $sort = in_array($sort, ['departure', 'patient'], true) ? $sort : 'departure';

    $absenceRepository = new AbsenceRepository($db);
    $absences = $absenceRepository->listForStaff($activeOnly, $sort);

    require dirname(__DIR__) . '/templates/staff_overview.php';
} else {
    $headline = 'Personal-Login';
    $idLabel = 'Personal-ID';
    $action = '/personal.php';
    require dirname(__DIR__) . '/templates/login.php';
}

require dirname(__DIR__) . '/templates/footer.php';
