<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\AbsenceCleanup;
use AbsenceApp\AuditLogRepository;
use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
AbsenceCleanup::run($db);
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$auth->requireRole(Auth::ROLE_STAFF);

if ($auth->isFirstLogin()) {
    header('Location: /change_password.php');
    exit;
}

$entries = (new AuditLogRepository($db))->listAll();

$title = 'Protokoll';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_protokoll.php';
require __DIR__ . '/footer.php';
