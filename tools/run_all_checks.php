<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$tools = __DIR__;

$checks = [
    'check_php_lint_all.php',
    'check_version_consistency.php',
    'check_bootstrap_no_legacy.php',
    'check_no_legacy_entrypoints.php',
    'check_no_legacy_repositories.php',
    'check_site_structure.php',
    'check_windows_filenames.php',
    'check_filenames.php',
    'check_flat_paths.php',
    'check_v090_schema.php',
    'check_person_management.php',
    'check_person_created_print.php',
    'check_qr_code_handout.php',
    'check_reasons_soft_delete.php',
    'check_absence_manual_delete.php',
    'check_seed_test_data.php',
    'check_menu_overlay.php',
    'check_confirm_modal.php',
    'check_js_syntax.php',
    'check_password_toggle.php',
    'check_reasons_confirm.php',
    'check_login_start_flow.php',
    'check_absence_cleanup.php',
    'check_cleanup_return_time.php',
    'check_config_usage.php',
    'check_cleanup_test_data.php',
    'check_cleanup_seed_runtime.php',
    'check_forms.php',
    'check_client_time_markup.php',
    'check_bootstrap.php',
    'check_entrypoints.php',
    'check_init_database.php',
    'check_initial_seed.php',
];

$failed = 0;

foreach ($checks as $check) {
    $path = $tools . DIRECTORY_SEPARATOR . $check;

    if (!is_file($path)) {
        echo "[MISSING] {$check}\n";
        $failed++;
        continue;
    }

    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($path);
    $output = [];
    $exitCode = 0;
    exec($command . ' 2>&1', $output, $exitCode);

    $status = $exitCode === 0 ? 'OK' : 'FAIL';
    echo "[{$status}] {$check}\n";

    foreach ($output as $line) {
        echo "    {$line}\n";
    }

    if ($exitCode !== 0) {
        $failed++;
    }
}

if ($failed > 0) {
    fwrite(STDERR, "Checks failed: {$failed}\n");
    exit(1);
}

echo "All checks passed.\n";
