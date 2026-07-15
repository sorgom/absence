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
$createdId = null;
$generatedPassword = null;
$createdType = 'Patient';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        $personType = (string) ($_POST['person_type'] ?? 'patient');
        $isStaff = $personType === 'staff';
        $createdType = $isStaff ? 'Personal' : 'Patient';

        $repository = new PersonRepository($db);
        $createdId = trim((string) ($_POST['id'] ?? ''));
        $generatedPassword = $repository->create($createdId, $isStaff);

        $success = 'Person wurde angelegt.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Person anlegen';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_person_create.php';
require __DIR__ . '/footer.php';
