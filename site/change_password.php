<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\PasswordService;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$error = null;
$success = null;
$isFirstLogin = $auth->isFirstLogin();
$minPasswordLength = PasswordService::minLengthForRole((string) $auth->currentRole());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    try {
        Csrf::requireValid($_POST['csrf_token'] ?? null);

        if ($action === 'cancel') {
            if ($isFirstLogin) {
                $error = 'Beim ersten Login muss das Passwort geändert werden.';
            } else {
                header('Location: ' . ($auth->currentRole() === Auth::ROLE_STAFF ? '/personal.php' : '/index.php'));
                exit;
            }
        }

        if ($action === 'change') {
            $service = new PasswordService($db);
            $service->changePasswordForPerson(
                (string) $auth->currentUserId(),
                (string) ($_POST['new_password'] ?? ''),
                (string) ($_POST['repeat_password'] ?? ''),
                (string) $auth->currentRole()
            );

            $success = 'Passwort wurde geändert.';

            header('Location: ' . ($auth->currentRole() === Auth::ROLE_STAFF ? '/personal.php' : '/index.php'));
            exit;
        }
    } catch (Throwable $exception) {
        $error = Utils::safeMessage($exception);
    }
}

$title = 'Passwort ändern';
require __DIR__ . '/header.php';
require __DIR__ . '/templates_change_password.php';
require __DIR__ . '/footer.php';
