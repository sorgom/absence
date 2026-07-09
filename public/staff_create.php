<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\Session;
use AbsenceApp\StaffRepository;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$auth->requireRole(Auth::ROLE_STAFF);

$repository = new StaffRepository($db);
$error = null;
$success = null;
$generatedPassword = null;
$createdStaffId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = trim((string) ($_POST['staff_id'] ?? ''));

    try {
        $generatedPassword = $repository->create($staffId);
        $createdStaffId = $staffId;
        $success = 'Personal-Member wurde angelegt.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Personal-Member anlegen';
require dirname(__DIR__) . '/templates/header.php';
require dirname(__DIR__) . '/templates/staff_create.php';
require dirname(__DIR__) . '/templates/footer.php';
