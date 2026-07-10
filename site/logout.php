<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';



use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\Session;

Session::start();

$redirectTarget = '/index.php';

$auth = new Auth(Database::getConnection());
$auth->logout();

header('Location: ' . $redirectTarget);
exit;
