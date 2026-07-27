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
        <a href="/index.php">Abwesenheit</a>
        <a href="/change_password.php">Passwort ändern</a>
        <a href="/logout.php">Logout</a>
    <?php elseif ($role === Auth::ROLE_STAFF): ?>
        <a href="/personal.php">Übersicht</a>
        <a href="/reasons.php">Gründe und Ziele</a>
        <a href="/person_create.php">Person anlegen</a>
        <a href="/person_password.php">Person Passwort</a>
        <a href="/person_delete.php">Person löschen</a>
        <a href="/index.php">Ausgang</a>
        <a href="/change_password.php">Passwort ändern</a>
        <a href="/logout.php">Logout</a>
    <?php endif; ?>
</nav>
