<?php
declare(strict_types=1);

use AbsenceApp\Auth;
use AbsenceApp\Session;
use AbsenceApp\Utils;

$role = Session::get('role');
$userId = (string) Session::get('user_id', '');
?>
<nav class="drawer-menu" id="drawer-menu" aria-label="Menü">
    <?php if ($role === Auth::ROLE_PATIENT): ?>
        <a href="/index.php">Ausgang</a>
        <a href="/change_password.php">Passwort ändern</a>
        <a href="/logout.php">Logout</a>
    <?php elseif ($role === Auth::ROLE_STAFF): ?>
        <p class="menu-login">Aktueller Login: <?= Utils::h($userId) ?></p>
        <a href="/personal.php">Start / Übersicht</a>
        <a href="/reasons.php">Liste „Grund / Ziel des Ausgangs“ bearbeiten</a>
        <a href="/patient_create.php">Patient anlegen</a>
        <a href="/patient_delete.php">Patient löschen</a>
        <a href="/staff_create.php">Personal-Member anlegen</a>
        <a href="/staff_delete.php">Personal-Member löschen</a>
        <a href="/change_password.php">Passwort ändern</a>
        <a href="/logout.php">Logout</a>
    <?php endif; ?>
</nav>
