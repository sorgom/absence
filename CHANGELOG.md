# CHANGELOG

## v1.0.0-beta.10.5.20

### Changed
- Staff overview desktop sorting now uses the right-side sort buttons only.
- Removed table-header sorting from the staff overview.
- Sort buttons are stacked and equally wide on desktop and mobile.

### Added
- `tools/check_staff_overview_pc_sort_buttons.php`.

## v1.0.0-beta.10.5.19

### Changed
- Simplified staff overview heading to `Übersicht`.
- Removed the overview status/sort summary row and refresh button.
- Replaced the mobile sort popup with direct sort buttons in a two-column layout.

### Added
- `tools/check_staff_overview_simplified.php`.

## v1.0.0-beta.10.5.18

### Changed
- Removed additional CSS padding around the QR code.
- Enlarged the QR image inside its frame to visually reduce the white QR quiet zone.

### Added
- `tools/check_qr_code_less_padding.php`.

## v1.0.0-beta.10.5.17

### Changed
- QR code display size doubled via CSS.
- QR code padding reduced.
- Printed QR code size increased as well.

### Added
- `tools/check_qr_code_size.php`.

## v1.0.0-beta.10.5.16

### Changed
- Kept credentials page data available across browser refreshes.
- Refreshing the credentials page after password reset no longer redirects to `Person anlegen`.

### Added
- `tools/check_credentials_refresh_stays.php`.

## v1.0.0-beta.10.5.15

### Changed
- Renamed patient hamburger menu entry `Abwesenheit` to `Ausgang`.

### Added
- `tools/check_hamburger_menu_patient.php`.

## v1.0.0-beta.10.5.14

### Changed
- Removed `autofocus` from the new reason/target input on `Gründe und Ziele` to prevent smartphone keyboards from opening immediately.

### Added
- `tools/check_reasons_no_autofocus.php`.

## v1.0.0-beta.10.5.13

### Changed
- Removed success feedback messages from the outing page after starting or ending an outing.
- Removed the short-correction deletion feedback message on the outing page.
- Error feedback remains unchanged.

### Added
- `tools/check_outing_no_feedback.php`.

## v1.0.0-beta.10.5.12

### Changed
- Credentials page prints the `Zugangsdaten` heading.
- QR code is inverted in dark mode on screen.
- Credentials page hint changed to `Das Passwort muss beim nächsten Login geändert werden.`
- Removed the `Weitere Person anlegen` button from the credentials page.
- Removed the deleted-reasons explanatory hint from `Gründe und Ziele`.

### Added
- `tools/check_reasons_hint_removed.php`.

## v1.0.0-beta.10.5.11

### Added
- Staff feature `Person Passwort` for resetting passwords.
- New page `site/person_password.php`.
- New template `site/templates_person_password.php`.
- Password reset generates a new four-digit password, marks first login as required, and forwards to the credentials handout page.
- `tools/check_person_password_reset.php`.

## v1.0.0-beta.10.5.10

### Changed
- Header status LED is now a link to `/index.php`, matching the `Ausgang` menu action.

### Added
- `tools/check_status_led_link.php`.

## v1.0.0-beta.10.5.9

### Changed
- Changed the person-created page title line to `Zugangsdaten`.
- Removed the person-created success message.
- Removed explanatory one-time-display text from the person-created page.
- Removed the duplicate smaller `Zugangsdaten` heading from the credentials card.

## v1.0.0-beta.10.5.8

### Changed
- Browser tab title now always uses `app_name` from `site/app_config.php`.
- Footer now renders `{app_name} · v{version}` from configuration and version metadata.

### Added
- `tools/check_app_name_title_footer.php`.

## v1.0.0-beta.10.5.7

### Changed
- Kept the hamburger menu button border stable on focus and open state.
- Added lightly rounded corners to the hamburger menu.
- Removed the current-login line from the staff hamburger menu.
- Renamed staff menu entries to `Übersicht` and `Ausgang`.
- Moved `Ausgang` before `Passwort ändern`.

### Added
- `tools/check_hamburger_menu_personal.php`.

## v1.0.0-beta.10.5.6

### Changed
- Removed the special dark-mode border treatment from the hamburger button.
- Kept the hamburger button style stable while the menu is open.
- Strengthened the dark-mode border of the opened hamburger menu.
- Kept the improved dark-mode input field borders unchanged.

## v1.0.0-beta.10.5.5

### Changed
- Further increased dark-mode border contrast for the hamburger button.
- Further increased dark-mode contrast for input, select and textarea borders.
- Improved dark-mode card, table and menu border visibility.

## v1.0.0-beta.10.5.4

### Changed
- Increased dark-mode contrast for the hamburger menu border.
- Increased dark-mode contrast for input, select and textarea borders.

### Added
- `tools/check_dark_mode_contrast.php`.

## v1.0.0-beta.10.5.3

### Fixed
- Added the explicit `fgetcsv()` escape parameter in `tools/seed_test_data.php` to avoid PHP deprecation warnings.

### Added
- `tools/check_fgetcsv_escape.php`.

## v1.0.0-beta.10.5.2

### Removed
- Removed `tools/check_favicon.php`.

### Changed
- Removed favicon-check references from `tools/run_all_checks.php`, `tools/check_site_structure.php`, and `tools/check_repository_artifact_ignores.php`.

### Added
- `tools/check_no_favicon_test.php` to ensure the old favicon test stays removed while the favicon link remains present.

## v1.0.0-beta.10.5.1

### Fixed
- Escaped literal `$file` markers in `tools/check_repository_artifact_ignores.php` to avoid PHP warnings.

## v1.0.0-beta.10.5

### Fixed
- `tools/check_site_structure.php` now allows the intentional `site/php-qrcode` submodule directory.
- `tools/check_filenames.php` now ignores `.patch` files and the `php-qrcode` submodule.
- `tools/check_favicon.php` now accepts single-quoted and double-quoted SVG `viewBox` attributes.

### Added
- `tools/check_repository_artifact_ignores.php`.

## v1.0.0-beta.10.4

### Changed
- Person creation role selector now uses the compact, left-aligned radio-button layout.
- Person creation role selector legend changed from `Personentyp` to `Rolle`.

### Added
- `tools/check_person_create_role_ui.php`.

## v1.0.0-beta.10.3

### Fixed
- Mobile staff overview sort popup is now left-anchored below the sort button.
- Sort popup width is constrained to the viewport.

### Added
- `tools/check_sort_popup_anchor.php`.

## v1.0.0-beta.10.2

### Changed
- Staff overview delete icon now switches between `trash_b.svg` and `trash_w.svg` based on the color scheme.
- Removed the blue left border from active overview rows.

### Added
- `tools/check_overview_theme_icons.php`.

## v1.0.0-beta.10.1

### Changed
- Reduced the staff overview delete icon button size.
- Rendered the delete icon via CSS `background-image: url("/trash.svg")`.
- Removed the inline `<img>` from the delete button.

## v1.0.0-beta.10

### Added
- Staff overview filter option `Beendet`.
- Mobile sort popup for the staff overview.
- `tools/check_staff_overview_filters.php`.
- `tools/check_staff_overview_table_icons.php`.
- `tools/check_staff_overview_mobile.php`.

### Changed
- Replaced the active-only checkbox with radio buttons `Aktiv`, `Beendet`, `Alle`.
- Replaced status text with LED indicators in the overview table.
- Replaced delete text button with the `trash.svg` icon.
- Empty return fields are hidden on smartphone table cards.

## v1.0.0-beta.9

### Changed
- Initial passwords for patients and staff are now always four-digit numeric PINs.
- Printable account handout now includes the first-login password-change notice.
- Person delete role selector is more compact, left-aligned and uses the title `Rolle`.
- The current logged-in person is no longer listed as a delete option.

### Added
- `site/icon.svg` and favicon link in the HTML header.
- `tools/check_person_password_handout.php`.
- `tools/check_person_delete_role_ui.php`.
- `tools/check_favicon.php`.

## v1.0.0-beta.8

### Added
- Configurable short outing correction window via `short_absence_delete_minutes`.
- `AbsenceRepository::endOrDeleteShort()`.
- `tools/check_outing_labels.php`.
- `tools/check_short_absence_delete.php`.

### Changed
- Start page heading is now `Ausgang`.
- Start label is now `Grund / Ziel auswählen:`.
- Start button is now `Ausgang starten`.
- Active outing heading is now `Ausgang aktiv`, with `aktiv` using the active header indicator color.
- End button is now `Ausgang beenden`.

## v1.0.0-beta.7

### Changed
- Simplified the header layout: menu button left, UID right-aligned, status indicator far right.
- Removed the visible "Current login" label from the header.
- Removed the app brand link from the header.
- The status indicator is now rendered as a CSS circle.

### Added
- `tools/check_header_layout.php`.

## v1.0.0-beta.6

### Added
- `site/qr_code.php` for local QR code image generation.
- QR code image on the printable person handout.
- `tools/check_qr_code_handout.php`.

### Changed
- The printable handout now shows a real QR code instead of a placeholder.
- Filename checks now ignore the `site/php-qrcode` submodule directory.

## v1.0.0-beta.5.1

### Fixed
- Added missing `site/person_created.php` file to the patch.
- Added missing `tools/check_person_created_print.php` file to the patch.

## v1.0.0-beta.5

### Added
- `site/person_created.php` as printable account handout page after person creation.
- Print-only layout for URL, QR code placeholder, UID and initial password.
- `tools/check_person_created_print.php`.

### Fixed
- `tools/check_version_consistency.php` now follows the public repository's uppercase root documentation filenames.
- `tools/check_filenames.php` allows conventional uppercase root documentation files and ignores local runtime/submodule folders.

### Changed
- `site/person_create.php` now redirects to the handout page after successful creation.
- The initial password is no longer displayed inline on the person creation form.

## v1.0.0-beta.4

### Changed
- Rewrote `README.md` in English.
- Rewrote `release_notes_v1_0_0_beta.md` in English.
- Updated documentation to describe the current beta state instead of the historical v0.9.x migration path.
- Removed outdated documentation references to legacy patient/staff redirect files.

### Notes
- No application behavior was changed in this release.


## v1.0.0-beta.3

### Removed
- Removed remaining legacy `site/create_patient.php` file if present.
- Removed legacy references from `site/bootstrap.php`.

### Fixed
- Updated checks that still expected old patient/staff repository files.
- Extended legacy checks to fail on stale references.

### Added
- `tools/check_bootstrap_no_legacy.php`.

## v1.0.0-beta.2

### Removed
- Removed legacy repository files:
  - `site/patient_repository.php`
  - `site/staff_repository.php`

### Added
- `tools/check_no_legacy_repositories.php`.

### Changed
- Bootstrap and structure checks now expect only `site/person_repository.php`.

## v1.0.0-beta.1

### Removed
- Removed legacy compatibility entrypoints:
  - `site/patient_create.php`
  - `site/patient_delete.php`
  - `site/staff_create.php`
  - `site/staff_delete.php`

### Added
- `tools/check_no_legacy_entrypoints.php`.

### Changed
- Site structure and release checks now expect only the unified person management entrypoints.

## v1.0.0-beta

### Release
- First beta release of the absence app.
- Consolidates the v0.9.x feature set for acceptance testing and decision-maker review.

### Included
- Unified login and role-based routing.
- Shared persons data model for patients and staff.
- Absence start and return flow.
- Staff overview with sorting, active/all toggle and manual deletion.
- Person management.
- Reasons and destinations with soft-delete.
- Automatic cleanup of completed absences N hours after return time.
- Active absences are not automatically deleted.
- CSV-based test data.
- Central release validation via `tools/run_all_checks.php`.

## v0.9.9

### Added
- `tools/run_all_checks.php` as central check runner.
- `tools/check_version_consistency.php`.
- `tools/check_php_lint_all.php`.

### Changed
- Documentation now points to the central check runner for release validation.

## v0.9.8.4

### Fixed
- Fixed `tools/seed_expired_absences.php` calling a non-existing password hash method.
- Added `tools/check_cleanup_seed_runtime.php`.

## v0.9.8.3

### Changed
- Cleanup test seed now uses three explicit test persons.
- Added a clearly visible active absence whose departure time is older than N hours.
- Active old absence remains because `return_time` is `NULL`.

### Added
- `tools/show_cleanup_test_absences.php`.
- `tools/check_cleanup_test_data.php`.

## v0.9.8.2

### Fixed
- Fixed fatal error in `absence_cleanup.php`.
- Cleanup now reads `default_absence_retention_hours` via keyed `Config::get()`.
- Added `tools/check_config_usage.php`.

## v0.9.8.1

### Changed
- Automatic cleanup now uses `return_time` instead of `departure_time`.
- Active absences without return time are no longer deleted by automatic cleanup.
- Cleanup test seed now creates one expired completed absence, one fresh completed absence and one old active absence.

### Added
- `tools/check_cleanup_return_time.php`.

## v0.9.8

### Added
- Automatic cleanup of absences older than the configured retention time.
- `site/absence_cleanup.php`.
- `tools/run_absence_cleanup.php`.
- `tools/seed_expired_absences.php`.
- `tools/check_absence_cleanup.php`.

### Changed
- `index.php` and `personal.php` run absence cleanup automatically.
- The default retention remains `48` hours via `site/app_config.php`.

## v0.9.7

### Changed
- Staff login target now depends on the staff member's own active absence.
- Staff with an active absence are sent to the return page.
- Staff without an active absence are sent to the staff overview.
- First-login password change still has priority over all other targets.

### Added
- `tools/check_login_start_flow.php`.

## v0.9.6.4

### Fixed
- Reason/destination deletion now uses the design confirmation modal.
- Confirmation message includes the selected reason/destination name.
- Added `tools/check_reasons_confirm.php`.

## v0.9.6.3

### Fixed
- Restored password visibility toggle.
- Password toggle JavaScript now supports multiple markup variants.
- Added `tools/check_password_toggle.php`.

## v0.9.6.2

### Fixed
- Confirmation modal dialog is now opaque.
- Transparent backdrop and solid dialog are separated into sibling layers.
- Dialog background now has an explicit solid fallback.

## v0.9.6.1

### Fixed
- Fixed `app.js` syntax error near the old confirm handler.
- Restored hamburger dropdown behavior.
- Rebuilt `app.js` with clean, separate helper blocks.
- Added `tools/check_js_syntax.php`.

## v0.9.6

### Changed
- Replaced `window.confirm()` with a reusable design confirmation modal.
- Confirmation messages can now use dynamic values.
- Person deletion now asks e.g. `Möchten Sie Person 233 wirklich löschen?`.
- Absence deletion now includes the affected person ID where possible.

### Added
- Modal markup in `footer.php`.
- Confirmation modal CSS and JavaScript.
- `tools/check_confirm_modal.php`.

## v0.9.5.3

### Fixed
- Dropdown menu is now anchored to the hamburger button.
- Menu no longer opens on the opposite side of the header.
- Added `.menu-dropdown-anchor` wrapper.

## v0.9.5.2

### Fixed
- Replaced side drawer with a simple CSS-first dropdown overlay.
- Menu no longer uses fixed drawer/backdrop/body-scroll behavior.
- JavaScript only toggles `.is-open` and `aria-expanded`.
- Removed drawer close button from menu markup.

## v0.9.5.1

### Fixed
- Reworked hamburger overlay as CSS-first drawer.
- Removed conflicting appended overlay behavior.
- Drawer is now forced out of document flow with `position: fixed`.
- JavaScript now only toggles `is-open`, `drawer-open` and `aria-expanded`.

## v0.9.5

### Changed
- Hamburger menu now opens as a fixed overlay drawer.
- Page content is no longer pushed down when the menu opens.
- Added backdrop, close button and Escape-key handling.

### Added
- `tools/check_menu_overlay.php`.

## v0.9.4

### Added
- `personen.csv` with compact CSV-driven test data.
- `tools/seed_test_data.php` reads semicolon-separated CSV files.
- The seed script creates persons, passwords, first-login flags and absences from CSV columns.
- `tools/check_seed_test_data.php`.

## v0.9.3

### Added
- Staff overview now allows manual deletion of active and completed absences.
- Added `AbsenceRepository::deleteById()`.
- Deleting an absence triggers garbage collection for soft-deleted reasons.
- Added `tools/check_absence_manual_delete.php`.

## v0.9.2

### Changed
- `reasons` now has a `deleted` flag.
- Deleting a reason now performs a soft delete.
- New absences only offer non-deleted reasons.
- Existing absences still display deleted reasons.
- Reasons page title changed to `Gründe und Ziele`.

### Added
- Garbage collection for deleted, unreferenced reasons.
- Migration for existing databases to add `reasons.deleted`.
- `tools/check_reasons_soft_delete.php`.

## v0.9.1

### Changed
- Replaced separate patient/staff create pages with unified `person_create.php`.
- Replaced separate patient/staff delete pages with unified `person_delete.php`.
- Person create/delete forms now use radio buttons for `Patient` and `Personal`.
- Staff menu now shows `Person anlegen` and `Person löschen`.
- Old patient/staff create/delete pages remain as redirects for compatibility.

### Added
- `templates_person_create.php`.
- `templates_person_delete.php`.
- `tools/check_person_management.php`.

## v0.9.0

### Changed
- Replaced separate `patients` and `staff` tables with shared `persons` table.
- Added `persons.is_staff` flag to distinguish patient/person vs. staff navigation.
- `absences` now references `person_id` instead of `patient_id`.
- Login is now unified through `index.php`.
- Staff users are redirected to `personal.php` after login based on `is_staff`.
- Staff can open `index.php` via menu item "Abwesenheit" to start/end their own absence.
- Password change no longer asks for the old password.

### Added
- Migration logic in `tools/init_database.php` for v0.8.x databases.
- `site/person_repository.php`.
- `tools/check_v090_schema.php`.

## v0.8.19

### Changed
- Renamed all project filenames to lowercase.
- Replaced interCaps filenames with underscore names.
- Updated all internal include paths and checks.
- `Config.php` is now `config_class.php`.
- `AppInfo.php` is now `app_info.php`.
- `PasswordService.php` is now `password_service.php`.
- `VERSION` is now `version`.

### Added
- `tools/check_filenames.php`.

## v0.8.18

### Fixed
- Fixed `tools/check_site_structure.php` on Windows.
- The check now compares actual filenames exactly instead of using `is_file('site/config.php')`, which incorrectly matches `config_class.php` on Windows.
- Tightened `tools/check_initial_seed.php` to verify all required default reasons explicitly.

## v0.8.17

### Changed
- Moved database initialization from `site/init_database.php` to `tools/init_database.php`.
- `site/` no longer contains the database initialization script.
- Initialization still creates/updates `site/database.sqlite`.
- Updated related checks and documentation.

## v0.8.16

### Fixed
- `site/init_database.php` now seeds the initial staff member `anfang` with password `anfang`.
- Default reasons are seeded during database initialization.
- Seeding is idempotent via `INSERT OR IGNORE`.

### Added
- `tools/check_initial_seed.php`.

## v0.8.15

### Fixed
- Reworked remaining internal path references for the fully flat `site/` layout.
- Fixed `site/init_database.php` to require `site/bootstrap.php`.
- Added checks for old structure references and database initialization.

### Added
- `tools/check_flat_paths.php`.
- `tools/check_init_database.php`.

### Checks
- Entrypoint and flat-path checks were tightened for the flat `site/` layout.

## v0.8.14

### Fixed
- Fixed Windows case-insensitive filename collision between `config_class.php` and `config.php`.
- Renamed flat configuration file to `app_config.php`.
- Updated `config_class.php` to load `app_config.php`.
- Added Windows filename collision checks.

## v0.8.13

### Fixed
- Fixed flat-site entrypoints to require `site/bootstrap.php` before using application classes.
- Added `tools/check_entrypoints.php` to prevent regressions where `Session::start()` runs before bootstrap.

## v0.8.12

### Fixed
- Fixed flat-site bootstrap loading order.
- `config_class.php` is now loaded before `session.php`, preventing `Class "AbsenceApp\Config" not found`.
- Added `tools/check_bootstrap.php`.

## v0.8.11

### Changed
- Added nginx runtime directories `logs` and `temp` to `.gitignore`.


## v0.8.10

### Changed
- Flattened `site/` completely.
- Removed all subdirectories from `site/`.
- Moved CSS/JS to `site/style.css` and `site/app.js`.
- Moved PHP classes, templates, config and SQL directly into `site/`.
- SQLite database path is now `site/database.sqlite`.
- Updated all include paths and tool checks.

### Note
- Template files that would collide with page entry points are prefixed with `templates_`, for example `templates_change_password.php`.

## v0.8.9

### Changed
- Moved all helper/check tools from `site/app/tools/` to top-level `tools/`.
- Removed `site/app/tools/` from the deployable `site/` tree.
