<?php
declare(strict_types=1);

return [
    'app_name' => 'Abwesenheits-App',
    'database_path' => __DIR__ . '/database.sqlite',
    'session_name' => 'absence_app_session',
    'default_absence_retention_hours' => 48,
    'short_absence_delete_minutes' => 10,
    'datetime_display_format' => 'd.m.Y H:i',
];
