# CHANGELOG

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
