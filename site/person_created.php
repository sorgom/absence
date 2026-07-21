<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use AbsenceApp\Auth;
use AbsenceApp\Database;
use AbsenceApp\Session;
use AbsenceApp\Utils;

Session::start();

$db = Database::getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$auth->requireRole(Auth::ROLE_STAFF);

$createdPerson = Session::get('created_person');
unset($_SESSION['created_person']);

if (!is_array($createdPerson)
    || !isset($createdPerson['id'], $createdPerson['initial_password'])
) {
    header('Location: /person_create.php');
    exit;
}

$host = (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
$forwardedProto = (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
$scheme = $forwardedProto !== ''
    ? trim(explode(',', $forwardedProto)[0])
    : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');

$appUrl = $scheme . '://' . $host;
$createdType = (string) ($createdPerson['type'] ?? 'Person');
$createdId = (string) $createdPerson['id'];
$initialPassword = (string) $createdPerson['initial_password'];

$title = 'Person angelegt';
require __DIR__ . '/header.php';
?>
<section class="card person-created-card">
    <div class="screen-only">
        <h1>Person angelegt</h1>
        <p class="alert success">
            <?= Utils::h($createdType) ?> wurde erfolgreich angelegt.
        </p>
        <p>
            Diese Seite kann für die Übergabe der Zugangsdaten gedruckt werden.
            Das initiale Passwort wird danach nicht erneut angezeigt.
        </p>
    </div>

    <div class="print-card" aria-label="Zugangsdaten für neue Person">
        <h2>Zugangsdaten</h2>

        <dl class="credential-list">
            <div>
                <dt>URL</dt>
                <dd><?= Utils::h($appUrl) ?></dd>
            </div>

            <div class="qr-placeholder">
                <dt>QR-Code</dt>
                <dd>
                    <img
                        class="qr-code-image"
                        src="/qr_code.php"
                        alt="QR-Code für <?= Utils::h($appUrl) ?>"
                    >
                </dd>
            </div>

            <div>
                <dt>UID</dt>
                <dd><?= Utils::h($createdId) ?></dd>
            </div>

            <div>
                <dt>Initiales Passwort</dt>
                <dd><?= Utils::h($initialPassword) ?></dd>
            </div>

            <div class="password-change-hint">
                <dt>Hinweis</dt>
                <dd>Das Passwort muss beim ersten Login geändert werden.</dd>
            </div>
        </dl>
    </div>

    <div class="screen-actions screen-only">
        <button type="button" onclick="window.print()">Drucken</button>
        <a class="button-secondary" href="/person_create.php">Weitere Person anlegen</a>
    </div>
</section>
<?php
require __DIR__ . '/footer.php';
