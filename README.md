# Abwesenheits-App

Version **v0.4** – Patienten-Workflow plus Personalübersicht.

## Enthalten

- Patienten-Login
- Ausgang starten
- Rückkehr erfassen
- Passwort ändern inklusive Erstlogin-Zwang
- Rollenabhängige Hamburger-Menüs
- Personal-Login
- Personalübersicht mit Tabelle
- Anzeige aktive / alle Abwesenheiten
- Sortierung nach Aufbruch oder Patienten-ID
- SQLite-Datenbank
- Responsive Basislayout mit Light-/Dark-Mode

## Voraussetzungen

- PHP 8.0 oder neuer
- PHP-Erweiterungen:
  - `pdo`
  - `pdo_sqlite`

## Installation

```bash
php sql/init_database.php
```

Optionaler Testpatient:

```bash
php sql/create_patient.php patient1 1234
```

## Lokaler Testserver

```bash
php -S localhost:8080 -t public
```

Danach öffnen:

- Patientenbereich: <http://localhost:8080/>
- Personalbereich: <http://localhost:8080/personal.php>

## Initialer Personalzugang

```text
ID: anfang
Passwort: anfang
```

## Git

Empfohlener Commit:

```bash
git add .
git commit -m "feat: add staff absence overview"
git tag v0.4
```


## v0.4.2

Bugfix-Release nach v0.4.1:

- Personal-Login funktioniert wieder.
- Personal-Logout führt weiter korrekt zum Personal-Login.
- Die Personalübersicht speichert Filter und Sortierung in der Session.
- `Aktualisieren` bleibt erhalten.
