# Abwesenheits-App

Version **v0.2** – lauffähige Projektbasis mit SQLite, Login und Sessionverwaltung.

## Installation

```bash
php sql/init_database.php
php -S localhost:8080 -t public
```

- Patientenbereich: http://localhost:8080/
- Personalbereich: http://localhost:8080/personal.php

Initialer Personalzugang:

```text
ID: anfang
Passwort: anfang
```

Empfohlener Commit:

```bash
git add .
git commit -m "feat: bootstrap authentication base"
git tag v0.2
```
