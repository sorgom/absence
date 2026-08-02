<?php
declare(strict_types=1);

return [
    'app_name' => 'Ausgangsbuch',
    // Kept inside the document root (some hosters don't allow paths above
    // it), but the data/ subdirectory ships its own .htaccess with
    // "Require all denied", so it is unreachable over HTTP regardless of
    // the filename. The top-level .htaccess additionally blocks *.sqlite
    // anywhere in this app as a second layer of defense. Both only work if
    // your host's Apache config allows AllowOverride here — verify by
    // requesting the file's URL directly after deploying (must return 403).
    'database_path' => __DIR__ . '/data/database.sqlite',
    'session_name' => 'absence_app_session',
    'default_absence_retention_hours' => 48,
    'short_absence_delete_minutes' => 10,
    'datetime_display_format' => 'd.m.Y H:i',
    'login_max_attempts' => 5,
    'login_lockout_window_minutes' => 15,
    'password_min_length_patient' => 6,
    'password_min_length_staff' => 8,
];
