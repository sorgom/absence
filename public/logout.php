<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\Session;

Session::start();

$role = Session::get('role');
$redirectTarget = $role === Auth::ROLE_STAFF ? '/personal.php' : '/index.php';

$auth = new Auth(Database::getConnection());
$auth->logout();

header('Location: ' . $redirectTarget);
exit;
