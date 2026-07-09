<?php
declare(strict_types=1);
return [
    'app_name' => 'Abwesenheits-App',
    'database_path' => dirname(__DIR__) . '/database/database.sqlite',
    'session_name' => 'absence_app_session',
    'default_absence_retention_hours' => 48,
    'datetime_display_format' => 'd.m.Y H:i',
];
