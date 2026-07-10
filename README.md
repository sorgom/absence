# Abwesenheits-App

Version **v0.9.0**

## Wichtigste Änderung in v0.9.0

Das Datenmodell wurde auf eine gemeinsame Tabelle `persons` umgestellt.

```text
persons
├── id
├── password_hash
├── is_staff
├── first_login
└── created_at
```

Patienten und Personal-Members liegen nicht mehr in getrennten Tabellen. Das Flag `is_staff` bestimmt Rolle, Navigation und Weiterleitung.

## Login

Der Einstieg erfolgt einheitlich über:

```text
site/index.php
```

Nach dem Login entscheidet `persons.is_staff`:

- `0` -> Abwesenheit / Patientenansicht
- `1` -> Personalbereich

## Datenbank initialisieren oder migrieren

```bash
php tools/init_database.php
```

Bei einer bestehenden v0.8.x-Datenbank werden `patients` und `staff` nach `persons` migriert und `absences.patient_id` wird zu `absences.person_id`.

## Lokaler Testserver

```bash
php -S localhost:8090 -t site
```

## Checks

```bash
php tools/check_site_structure.php
php tools/check_windows_filenames.php
php tools/check_filenames.php
php tools/check_flat_paths.php
php tools/check_v090_schema.php
php tools/check_forms.php
php tools/check_client_time_markup.php
php tools/check_bootstrap.php
php tools/check_entrypoints.php
php tools/check_init_database.php
php tools/check_initial_seed.php
```
