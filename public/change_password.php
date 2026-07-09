<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Csrf;
use AbsenceApp\Database;
use AbsenceApp\PasswordService;
use AbsenceApp\Session;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = null;
$success = null;
$isFirstLogin = $auth->isFirstLogin();

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
            $service->changePassword(
                (string) $auth->currentRole(),
                (string) $auth->currentUserId(),
                (string) ($_POST['old_password'] ?? ''),
                (string) ($_POST['new_password'] ?? ''),
                (string) ($_POST['repeat_password'] ?? '')
            );

            $success = 'Passwort wurde geändert.';

            header('Location: ' . ($auth->currentRole() === Auth::ROLE_STAFF ? '/personal.php' : '/index.php'));
            exit;
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Passwort ändern';
require dirname(__DIR__) . '/templates/header.php';
require dirname(__DIR__) . '/templates/change_password.php';
require dirname(__DIR__) . '/templates/footer.php';
