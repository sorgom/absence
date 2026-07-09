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


## v0.8.0

Neu in dieser Version:

- Passwortänderung für Patienten und Personal.
- Passwortänderung beim ersten Login wird erzwungen.
- Checkbox „Passwörter anzeigen“.
- CSRF-Schutz für zustandsändernde Formulare im Personalbereich.
- Verbesserte Fokusdarstellung für Tastaturbedienung.


## v0.8.1

Bugfix-Release:

- Fehlerhafte Formular-Attribute aus v0.8.0 korrigiert.
- CSRF-Felder manuell und valide in die Formulare eingefügt.


## v0.8.2

Dieses Release ergänzt ein Prüfskript für Formular-Markup:

```bash
php tools/check_forms.php
```

Wenn nach dem Entpacken weiterhin Requests wie

```text
/personal.php        <input type=
```

auftreten, läuft sehr wahrscheinlich noch eine alte Datei aus v0.8.0 oder ein PHP/OPcache-Prozess liefert alten Code aus.

Empfohlen:

1. Zielverzeichnis leeren, aber `.git/` behalten.
2. ZIP vollständig neu entpacken.
3. PHP/nginx bzw. PHP-CGI/FPM neu starten.
4. Prüfen:

```bash
php tools/check_forms.php
```


## v0.8.3

Bugfix-Release:

- `templates/login.php` wurde gezielt neu geschrieben.
- Das sichtbare `">` auf der Personal-Login-Seite wurde korrigiert.
- Das öffnende Login-`form`-Tag nutzt jetzt eine vorberechnete Variable `$safeAction`.
- `tools/check_forms.php` prüft zusätzliche bekannte Fehlerbilder.

Nach dem Entpacken prüfen:

```bash
php tools/check_forms.php
```


## v0.8.4

Bugfix-Release:

- `tools/check_forms.php` meldet keine harmlosen mehrzeiligen Formular-Tags mehr.
- Die betroffenen Templates wurden zusätzlich auf einzeilige öffnende `<form>`-Tags umgestellt.
- Nach dem Entpacken sollte `php tools/check_forms.php` sauber `Form markup check: OK` melden.


## v0.8.5

Bugfix-Release für den Patienten-Login:

- Nach korrekter initialer PIN wird direkt auf `/change_password.php` weitergeleitet.
- Nach späterem Login mit eigenem Passwort wird der Patientenbereich angezeigt.
- Patienten-Formulare verwenden explizit `action="/index.php"`.

Diagnose:

```bash
php tools/check_patient_login.php
php tools/check_forms.php
```
