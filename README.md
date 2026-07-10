# Abwesenheits-App

Version **v0.9.2**

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


## Personenverwaltung

Im Personalbereich gibt es jetzt gemeinsame Seiten:

```text
site/person_create.php
site/person_delete.php
```

Dort wird per Radio-Button zwischen `Patient` und `Personal` gewählt.

Die alten Seiten bleiben als Weiterleitungen erhalten:

```text
patient_create.php -> person_create.php
staff_create.php   -> person_create.php
patient_delete.php -> person_delete.php?person_type=patient
staff_delete.php   -> person_delete.php?person_type=staff
```

Zusätzlicher Check:

```bash
php tools/check_person_management.php
```


## Soft-Delete für Gründe und Ziele

`reasons` enthält jetzt ein `deleted`-Flag.

- Neue Abwesenheiten zeigen nur Gründe mit `deleted = 0`.
- Beim Löschen wird ein Grund nur ausgeblendet.
- Bestehende Abwesenheiten zeigen den alten Grund weiterhin.
- Nicht mehr referenzierte gelöschte Gründe werden durch Garbage Collection entfernt.

Zusätzlicher Check:

```bash
php tools/check_reasons_soft_delete.php
```
