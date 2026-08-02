<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';




use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\ReasonRepository;
use AbsenceApp\Session;
use AbsenceApp\Utils;

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
        $error = Utils::safeMessage($exception);
    }
}

$reasons = $repository->listAll();

$title = 'Gründe und Ziele';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_reasons.php';
require __DIR__ . '/footer.php';
