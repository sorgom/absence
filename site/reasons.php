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
    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);
        $repository->replaceFromText((string) ($_POST['reasons_text'] ?? ''));
        $success = 'Gründe und Ziele wurden gespeichert.';
    } catch (Throwable $exception) {
        $error = Utils::safeMessage($exception);
    }
}

$reasonsText = $repository->asText();

$title = 'Gründe und Ziele';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_reasons.php';
require __DIR__ . '/footer.php';
