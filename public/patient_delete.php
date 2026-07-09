<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\PatientRepository;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$auth->requireRole(Auth::ROLE_STAFF);

if ($auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

$repository = new PatientRepository($db);
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = trim((string) ($_POST['patient_id'] ?? ''));

    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);
        $repository->delete($patientId);
        $success = 'Patient wurde gelöscht.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$patientIds = $repository->listIds();

$title = 'Patient löschen';
require dirname(__DIR__) . '/templates/header.php';
require dirname(__DIR__) . '/templates/patient_delete.php';
require dirname(__DIR__) . '/templates/footer.php';
