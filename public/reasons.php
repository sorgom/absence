<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\ReasonRepository;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$auth->requireRole(Auth::ROLE_STAFF);

if ($auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

$repository = new ReasonRepository($db);
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        if ($action === 'add') {
            $repository->add((string) ($_POST['name'] ?? ''));
            $success = 'Grund / Ziel wurde hinzugefügt.';
        }

        if ($action === 'delete') {
            $repository->delete((int) ($_POST['reason_id'] ?? 0));
            $success = 'Grund / Ziel wurde gelöscht.';
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$reasons = $repository->listAll();

$title = 'Gründe / Ziele bearbeiten';
require dirname(__DIR__) . '/templates/header.php';
require dirname(__DIR__) . '/templates/reasons.php';
require dirname(__DIR__) . '/templates/footer.php';
