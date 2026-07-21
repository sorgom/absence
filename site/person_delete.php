<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\PersonRepository;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$auth->requireRole(Auth::ROLE_STAFF);

$error = null;
$success = null;
$selectedType = (string) ($_POST['person_type'] ?? $_GET['person_type'] ?? 'patient');
$selectedType = $selectedType === 'staff' ? 'staff' : 'patient';
$isStaffSelection = $selectedType === 'staff';

$repository = new PersonRepository($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        $repository->delete(
            (string) ($_POST['id'] ?? ''),
            (string) $auth->currentUserId()
        );

        $success = 'Person wurde gelöscht.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$ids = $repository->listIdsExcept($isStaffSelection, (string) $auth->currentUserId());

$title = 'Person löschen';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_person_delete.php';
require __DIR__ . '/footer.php';
