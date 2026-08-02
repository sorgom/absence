<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\PersonRepository;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$auth->requireRole(Auth::ROLE_STAFF);

$error = null;
$selectedType = (string) ($_POST['person_type'] ?? $_GET['person_type'] ?? 'patient');
$selectedType = $selectedType === 'staff' ? 'staff' : 'patient';
$isStaffSelection = $selectedType === 'staff';

$repository = new PersonRepository($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset_password') {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        $selectedId = trim((string) ($_POST['id'] ?? ''));
        $initialPassword = $repository->resetPassword($selectedId);

        Session::set('created_person', [
            'id' => $selectedId,
            'type' => $isStaffSelection ? 'Personal' : 'Patient',
            'initial_password' => $initialPassword,
        ]);

        header('Location: /person_created.php');
        exit;
    } catch (Throwable $exception) {
        $error = Utils::safeMessage($exception);
    }
}

$ids = $repository->listIds($isStaffSelection);

$title = 'Passwort zurücksetzen';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_person_password.php';
require __DIR__ . '/footer.php';
