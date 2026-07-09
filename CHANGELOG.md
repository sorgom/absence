# CHANGELOG

## v0.6.0

### Added
- Staff-side creation of Personal-Members.
- Automatic initial password generation for new Personal-Members.
- Staff-side deletion of Personal-Members.
- Safety guard preventing deletion of the currently logged-in account.
- `StaffRepository` for staff management.

### Changed
- Version bumped to `0.6.0`.
- Personal-Member menu entries are now functional.

## v0.5.1

### Fixed
- Patient management pages are now explicitly linked in the staff hamburger menu.
- Ensured the shared menu component is included from the header.
- Added robust hamburger toggle handling.

## v0.5.0

### Added
- Staff-side patient creation.
- Automatic four-digit initial PIN generation for new patients.
- Staff-side patient deletion.
- Confirmation dialog before deleting patients.
- `PatientRepository` for patient management.
