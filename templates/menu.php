<?php
/**
 * Role-aware hamburger menu.
 *
 * The menu follows the specification: patients and staff members see
 * different navigation entries because their workflows are different.
 */
declare(strict_types=1);

use AbsenceApp\Auth;
use AbsenceApp\Session;
use AbsenceApp\Utils;

$role = Session::get('role');
$userId = (string) Session::get('user_id', '');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (!Session::has('user_id') || !is_string($role)) {
    return;
}

/**
 * Marks the current menu item for screen readers and styling.
 */
function menuCurrent(string $href, string $currentPath): string
{
    return $href === $currentPath ? ' aria-current="page"' : '';
}
?>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="app-menu">
    <span class="menu-icon" aria-hidden="true">☰</span>
    <span class="visually-hidden">Menü öffnen</span>
</button>

<nav id="app-menu" class="app-menu" aria-label="Hauptmenü" hidden>
    <div class="menu-header">
        <strong>
            <?= $role === Auth::ROLE_STAFF ? 'Personal' : 'Patient' ?>:
            <?= Utils::h($userId) ?>
        </strong>
    </div>

    <?php if ($role === Auth::ROLE_PATIENT): ?>
        <a href="/index.php"<?= menuCurrent('/index.php', $currentPath) ?>>Ausgang</a>
        <a href="/change_password.php"<?= menuCurrent('/change_password.php', $currentPath) ?>>Passwort ändern</a>
        <a href="/logout.php">Logout</a>
    <?php elseif ($role === Auth::ROLE_STAFF): ?>
        <a href="/personal.php"<?= menuCurrent('/personal.php', $currentPath) ?>>Start / Übersicht</a>
        <a href="/staff/reasons.php" aria-disabled="true">Liste „Grund / Ziel“ bearbeiten</a>
        <a href="/staff/patient_create.php" aria-disabled="true">Patient anlegen</a>
        <a href="/staff/patient_delete.php" aria-disabled="true">Patient löschen</a>
        <a href="/staff/staff_create.php" aria-disabled="true">Personal-Member anlegen</a>
        <a href="/staff/staff_delete.php" aria-disabled="true">Personal-Member löschen</a>
        <a href="/change_password.php"<?= menuCurrent('/change_password.php', $currentPath) ?>>Passwort ändern</a>
        <a href="/logout.php">Logout</a>
    <?php endif; ?>
</nav>
