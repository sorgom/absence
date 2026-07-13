# Abwesenheits-App

Version **v0.9.6.2**

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
