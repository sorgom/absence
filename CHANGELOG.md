# CHANGELOG

## v0.5.0

### Added
- Staff-side patient creation.
- Automatic four-digit initial PIN generation for new patients.
- Staff-side patient deletion.
- Confirmation dialog before deleting patients.
- `PatientRepository` for patient management.

### Changed
- Version bumped to `0.5.0`.

## v0.4.3

### Added
- Central `VERSION` file.
- `AbsenceApp\AppInfo` helper for application metadata.
- Optional Git pre-commit hook in `tools/pre-commit`.

### Changed
- `templates/footer.php` now reads the version dynamically from `VERSION`.

## v0.4.2

### Fixed
- Restored staff login after the v0.4.1 regression.
- Kept staff logout redirecting to the personal login.
- Restored the central bootstrap loading path from v0.4.

### Changed
- Staff overview keeps filter and sorting in the session.
- Removed redundant `Anzeigen` button.
- Kept explicit `Aktualisieren` button.
- Removed duplicate `Aktueller Login` line from the overview.

### Added
- Ascending and descending sorting in the staff overview.
