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
    'check_person_create_duplicate_id.php',
    'check_person_created_print.php',
    'check_credential_print_brand.php',
    'check_credentials_refresh_stays.php',
    'check_repository_artifact_ignores.php',
    'check_no_favicon_test.php',
    'check_person_delete_role_ui.php',
    'check_person_create_role_ui.php',
    'check_wording_team_patient.php',
    'check_person_password_handout.php',
    'check_initial_password_alphabet.php',
    'check_person_password_reset.php',
    'check_protocol.php',
    'check_qr_code_handout.php',
    'check_qr_code_size.php',
    'check_qr_code_less_padding.php',
    'check_start_reason_text.php',
    'check_reasons_text_editor.php',
    'check_absence_reason_denormalized.php',
    'check_absence_manual_delete.php',
    'check_staff_overview_mobile.php',
    'check_staff_overview_simplified.php',
    'check_staff_overview_pc_sort_buttons.php',
    'check_staff_overview_mobile_sort_fit.php',
    'check_sort_popup_anchor.php',
    'check_staff_overview_table_icons.php',
    'check_overview_theme_icons.php',
    'check_staff_overview_filters.php',
    'check_seed_test_data.php',
    'check_fgetcsv_escape.php',
    'check_menu_overlay.php',
    'check_hamburger_menu_personal.php',
    'check_hamburger_menu_patient.php',
    'check_confirm_modal.php',
    'check_js_syntax.php',
    'check_password_toggle.php',
    'check_login_start_flow.php',
    'check_short_absence_delete.php',
    'check_outing_labels.php',
    'check_outing_no_feedback.php',
    'check_outing_reason_textarea.php',
    'check_absence_cleanup.php',
    'check_cleanup_return_time.php',
    'check_config_usage.php',
    'check_cleanup_test_data.php',
    'check_cleanup_seed_runtime.php',
    'check_forms.php',
    'check_header_layout.php',
    'check_header_menu_redesign.php',
    'check_html_aria_validity.php',
    'check_status_led_link.php',
    'check_app_name_title_footer.php',
    'check_dark_mode_contrast.php',
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
