<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$site = $root . '/site';
$tools = $root . '/tools';

$requiredSite = [
    'index.php',
    'personal.php',
    'person_create.php',
    'person_delete.php',
    'logout.php',
    'style.css',
    'app.js',
    'bootstrap.php',
    'config_class.php',
    'app_config.php',
    'session.php',
    'person_repository.php',
    'header.php',
    'version',
];

$requiredRootTools = [
    'check_site_structure.php',
    'check_windows_filenames.php',
    'check_flat_paths.php',
    'check_forms.php',
    'check_client_time_markup.php',
    'check_bootstrap.php',
    'check_entrypoints.php',
    'check_init_database.php',
    'check_initial_seed.php',
    'check_filenames.php',
    'check_v090_schema.php',
    'check_person_management.php',
    'check_reasons_soft_delete.php',
    'check_absence_manual_delete.php',
    'init_database.php',
    'seed_test_data.php',
    'check_seed_test_data.php',
];

$errors = [];
$siteFileNames = [];
$toolFileNames = [];

if (!is_dir($site)) {
    fwrite(STDERR, "Missing site/ directory.\n");
    exit(1);
}

foreach (new DirectoryIterator($site) as $file) {
    if ($file->isDot()) {
        continue;
    }

    if ($file->isDir()) {
        $errors[] = 'site/ must be flat; subdirectory found: ' . $file->getFilename();
        continue;
    }

    $siteFileNames[] = $file->getFilename();
}

foreach (new DirectoryIterator($tools) as $file) {
    if ($file->isDot() || !$file->isFile()) {
        continue;
    }

    $toolFileNames[] = $file->getFilename();
}

foreach ($requiredSite as $file) {
    if (!in_array($file, $siteFileNames, true)) {
        $errors[] = 'Missing site/' . $file;
    }
}

foreach ($requiredRootTools as $file) {
    if (!in_array($file, $toolFileNames, true)) {
        $errors[] = 'Missing tools/' . $file;
    }
}

foreach (array_merge(['config.php', 'init_database.php'], [base64_decode('Q29uZmlnLnBocA==')]) as $forbiddenSiteFile) {
    if (in_array($forbiddenSiteFile, $siteFileNames, true)) {
        $errors[] = 'site/' . $forbiddenSiteFile . ' must not exist.';
    }
}

$caseInsensitiveNames = [];

foreach ($siteFileNames as $name) {
    $caseInsensitiveNames[strtolower($name)][] = $name;
}

foreach ($caseInsensitiveNames as $lowerName => $names) {
    $uniqueNames = array_values(array_unique($names));

    if (count($uniqueNames) > 1) {
        $errors[] = 'Case-insensitive filename collision in site/: ' . implode(', ', $uniqueNames);
    }
}

foreach (['src', 'templates', 'config', 'public'] as $oldDirectory) {
    if (is_dir($root . '/' . $oldDirectory)) {
        $errors[] = 'Old top-level directory should not exist: ' . $oldDirectory;
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Site structure check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Site structure check: OK\n";
