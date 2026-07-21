# Release Notes v1.0.0-beta.4

This is the first beta release line of the Absence App.

The beta release is intended for functional acceptance testing, pilot operation and decision-maker review.

## Purpose

The Absence App replaces paper-based absence lists with a simple digital workflow:

- record when a person leaves
- show who is currently absent
- record when a person returns
- keep staff informed during daily work and shift handovers
- remove completed records automatically after the configured retention time

## Main user benefit

The app reduces the operational problems caused by paper lists:

- unclear current status
- manual corrections
- search effort
- duplicate or outdated information
- missing or late return entries

## Included functionality

- shared login via `index.php`
- role-based routing for patients and staff
- mandatory password change on first login
- own absence start and return flow
- staff overview for active and completed absences
- manual deletion of absences by staff
- shared person management for patients and staff
- reason and destination management
- soft-delete for reasons and destinations
- automatic cleanup of completed absences N hours after return time
- active absences remain visible until a return is recorded
- CSV-based test data
- cleanup-specific test data
- central validation via `tools/run_all_checks.php`

## Removed legacy files

The old split patient/staff management files have been removed. The beta now uses the shared person management flow only.

Current files:

```text
site/person_create.php
site/person_delete.php
site/person_repository.php
```

Removed legacy files:

```text
site/create_patient.php
site/patient_create.php
site/patient_delete.php
site/patient_repository.php
site/staff_create.php
site/staff_delete.php
site/staff_repository.php
```

## Validation

Before committing or deploying a beta build, run:

```bash
php tools/run_all_checks.php
```

The validation suite includes checks for:

- PHP syntax
- version consistency
- flat file structure
- legacy file references
- login routing
- person management
- reason soft-delete behavior
- confirmation modal behavior
- password visibility toggle
- automatic absence cleanup
- cleanup test data

## Pilot recommendation

Use this release for a limited pilot area first.

During the pilot, collect feedback on:

- clarity of the staff overview
- ease of starting and ending absences
- handling of missed return entries
- suitability of the retention period
- practical workflow during shift handover

## Before production use

Review and decide:

- backup and restore process
- runtime database permissions
- final hosting setup
- retention policy
- operational support responsibility
- onboarding process for staff


## Update v1.0.0-beta.5

### Added
- Printable handout page after creating a new person.
- Print layout that contains only the application URL, QR code placeholder, UID and initial password.
- Static check for the printable account handout flow.

### Notes
- QR code generation is prepared as a placeholder and can be added in a later version.
- The initial password is shown only once after account creation and is not stored in plain text.


## Update v1.0.0-beta.5.1

Adds the missing `person_created.php` page and `check_person_created_print.php` check file to the Git patch.


## Update v1.0.0-beta.6

### Added
- QR code image on the printable account handout.
- `site/qr_code.php` endpoint for local QR code generation.
- `tools/check_qr_code_handout.php`.

### Notes
- QR code generation uses the `site/php-qrcode` Git submodule.
- The QR code contains only the current application URL.
- Arbitrary query data is intentionally not supported.


## Update v1.0.0-beta.7

### Changed
- Simplified the application header.
- Removed the visible "Current login" label from the header.
- Removed the app title from the header next to the menu button.
- Added a right-aligned UID and a CSS status indicator.


## Update v1.0.0-beta.8

### Changed
- Start page wording now uses "Ausgang".
- Active outing page title is "Ausgang aktiv"; the word "aktiv" uses the same color as the active header indicator.
- Start and end button labels were updated.

### Added
- Short outing correction window.
- Outings ended within the configured number of minutes are deleted without confirmation.
- Default correction window: 10 minutes.


## Update v1.0.0-beta.9

### Changed
- Initial passwords are always four-digit numeric PINs for both patients and staff.
- Printable account handout now states that the password must be changed on first login.
- Person deletion role selection now uses compact left-aligned radio buttons.
- The current logged-in person is excluded from the delete selection list.

### Added
- SVG favicon.
- Static checks for the password handout, delete role UI and favicon.


## Update v1.0.0-beta.10

### Changed
- Staff overview filter now uses radio buttons: Active, Ended and All.
- Status column now uses LED indicators instead of text.
- Delete action now uses the existing trash icon instead of a text button.
- Smartphone overview includes a sort popup.
- Empty return fields are hidden on smartphone cards.


## Update v1.0.0-beta.10.1

### Changed
- Delete action in the staff overview is now a smaller icon-only button.
- The trash icon is rendered via CSS `background-image` instead of an inline image element.


## Update v1.0.0-beta.10.2

### Changed
- Staff overview delete icon now uses `trash_b.svg` in light theme and `trash_w.svg` in dark theme.
- Removed the blue left border from active overview rows.
