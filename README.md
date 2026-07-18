# Absence App

Version **v1.0.0-beta.6**

The Absence App replaces paper-based absence lists with a simple browser-based workflow for recording departures, returns and current absence status.

The beta release is intended for acceptance testing, pilot use and decision-maker review.

## Why this app exists

Paper lists are easy to start, but they quickly become difficult to manage in daily operations:

- the current status is not always clear
- handwriting and manual updates can cause errors
- staff may need to search or ask around for the latest information
- shift handovers depend on whether the list was updated correctly
- completed entries stay on paper until someone cleans them up manually

The app focuses on one goal: make the current absence status visible and easier to maintain.

## Core features

- shared login page for patients and staff
- role-based navigation after login
- mandatory password change on first login
- start an absence with a reason or destination
- record the return time
- staff overview with active and completed absences
- manual deletion of absences by staff
- person management for patients and staff
- reason and destination management
- soft-delete for reasons and destinations
- automatic cleanup of completed absences after a configurable retention time
- active absences are never removed automatically
- CSV-based test data for acceptance testing

## Entry points

The application uses a flat `site/` directory. The main entry points are:

```text
site/index.php         patient view, login and own absence flow
site/personal.php      staff overview
site/person_create.php create patient or staff person
site/person_delete.php delete patient or staff person
site/reasons.php       manage reasons and destinations
```

Legacy patient/staff specific entry points and repositories have been removed. Person management now uses the shared `PersonRepository` and the `persons` table.

## Login flow

Users always start at:

```text
site/index.php
```

After a successful login:

- first-login password change has priority
- patients continue to their own absence flow
- staff with an active own absence are sent to the return page
- staff without an active own absence are sent to the staff overview

## Data model overview

Patients and staff are stored in one shared table:

```text
persons
├── id
├── password_hash
├── is_staff
├── first_login
└── created_at
```

The `is_staff` flag controls role, navigation and routing.

Absences reference `person_id`, not separate patient or staff tables.

## Absence cleanup

Completed absences are automatically deleted after the configured retention time.

Default configuration in `site/app_config.php`:

```php
'default_absence_retention_hours' => 48,
```

Cleanup rule:

- active absences with `return_time IS NULL` are kept
- completed absences are deleted N hours after `return_time`

Manual cleanup test:

```bash
php tools/seed_expired_absences.php
php tools/show_cleanup_test_absences.php
php tools/run_absence_cleanup.php
php tools/show_cleanup_test_absences.php
```

Expected result:

- `cleanup_done_expired` is removed
- `cleanup_done_fresh` remains
- `cleanup_active_old` remains, even though its departure time is older than the retention time

## Test data

The repository contains a semicolon-separated test data file:

```text
personen.csv
```

Initialize the database and seed test data:

```bash
php tools/init_database.php
php tools/seed_test_data.php
```

Use a custom CSV file:

```bash
php tools/seed_test_data.php path/to/file.csv
```

Expected columns:

```text
UID;is Staff;Passwort;muss PW ändern;Aufbruch;Rückkehr
```

Column meaning:

- `is Staff = X`: person is staff
- empty `is Staff`: person is a patient
- `muss PW ändern = X`: password change is required on first login
- `Aufbruch = X`, empty `Rückkehr`: active absence
- `Aufbruch = X`, `Rückkehr = X`: completed absence
- empty `Aufbruch`: no absence

The seed script prints the created logins in the terminal.

## Local development server

```bash
php -S localhost:8090 -t site
```

Then open:

```text
http://localhost:8090
```

## Validation

Run the full validation suite before committing:

```bash
php tools/run_all_checks.php
```

Important individual checks:

```bash
php tools/check_php_lint_all.php
php tools/check_version_consistency.php
php tools/check_no_legacy_entrypoints.php
php tools/check_no_legacy_repositories.php
php tools/check_bootstrap_no_legacy.php
php tools/check_absence_cleanup.php
php tools/check_cleanup_return_time.php
php tools/check_cleanup_test_data.php
```

## Database initialization

```bash
php tools/init_database.php
```

The initialization script creates or updates the local database at:

```text
site/database.sqlite
```

The database file is local runtime data and should not be committed.

## Beta status

This beta release is ready for functional review and pilot preparation.

Before production use, review:

- operating procedures
- backup and restore process
- file permissions for the runtime database
- retention policy for completed absences
- user and role management in real operation
- final hosting setup

## Suggested decision

Start a limited beta pilot, collect feedback from daily use and decide on final production rollout after the pilot phase.


## Printable account handout

After a staff member creates a new person, the app redirects to a printable handout page.

The page contains:

- application URL
- QR code placeholder
- UID
- initial password

The print view hides navigation, buttons and layout chrome. It prints only the handout data needed for account handover, including the QR code.

The initial password is not stored in plain text. It is only passed through the session once immediately after account creation.


## QR code generation

The printable account handout includes a QR code for the application URL.

QR code generation is handled locally through the `site/php-qrcode` Git submodule and the internal endpoint:

    site/qr_code.php

The endpoint only encodes the current application URL. It does not accept arbitrary URL or text input.
