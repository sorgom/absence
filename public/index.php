<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
use AbsenceApp\Auth; use AbsenceApp\Database; use AbsenceApp\Session; use AbsenceApp\ReasonRepository; use AbsenceApp\AbsenceRepository; use AbsenceApp\Utils;
Session::start(); $db=Database::getConnection(); $auth=new Auth($db); $error=null; $message=null;
if ($_SERVER['REQUEST_METHOD']==='POST' && !$auth->isLoggedIn()) { $id=trim((string)($_POST['id']??'')); $password=(string)($_POST['password']??''); if ($auth->login(Auth::ROLE_PATIENT,$id,$password)) { header('Location: /index.php'); exit; } $error='Login fehlgeschlagen.'; }
if ($auth->isLoggedIn() && $auth->currentRole()===Auth::ROLE_PATIENT && $auth->isFirstLogin()) { header('Location: /change_password.php'); exit; }
if ($auth->isLoggedIn() && $auth->currentRole()===Auth::ROLE_PATIENT && $_SERVER['REQUEST_METHOD']==='POST') {
    $absences=new AbsenceRepository($db); $patientId=(string)$auth->currentUserId();
    try { if (($_POST['action']??'')==='start') { $absences->start($patientId, (int)($_POST['reason_id']??0)); $message='Ausgang wurde gestartet.'; } elseif (($_POST['action']??'')==='end') { $absences->end($patientId); $message='Rückkehr wurde gespeichert.'; } } catch (Throwable $e) { $error='Die Aktion konnte nicht ausgeführt werden.'; }
}
$title='Patientenbereich'; require dirname(__DIR__) . '/templates/header.php';
if ($auth->isLoggedIn() && $auth->currentRole()===Auth::ROLE_PATIENT): $absences=new AbsenceRepository($db); $active=$absences->activeForPatient((string)$auth->currentUserId()); ?>
<section class="card"><h1>Patientenbereich</h1><?php if ($message): ?><p class="alert notice"><?= Utils::h($message) ?></p><?php endif; ?><?php if ($error): ?><p class="alert error"><?= Utils::h($error) ?></p><?php endif; ?><?php if ($active): ?><h2>Rückkehr</h2><p><strong>Grund / Ziel:</strong> <?= Utils::h((string)$active['reason_name']) ?></p><p><strong>Aufbruch:</strong> <?= Utils::h((string)$active['departure_time']) ?></p><form method="post"><input type="hidden" name="action" value="end"><button type="submit">Ende</button></form><?php else: $reasons=(new ReasonRepository($db))->all(); ?><h2>Ausgang starten</h2><form method="post"><input type="hidden" name="action" value="start"><label for="reason_id">Grund / Ziel des Ausgangs</label><select id="reason_id" name="reason_id" required><?php foreach($reasons as $reason): ?><option value="<?= (int)$reason['id'] ?>"><?= Utils::h((string)$reason['name']) ?></option><?php endforeach; ?></select><button type="submit">Start</button></form><?php endif; ?></section>
<?php else: $headline='Patienten-Login'; $idLabel='Patienten-ID'; $action='/index.php'; require dirname(__DIR__) . '/templates/login.php'; endif; require dirname(__DIR__) . '/templates/footer.php';
