# CHANGELOG

## v0.8.7

### Changed
- Date/time values are still stored as UTC in SQLite.
- Date/time values are now rendered as `<time datetime="...Z" data-local-time>`.
- Browser JavaScript formats times using the client's own locale and timezone.
- Server-side visible fallback remains UTC until JavaScript formats the value.

### Added
- `Utils::utcIsoDateTime()`.
- `Utils::fallbackDateTime()`.
- `Utils::localTimeElement()`.
- `tools/check_client_time_markup.php`.

## v0.8.5

### Fixed
- Patient login now redirects explicitly to password change on first login.
- Patient login no longer returns silently to the login form after accepting a correct PIN.
- Patient absence forms now use explicit `action="/index.php"` and CSRF fields.
- Fixed patient workflow call to `ReasonRepository::listAll()`.

### Added
- `tools/check_patient_login.php` diagnostic helper.
