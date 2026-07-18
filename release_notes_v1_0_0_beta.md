# Release Notes v1.0.0-beta

Dies ist der erste Beta-Release der Abwesenheits-App.

## Zweck

Der Beta-Stand ist für fachliche Tests, interne Abnahme und die Vorbereitung einer Entscheidungsvorlage gedacht.

## Wichtigste Funktionen

- gemeinsamer Login über `index.php`
- Personenmodell für Patientinnen/Patienten und Personal
- rollenbasierte Weiterleitung nach Login
- initiale Passwortänderung
- Abwesenheit starten und Rückkehr erfassen
- Personalübersicht
- manuelles Löschen von Abwesenheiten
- Gründe und Ziele verwalten
- Soft-Delete für Gründe und Ziele
- automatische Löschung abgeschlossener Abwesenheiten nach Rückkehrzeit
- aktive Abwesenheiten bleiben erhalten
- Testdaten-Import und Cleanup-Testdaten
- zentraler Check-Runner

## Validierung

Vor Commit oder Deployment:

```bash
php tools/run_all_checks.php
```

## Beta-Hinweis

Vor produktivem Betrieb sollten insbesondere geprüft werden:

- Backup/Restore der SQLite-Datenbank
- Schreibrechte des Webservers
- finaler HTTPS-/Reverse-Proxy-Betrieb
- organisatorische Aufbewahrungsfristen
- Rollen- und Benutzerverwaltung im Echtbetrieb


## Nachtrag v1.0.0-beta.1

Alte Übergangsdateien für getrennte Patienten-/Personalverwaltung wurden entfernt.
Die Verwaltung läuft nun ausschließlich über `person_create.php` und `person_delete.php`.


## Nachtrag v1.0.0-beta.2

Alte Repository-Dateien für getrennte Patienten-/Personalverwaltung wurden entfernt.
Die gemeinsame Personenverwaltung läuft ausschließlich über `person_repository.php`.


## Nachtrag v1.0.0-beta.3

Verbleibende Altlasten und alte Bootstrap-Referenzen wurden bereinigt.
