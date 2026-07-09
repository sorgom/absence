# Abwesenheits-App

Version **v0.3** – Patientenbereich mit Ausgang starten/beenden und Passwortänderung.

## Enthalten

- SQLite-Datenbank und Initialisierung
- Personal-Login (`anfang` / `anfang`)
- Patienten-Login-Grundlage
- Patientenbereich: Ausgang starten
- Patientenbereich: Rückkehr erfassen
- Passwortänderung für Patient und Personal
- Erstlogin-Erkennung mit erzwungener Passwortänderung
- Responsive Layout mit Light-/Dark-Mode

## Installation

```bash
php sql/init_database.php
```

## Optionalen Testpatienten anlegen

Da die Personal-Benutzerverwaltung erst in einem späteren Sprint kommt, kann zum Testen ein Patient per CLI erzeugt werden:

```bash
php sql/create_patient.php patient1 1234
```

## Lokaler Testserver

```bash
php -S localhost:8080 -t public
```

- Patientenbereich: <http://localhost:8080/>
- Personalbereich: <http://localhost:8080/personal.php>

## Initialer Personalzugang

```text
ID: anfang
Passwort: anfang
```

## Git

```bash
git add .
git commit -m "feat: implement patient absence workflow"
git tag v0.3
```
