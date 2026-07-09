<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\PatientRepository;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$auth->requireRole(Auth::ROLE_STAFF);

$repository = new PatientRepository($db);
$error = null;
$success = null;
$generatedPin = null;
$createdPatientId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = trim((string) ($_POST['patient_id'] ?? ''));

    try {
        $generatedPin = $repository->create($patientId);
        $createdPatientId = $patientId;
        $success = 'Patient wurde angelegt.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Patient anlegen';
require dirname(__DIR__) . '/templates/header.php';
require dirname(__DIR__) . '/templates/patient_create.php';
require dirname(__DIR__) . '/templates/footer.php';
