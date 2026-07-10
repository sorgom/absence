<?php
declare(strict_types=1);

// Bootstrap files are required explicitly because site/ is intentionally flat.
require_once __DIR__ . '/config_class.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/password_service.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/app_info.php';
require_once __DIR__ . '/person_repository.php';
require_once __DIR__ . '/reason_repository.php';
require_once __DIR__ . '/absence_repository.php';
require_once __DIR__ . '/patient_repository.php';
require_once __DIR__ . '/staff_repository.php';
