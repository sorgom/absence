<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';




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
$generatedPin = null;
$createdPatientId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = trim((string) ($_POST['patient_id'] ?? ''));

    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);
        $generatedPin = $repository->create($patientId);
        $createdPatientId = $patientId;
        $success = 'Patient wurde angelegt.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Patient anlegen';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_patient_create.php';
require __DIR__ . '/footer.php';
