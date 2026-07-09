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


## Versionierung

Die Anwendung liest ihre Versionsnummer aus der Datei:

```text
VERSION
```

Der Footer zeigt diese Version automatisch an. `templates/footer.php` muss daher nicht mehr pro Release manuell angepasst werden.

Optional kann ein Git-Pre-Commit-Hook installiert werden:

```bash
cp tools/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit
```

Der Hook prüft, ob die `VERSION`-Datei vorhanden ist und der Footer die Version dynamisch liest.

## v0.4.3

- Zentrale `VERSION`-Datei ergänzt.
- Footer zeigt die Version dynamisch an.
- Optionales Git-Pre-Commit-Skript ergänzt.


## v0.5.0

Neu in dieser Version:

- Patienten im Personalbereich anlegen.
- Vierstellige initiale PIN wird automatisch generiert und einmal angezeigt.
- Patienten im Personalbereich löschen.
- Löschvorgang mit Bestätigungsdialog.
- Patientenverwaltung über das Personal-Hamburger-Menü erreichbar.


## v0.5.1

Bugfix-Release:

- Patientenverwaltung ist im Personal-Hamburger-Menü verlinkt.
- Gemeinsame Menü-Komponente wird zuverlässig im Header eingebunden.


## v0.6.0

Neu in dieser Version:

- Personal-Member im Personalbereich anlegen.
- Initiales Passwort automatisch generieren und einmal anzeigen.
- Personal-Member löschen.
- Eigener Account kann nicht gelöscht werden.
- Personalverwaltung über das Personal-Hamburger-Menü erreichbar.


## v0.7.0

Neu in dieser Version:

- Liste „Grund / Ziel des Ausgangs“ im Personalbereich bearbeiten.
- Neue Gründe / Ziele hinzufügen.
- Vorhandene Gründe / Ziele löschen.
- Einträge, die bereits von Abwesenheiten verwendet werden, werden vor Löschung geschützt.
