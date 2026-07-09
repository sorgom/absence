<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/Config.php';
require dirname(__DIR__) . '/src/Session.php';
require dirname(__DIR__) . '/src/Database.php';
require dirname(__DIR__) . '/src/Auth.php';
use AbsenceApp\Auth;
use AbsenceApp\Database;
$auth = new Auth(Database::getConnection());
$auth->logout();
header('Location: /');
exit;
