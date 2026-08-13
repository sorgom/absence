<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$site = $root . '/site';
$errors = [];

$files = [
    'audit_log_repository.php',
    'protokoll.php',
    'templates_protokoll.php',
];

foreach ($files as $file) {
    if (!is_file($site . '/' . $file)) {
        $errors[] = "Missing protocol file: site/{$file}";
    }
}

$config = file_get_contents($site . '/app_config.php') ?: '';
$sql = file_get_contents($site . '/create_database.sql') ?: '';
$repository = file_get_contents($site . '/audit_log_repository.php') ?: '';
$cleanup = file_get_contents($site . '/absence_cleanup.php') ?: '';
$menu = file_get_contents($site . '/menu.php') ?: '';
$page = file_get_contents($site . '/protokoll.php') ?: '';
$template = file_get_contents($site . '/templates_protokoll.php') ?: '';
$bootstrap = file_get_contents($site . '/bootstrap.php') ?: '';

foreach ([
    "'protocol_retention_hours' => 48",
] as $marker) {
    if (!str_contains($config, $marker)) {
        $errors[] = "Missing protocol config marker: {$marker}";
    }
}

foreach ([
    'CREATE TABLE IF NOT EXISTS audit_logs',
    'staff_user_id TEXT NOT NULL',
    'action TEXT NOT NULL',
    'affected_element TEXT',
    'idx_audit_logs_created_at',
] as $marker) {
    if (!str_contains($sql, $marker)) {
        $errors[] = "Missing protocol SQL marker: {$marker}";
    }
}

foreach ([
    'final class AuditLogRepository',
    'ACTION_PERSON_CREATED',
    'ACTION_PERSON_DELETED',
    'ACTION_PASSWORD_RESET',
    'ACTION_REASONS_SAVED',
    'INSERT INTO audit_logs',
    'deleteExpired',
    'ORDER BY created_at DESC, id DESC',
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing protocol repository marker: {$marker}";
    }
}

foreach ([
    "Config::get('protocol_retention_hours')",
    'new AuditLogRepository($db)',
] as $marker) {
    if (!str_contains($cleanup, $marker)) {
        $errors[] = "Missing protocol cleanup marker: {$marker}";
    }
}

if (!str_contains($menu, '<a href="/person_delete.php">Person löschen</a>' . "\n        " . '<a href="/protokoll.php">Protokoll</a>')) {
    $errors[] = 'Protocol menu entry must be directly below Person löschen.';
}

foreach ([
    '$auth->requireRole(Auth::ROLE_STAFF)',
    'new AuditLogRepository($db)',
    'templates_protokoll.php',
] as $marker) {
    if (!str_contains($page, $marker)) {
        $errors[] = "Missing protocol page marker: {$marker}";
    }
}

foreach ([
    '<h1>Protokoll</h1>',
    'Datum / Uhrzeit',
    'User ID',
    'Aktion',
    'Element',
] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing protocol template marker: {$marker}";
    }
}

if (str_contains($template, 'method="post"') || str_contains($template, 'Löschen')) {
    $errors[] = 'Protocol page must not contain manual delete controls.';
}

foreach ([
    'person_create.php' => 'ACTION_PERSON_CREATED',
    'person_delete.php' => 'ACTION_PERSON_DELETED',
    'person_password.php' => 'ACTION_PASSWORD_RESET',
    'reasons.php' => 'ACTION_REASONS_SAVED',
] as $file => $marker) {
    $content = file_get_contents($site . '/' . $file) ?: '';

    if (!str_contains($content, 'new AuditLogRepository($db)') || !str_contains($content, $marker)) {
        $errors[] = "Missing protocol action marker in {$file}: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Protocol check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Protocol check: OK\n";
