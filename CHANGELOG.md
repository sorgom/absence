# CHANGELOG

## v0.8.4

### Fixed
- Removed overly strict `tools/check_forms.php` warning about harmless multi-line form tags.
- Rewrote delete/reason forms with single-line opening `<form>` tags to avoid false positives.
- Version bumped to `0.8.4`.

## v0.8.3

### Fixed
- Rewrote `templates/login.php` to avoid fragile PHP output inside the form action attribute.
- Fixed visible `">` artifact on the personal login page.
- Strengthened `tools/check_forms.php` to catch suspicious opening form tags.
