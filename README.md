# Abwesenheits-App

Version **v1.0.0-beta.2**

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


## Abwesenheiten manuell löschen

Personal-Members können in der Übersicht aktive und abgelaufene Abwesenheiten manuell löschen.

Zusätzlicher Check:

```bash
php tools/check_absence_manual_delete.php
```


## CSV-Testdaten

Das Projekt enthält eine Testdaten-CSV:

```text
personen.csv
```

Seed ausführen:

```bash
php tools/init_database.php
php tools/seed_test_data.php
```

Alternativ kann eine andere CSV übergeben werden:

```bash
php tools/seed_test_data.php pfad/zur/datei.csv
```

Erwartete Spalten mit Semikolon als Trennzeichen:

```text
UID;is Staff;Passwort;muss PW ändern;Aufbruch;Rückkehr
```

Bedeutung:

- `is Staff = X`: Person ist Personal
- leer bei `is Staff`: Person ist Patient
- `muss PW ändern = X`: initiale Passwortänderung erforderlich
- `Aufbruch = X`, `Rückkehr` leer: aktive Abwesenheit
- `Aufbruch = X`, `Rückkehr = X`: abgeschlossene Abwesenheit
- `Aufbruch` leer: keine Abwesenheit

Die Logins werden nach dem Seed direkt im Terminal ausgegeben.

Zusätzlicher Check:

```bash
php tools/check_seed_test_data.php
```


## Hamburger-Menü Overlay

Das Hamburger-Menü wird als Overlay geöffnet und schiebt den Seiteninhalt nicht mehr nach unten.

Zusätzlicher Check:

```bash
php tools/check_menu_overlay.php
```


## v0.9.5.2 Menü-Dropdown

Das Hamburger-Menü ist jetzt als einfaches CSS-Dropdown umgesetzt:

- `display: none` im geschlossenen Zustand
- `position: absolute` über dem Inhalt
- `.is-open` zeigt das Menü
- JavaScript schaltet nur `.is-open` und `aria-expanded`

Das entspricht dem klassischen CSS-Dropdown-Prinzip.


## v0.9.5.3 Menüposition

Das Dropdown-Menü ist jetzt an einem Wrapper um den Hamburger-Button verankert:

```html
<div class="menu-dropdown-anchor">
    <button ...>☰</button>
    <nav id="drawer-menu">...</nav>
</div>
```

Dadurch erscheint das Menü direkt am Button.


## Bestätigungsdialog

`window.confirm()` wurde durch ein eigenes Dialog-Modal ersetzt.

Formulare können eine statische Meldung verwenden:

```html
<form data-confirm-message="Möchten Sie wirklich löschen?">
```

Oder eine dynamische Meldung mit `{value}`:

```html
<form
  data-confirm-template="Möchten Sie Person {value} wirklich löschen?"
  data-confirm-value-source="#id">
```

Zusätzlicher Check:

```bash
php tools/check_confirm_modal.php
```


## v0.9.6.1 JavaScript-Fix

`site/app.js` wurde bereinigt und enthält jetzt nur noch gültige, getrennte Blöcke für:

- Passwortfelder anzeigen
- lokale Zeitanzeige
- CSS-Dropdown-Menü
- Bestätigungsmodal

Zusätzlicher Check:

```bash
php tools/check_js_syntax.php
```


## v0.9.6.2 Modal-Hintergrund

Der Bestätigungsdialog trennt jetzt Backdrop und Dialog sauber:

- transparenter Backdrop als eigene Ebene
- Dialog in eigener Panel-Ebene
- Dialog-Hintergrund explizit deckend
- keine Opacity auf dem gemeinsamen Parent


## v0.9.6.3 Passwort anzeigen

Die Checkbox „Passwort anzeigen“ wird wieder unterstützt. Der JavaScript-Code erkennt mehrere Markup-Varianten robust.


## v0.9.6.4 Gründe/Ziele-Bestätigung

Beim Löschen von Gründen/Zielen wird jetzt ebenfalls das Design-Bestätigungsmodal angezeigt.
Die Meldung enthält den Namen des Grundes/Ziels.


## v0.9.7 Login-Startverhalten

Nach dem Login gilt:

- Pflicht-Passwortwechsel hat Vorrang.
- Personal mit aktiver eigener Abwesenheit landet auf der Abwesenheits-/Rückkehrseite.
- Personal ohne aktive eigene Abwesenheit landet in der Übersicht.
- Patienten bleiben im normalen Abwesenheitsfluss.

Zusätzlicher Check:

```bash
php tools/check_login_start_flow.php
```


## v0.9.8 Automatische Bereinigung alter Abwesenheiten

Abgeschlossene Abwesenheiten werden automatisch gelöscht, wenn ihre Rückkehrzeit älter ist als die konfigurierte Aufbewahrungszeit. Aktive Abwesenheiten ohne Rückkehrzeit werden nicht automatisch gelöscht.

Standard in `site/app_config.php`:

```php
'default_absence_retention_hours' => 48,
```

Die Bereinigung läuft beim Aufruf der zentralen Seiten automatisch mit.

Manueller Test:

```bash
php tools/init_database.php
php tools/seed_expired_absences.php
php tools/run_absence_cleanup.php
```

Erwartung:
- eine alte abgeschlossene Test-Abwesenheit wird gelöscht
- eine frische abgeschlossene Test-Abwesenheit bleibt erhalten
- eine alte aktive Test-Abwesenheit bleibt erhalten

Zusätzlicher Check:

```bash
php tools/check_absence_cleanup.php
```


## v0.9.8.1 Rückkehrzeit als Cleanup-Basis

Die automatische Bereinigung verwendet jetzt `return_time` statt `departure_time`.

Regeln:

- aktive Abwesenheiten mit `return_time IS NULL` werden nicht automatisch gelöscht
- abgeschlossene Abwesenheiten werden `N` Stunden nach Rückkehr gelöscht

Zusätzlicher Check:

```bash
php tools/check_cleanup_return_time.php
```


## v0.9.8.2 Config-Zugriff

`absence_cleanup.php` liest die Aufbewahrungszeit jetzt korrekt über:

```php
Config::get('default_absence_retention_hours', 48)
```


## v0.9.8.3 Cleanup-Testdaten

Die Cleanup-Testdaten enthalten jetzt drei sprechende Testpersonen:

- `cleanup_done_expired`: abgeschlossen, Rückkehr älter als N Stunden, wird gelöscht
- `cleanup_done_fresh`: abgeschlossen, Rückkehr jünger als N Stunden, bleibt
- `cleanup_active_old`: aktiv, Aufbruch älter als N Stunden, bleibt

Testablauf:

```bash
php tools/seed_expired_absences.php
php tools/show_cleanup_test_absences.php
php tools/run_absence_cleanup.php
php tools/show_cleanup_test_absences.php
```

Nach dem Cleanup sollte `cleanup_done_expired` verschwunden sein.
`cleanup_done_fresh` und `cleanup_active_old` sollten bleiben.

Zusätzliche Checks:

```bash
php tools/check_cleanup_test_data.php
```


## v0.9.8.4 Cleanup-Seed Passwort

`tools/seed_expired_absences.php` verwendet jetzt die tatsächlich vorhandene Passwort-Hash-Methode aus `PasswordService`.


## v0.9.9 Stabilisierung

Vor dem v1.0.0-Release gibt es einen zentralen Check-Runner:

```bash
php tools/run_all_checks.php
```

Zusätzliche Stabilitätschecks:

```bash
php tools/check_php_lint_all.php
php tools/check_version_consistency.php
```

Der Check-Runner führt die vorhandenen Einzelchecks gesammelt aus und bricht bei Fehlern mit Exit-Code `1` ab.

## v1.0.0-beta Beta-Release

Dieser Stand ist der erste Beta-Release der Abwesenheits-App.

Enthaltene Kernfunktionen:

- gemeinsamer Login für Patientinnen/Patienten und Personal
- Rollensteuerung über Personen-Datensatz
- verpflichtende initiale Passwortänderung
- Abwesenheit starten und beenden
- Personalübersicht mit aktiven und abgeschlossenen Abwesenheiten
- manuelles Löschen von Abwesenheiten durch Personal
- Gründe und Ziele verwalten
- Soft-Delete für Gründe und Ziele
- automatische Bereinigung abgeschlossener Abwesenheiten N Stunden nach Rückkehr
- aktive Abwesenheiten ohne Rückkehrzeit bleiben erhalten
- Person anlegen und löschen
- CSV-basierte Testdaten
- zentrale Prüfungen über:

```bash
php tools/run_all_checks.php
```

Beta-Hinweis:

Dieser Stand ist für fachliche Abnahme, Testbetrieb und Entscheidungsvorbereitung gedacht.
Vor einem produktiven Einsatz sollten reale Betriebsparameter, Backup-Verfahren und Hosting-Konfiguration final geprüft werden.

## v1.0.0-beta.1 Bereinigung alter Einstiegspunkte

Die alten Übergangsdateien aus der Umstellung auf die gemeinsame Personenverwaltung wurden entfernt:

- `site/patient_create.php`
- `site/patient_delete.php`
- `site/staff_create.php`
- `site/staff_delete.php`

Aktuelle Einstiegspunkte sind:

- `site/person_create.php`
- `site/person_delete.php`

Zusätzlicher Check:

```bash
php tools/check_no_legacy_entrypoints.php
```

## v1.0.0-beta.2 Repository-Bereinigung

Die alten Repository-Dateien aus der getrennten Patienten-/Personalverwaltung wurden entfernt:

- `site/patient_repository.php`
- `site/staff_repository.php`

Die gemeinsame Personenverwaltung läuft über:

- `site/person_repository.php`

Zusätzlicher Check:

```bash
php tools/check_no_legacy_repositories.php
```

