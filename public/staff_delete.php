<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\Session;
use AbsenceApp\StaffRepository;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);
$auth->requireRole(Auth::ROLE_STAFF);

if ($auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

$repository = new StaffRepository($db);
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = trim((string) ($_POST['staff_id'] ?? ''));

    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);
        $repository->delete($staffId, (string) $auth->currentUserId());
        $success = 'Personal-Member wurde gelöscht.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$staffIds = array_values(array_filter(
    $repository->listIds(),
    static fn (string $staffId): bool => $staffId !== (string) $auth->currentUserId()
));

$title = 'Personal-Member löschen';
require dirname(__DIR__) . '/templates/header.php';
require dirname(__DIR__) . '/templates/staff_delete.php';
require dirname(__DIR__) . '/templates/footer.php';
