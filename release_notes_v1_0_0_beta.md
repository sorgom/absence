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


## Update v1.0.0-beta.10.3

### Fixed
- Mobile sort popup is now anchored below the sort button instead of drifting to the left.


## Update v1.0.0-beta.10.4

### Changed
- Person creation role selector now uses the same compact radio-button layout as person deletion.
- The fieldset legend is now `Rolle`.


## Update v1.0.0-beta.10.5

### Fixed
- Structure check now allows the intentional `site/php-qrcode` submodule.
- Filename check now ignores root patch files.
- Favicon check now accepts both single and double quotes around the SVG `viewBox`.


## Update v1.0.0-beta.10.5.1

### Fixed
- Removed PHP warnings from `tools/check_repository_artifact_ignores.php`.


## Update v1.0.0-beta.10.5.2

### Removed
- Removed the overly strict favicon validation check.

### Notes
- The favicon itself and the HTML favicon link remain unchanged.


## Update v1.0.0-beta.10.5.3

### Fixed
- `tools/seed_test_data.php` now passes the `fgetcsv()` escape parameter explicitly to avoid PHP deprecation warnings.

### Added
- `tools/check_fgetcsv_escape.php`.


## Update v1.0.0-beta.10.5.4

### Changed
- Improved dark-mode border contrast for the hamburger menu and form fields.
- Added explicit dark-mode border variables for menu and input field outlines.

### Added
- `tools/check_dark_mode_contrast.php`.


## Update v1.0.0-beta.10.5.5

### Changed
- Further increased dark-mode border contrast for hamburger menu, cards, tables and form fields.
- Added subtle extra outlines for the hamburger button and form fields in dark mode.


## Update v1.0.0-beta.10.5.6

### Changed
- Kept the hamburger button visually stable in open and closed state.
- Increased the dark-mode border contrast of the opened hamburger menu.
- Kept the improved dark-mode input field borders unchanged.


## Update v1.0.0-beta.10.5.7

### Changed
- Hamburger button visual state no longer changes on focus or when the menu is open.
- Hamburger menu now has lightly rounded corners.
- Staff menu no longer shows the current login line.
- Staff menu label `Start / Übersicht` changed to `Übersicht`.
- Staff menu label `Abwesenheit` changed to `Ausgang` and moved before `Passwort ändern`.

### Added
- `tools/check_hamburger_menu_personal.php`.


## Update v1.0.0-beta.10.5.8

### Changed
- Browser title now always uses the configured app name.
- Footer now uses the configured app name and the current version.

### Notes
- Page headings inside the application remain unchanged.


## Update v1.0.0-beta.10.5.9

### Changed
- Person-created page heading is now `Zugangsdaten`.
- Removed the success message and one-time-display explanatory text from the person-created page.
- Removed the duplicate smaller `Zugangsdaten` heading from the credentials card.


## Update v1.0.0-beta.10.5.10

### Changed
- Header status LED now links to the outing page (`/index.php`), matching the `Ausgang` menu entry.


## Update v1.0.0-beta.10.5.11

### Added
- Staff menu entry `Person Passwort`.
- Staff page `Passwort zurücksetzen`.
- Password reset flow creates a new initial password and forwards to the credentials handout page.
- `tools/check_person_password_reset.php`.


## Update v1.0.0-beta.10.5.12

### Changed
- Credentials page now prints the `Zugangsdaten` heading.
- QR code is inverted on screen in dark mode.
- Credentials hint now says that the password must be changed at the next login.
- Removed the `Weitere Person anlegen` button from the credentials page.
- Removed the deleted-reasons hint from `Gründe und Ziele`.

### Added
- `tools/check_reasons_hint_removed.php`.


## Update v1.0.0-beta.10.5.13

### Changed
- Removed success feedback messages from the outing page after start, return and short correction deletion.
- Error messages remain visible.


## Update v1.0.0-beta.10.5.14

### Changed
- Removed initial focus from the new reason/target input so smartphone keyboards do not open immediately on the `Gründe und Ziele` page.

### Added
- `tools/check_reasons_no_autofocus.php`.


## Update v1.0.0-beta.10.5.15

### Changed
- Patient hamburger menu label `Abwesenheit` changed to `Ausgang`.

### Added
- `tools/check_hamburger_menu_patient.php`.


## Update v1.0.0-beta.10.5.16

### Changed
- Credentials page no longer redirects to `Person anlegen` when refreshed after a password reset.
- Credentials data remains available in the session for page refreshes.

### Added
- `tools/check_credentials_refresh_stays.php`.


## Update v1.0.0-beta.10.5.17

### Changed
- Enlarged the QR code display area on the credentials page via CSS.
- Reduced QR code padding.
- Enlarged the printed QR code as well.

### Added
- `tools/check_qr_code_size.php`.


## Update v1.0.0-beta.10.5.18

### Changed
- Removed additional padding around the QR code box.
- Cropped the QR image quiet zone visually via CSS to reduce the white border.

### Added
- `tools/check_qr_code_less_padding.php`.


## Update v1.0.0-beta.10.5.19

### Changed
- Simplified staff overview heading to `Übersicht`.
- Removed overview summary line and refresh button.
- Replaced mobile sort popup with direct sort buttons in a two-column control layout.

### Added
- `tools/check_staff_overview_simplified.php`.


## Update v1.0.0-beta.10.5.20

### Changed
- Staff overview desktop sorting now uses the right-side sort buttons only.
- Removed sorting controls from the table headers.
- Right-side sort buttons are stacked and equally wide on desktop and mobile.

### Added
- `tools/check_staff_overview_pc_sort_buttons.php`.


## Update v1.0.0-beta.10.5.21

### Changed
- Staff overview mobile sort buttons now stay inside the overview card.
- Mobile sort controls use flexible width instead of the desktop fixed width.

### Added
- `tools/check_staff_overview_mobile_sort_fit.php`.


## Update v1.0.0-beta.10.5.23

### Changed
- Removed the duplicate visible `Gründe und Ziele` label above the reasons textarea.
- Kept the textarea accessible with an aria-label.

### Fixed
- Updated the password handout check to match the current role-based password generator.


## Update v1.0.0-beta.10.5.24

### Changed
- Replaced the free reason field on the outing start page with a textarea.
- Added CSS `field-sizing: content` for variable textarea height.
- Kept the outing reason textarea fixed to the form width.

### Added
- `tools/check_outing_reason_textarea.php`.


## Update v1.0.0-beta.10.5.25

### Changed
- Initial passwords now use lowercase letters except `l` and digits `2` through `9`.
- Digits `0` and `1` are no longer used for generated initial passwords.

### Added
- `tools/check_initial_password_alphabet.php`.


## Update v1.0.0-beta.10.5.26

### Added
- Staff protocol for relevant staff actions.
- Protocol page `protokoll.php`, available for staff only.
- Configurable protocol retention via `protocol_retention_hours`.
- Automatic cleanup of old protocol entries.

### Changed
- Staff actions now record protocol entries for person creation, person deletion, password reset and reasons/destinations changes.


## Update v1.0.0-beta.10.5.26.1

### Fixed
- Loaded `audit_log_repository.php` in bootstrap so protocol cleanup can use `AuditLogRepository`.
- Extended protocol check to verify bootstrap loading.


## Update v1.0.0-beta.10.5.27

### Fixed
- Removed invalid `aria-label` usage from generic `div` elements.
- Added a valid role to status-dot `span` elements that carry `aria-label`.

### Added
- `tools/check_html_aria_validity.php`.


## Update v1.0.0-beta.10.5.28

### Changed
- Header layout now shows `icon.svg` on the left, a smaller centered status LED and the hamburger menu on the right.
- Hamburger menu now shows `Login: UID` above `Ausgang`.
- Staff menu includes a subtle separator between `Protokoll` and `Login: UID`.

### Added
- `tools/check_header_menu_redesign.php`.


## Update v1.0.0-beta.10.5.29

### Changed
- Duplicate-ID error on person creation now includes the concrete UID.
- Generated initial passwords now contain as many digits as letters as possible.
- Printed credential handouts now include the configured app title and visible signet.

### Added
- `tools/check_person_create_duplicate_id.php`.


## Update v1.0.0-beta.10.5.29.1

### Fixed
- Credential handout signet and configured title are now visible only in print output.

### Added
- `tools/check_credential_print_brand.php`.


## Update v1.0.0-beta.10.5.30

### Changed
- User-visible wording changed from `Personal` to `Team`.
- User-visible wording changed from `Patient` to `Patient/-in`.

### Added
- `tools/check_wording_team_patient.php`.
