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

        Session::set('created_person', [
            'id' => $createdId,
            'type' => $createdType,
            'initial_password' => $generatedPassword,
        ]);

        header('Location: /person_created.php');
        exit;
    } catch (Throwable $exception) {
        $error = Utils::safeMessage($exception);
    }
}

$title = 'Person anlegen';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_person_create.php';
require __DIR__ . '/footer.php';
