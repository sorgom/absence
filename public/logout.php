<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
use AbsenceApp\Auth; use AbsenceApp\Database;
(new Auth(Database::getConnection()))->logout(); header('Location: /'); exit;
