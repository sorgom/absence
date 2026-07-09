# CHANGELOG

## v0.8.5

### Fixed
- Patient login now redirects explicitly to password change on first login.
- Patient login no longer returns silently to the login form after accepting a correct PIN.
- Patient absence forms now use explicit `action="/index.php"` and CSRF fields.
- Fixed patient workflow call to `ReasonRepository::listAll()`.

### Added
- `tools/check_patient_login.php` diagnostic helper.

## v0.8.4

### Fixed
- Removed overly strict `tools/check_forms.php` warning about harmless multi-line form tags.
- Rewrote delete/reason forms with single-line opening `<form>` tags to avoid false positives.
- Version bumped to `0.8.4`.
