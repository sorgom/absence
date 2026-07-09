<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
use AbsenceApp\Auth; use AbsenceApp\Database; use AbsenceApp\Session;
Session::start(); $auth=new Auth(Database::getConnection()); if (!$auth->isLoggedIn()) { header('Location: /'); exit; }
$error=null; $message=$auth->isFirstLogin() ? 'Bitte legen Sie beim ersten Login ein eigenes Passwort fest.' : null; $cancelUrl=$auth->currentRole()===Auth::ROLE_STAFF ? '/personal.php' : '/index.php';
if ($_SERVER['REQUEST_METHOD']==='POST') { $error=$auth->changePassword((string)($_POST['old_password']??''),(string)($_POST['new_password']??''),(string)($_POST['repeat_password']??'')); if ($error===null) { header('Location: '.$cancelUrl); exit; } }
$title='Passwort ändern'; require dirname(__DIR__) . '/templates/header.php'; require dirname(__DIR__) . '/templates/change_password.php'; require dirname(__DIR__) . '/templates/footer.php';
