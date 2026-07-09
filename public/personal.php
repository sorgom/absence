<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
use AbsenceApp\Auth; use AbsenceApp\Database; use AbsenceApp\Session; use AbsenceApp\Utils;
Session::start(); $auth=new Auth(Database::getConnection()); $error=null;
if ($_SERVER['REQUEST_METHOD']==='POST' && !$auth->isLoggedIn()) { $id=trim((string)($_POST['id']??'')); $password=(string)($_POST['password']??''); if ($auth->login(Auth::ROLE_STAFF,$id,$password)) { header('Location: /personal.php'); exit; } $error='Login fehlgeschlagen.'; }
if ($auth->isLoggedIn() && $auth->currentRole()===Auth::ROLE_STAFF && $auth->isFirstLogin()) { header('Location: /change_password.php'); exit; }
$title='Personalbereich'; require dirname(__DIR__) . '/templates/header.php';
if ($auth->isLoggedIn() && $auth->currentRole()===Auth::ROLE_STAFF): ?><section class="card"><h1>Personalbereich</h1><p><strong>Aktueller Login:</strong> <?= Utils::h((string)$auth->currentUserId()) ?></p><p>Die tabellarische Übersicht aktiver Abwesenheiten folgt in v0.4.</p></section><?php else: $headline='Personal-Login'; $idLabel='Personal-ID'; $action='/personal.php'; require dirname(__DIR__) . '/templates/login.php'; endif; require dirname(__DIR__) . '/templates/footer.php';
