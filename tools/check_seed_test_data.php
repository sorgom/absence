<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$script = $root . '/tools/seed_test_data.php';
$csv = $root . '/personen.csv';
$errors = [];

if (!is_file($script)) {
    fwrite(STDERR, "Missing tools/seed_test_data.php\n");
    exit(1);
}

if (!is_file($csv)) {
    fwrite(STDERR, "Missing personen.csv\n");
    exit(1);
}

$content = file_get_contents($script) ?: '';

foreach ([
    'fgetcsv($handle, 0, \';\', \'"\', \'\\\\\')',
    'UID',
    'is Staff',
    'Passwort',
    'muss PW ändern',
    'Aufbruch',
    'Rückkehr',
    'INSERT INTO absences',
    'Run first: php tools/init_database.php',
] as $marker) {
    if (!str_contains($content, $marker)) {
        $errors[] = "Missing seed marker: {$marker}";
    }
}

$csvContent = file_get_contents($csv) ?: '';

if (preg_match('//u', $csvContent) !== 1) {
    if (function_exists('iconv')) {
        $converted = iconv('Windows-1252', 'UTF-8//IGNORE', $csvContent);

        if (is_string($converted)) {
            $csvContent = $converted;
        }
    } else {
        $csvContent = strtr($csvContent, [
            "\xE4" => 'ä',
            "\xF6" => 'ö',
            "\xFC" => 'ü',
            "\xC4" => 'Ä',
            "\xD6" => 'Ö',
            "\xDC" => 'Ü',
            "\xDF" => 'ß',
        ]);
    }
}

$csvContent = str_replace("\r\n", "\n", $csvContent);
$csvContent = str_replace("\r", "\n", $csvContent);
$lines = array_values(array_filter(
    explode("\n", $csvContent),
    static fn (string $line): bool => trim($line) !== ''
));

$header = $lines[0] ?? '';
$expectedHeader = 'UID;is Staff;Passwort;muss PW ändern;Aufbruch;Rückkehr';

if ($header !== $expectedHeader) {
    $errors[] = "Unexpected CSV header. Expected: {$expectedHeader}";
}

if (count($lines) < 2) {
    $errors[] = 'CSV must contain at least one data row.';
}

$hasStaff = false;
$hasPatient = false;
$hasDeparture = false;
$hasReturned = false;

foreach (array_slice($lines, 1) as $lineNumber => $line) {
    $columns = str_getcsv($line, ';', '"', '\\');

    if (count($columns) !== 6) {
        $errors[] = 'CSV data row ' . ($lineNumber + 2) . ' must have exactly 6 semicolon-separated columns.';
        continue;
    }

    [$uid, $isStaff, $password, $mustChangePassword, $departure, $return] = array_map(
        static fn (string $value): string => trim($value),
        $columns
    );

    if ($uid === '') {
        $errors[] = 'CSV data row ' . ($lineNumber + 2) . ' has an empty UID.';
    }

    if ($password === '') {
        $errors[] = 'CSV data row ' . ($lineNumber + 2) . ' has an empty password.';
    }

    if ($isStaff === 'X') {
        $hasStaff = true;
    } elseif ($isStaff === '') {
        $hasPatient = true;
    } else {
        $errors[] = 'CSV data row ' . ($lineNumber + 2) . ' has invalid is Staff marker.';
    }

    if ($mustChangePassword !== '' && $mustChangePassword !== 'X') {
        $errors[] = 'CSV data row ' . ($lineNumber + 2) . ' has invalid muss PW ändern marker.';
    }

    if ($departure === 'X') {
        $hasDeparture = true;
    } elseif ($departure !== '') {
        $errors[] = 'CSV data row ' . ($lineNumber + 2) . ' has invalid Aufbruch marker.';
    }

    if ($return === 'X') {
        $hasReturned = true;
    } elseif ($return !== '') {
        $errors[] = 'CSV data row ' . ($lineNumber + 2) . ' has invalid Rückkehr marker.';
    }
}

if (!$hasStaff) {
    $errors[] = 'CSV must contain at least one staff row.';
}

if (!$hasPatient) {
    $errors[] = 'CSV must contain at least one patient row.';
}

if (!$hasDeparture) {
    $errors[] = 'CSV must contain at least one active or completed absence row.';
}

if (!$hasReturned) {
    $errors[] = 'CSV must contain at least one completed absence row.';
}

if ($errors !== []) {
    fwrite(STDERR, "Seed test data check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Seed test data check: OK\n";
