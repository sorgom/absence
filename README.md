# Abwesenheits-App

Version **v0.8.19**

## Dateinamen-Konvention

Alle Dateinamen sind klein geschrieben. CamelCase-Dateinamen wurden auf underscore umgestellt.

Beispiele:

```text
Config.php             -> config_class.php
AppInfo.php            -> app_info.php
PasswordService.php    -> password_service.php
AbsenceRepository.php  -> absence_repository.php
README_SITE.md         -> readme_site.md
VERSION                -> version
```

Die PHP-Klassennamen bleiben unverändert; geändert wurden nur die Dateinamen und internen Pfade.

## Deployment-Struktur

`site/` ist komplett flach und enthält keine Unterverzeichnisse.

```text
site/
├── index.php
├── personal.php
├── logout.php
├── style.css
├── app.js
├── bootstrap.php
├── config_class.php
├── app_config.php
├── database.php
├── session.php
├── auth.php
└── ...
```

Alles, was auf den Webserver kopiert werden muss, liegt direkt in `site/`.

Die Tools liegen außerhalb:

```text
tools/
```

## Datenbank initialisieren

```bash
php tools/init_database.php
```

Die SQLite-Datenbank wird erzeugt als:

```text
site/database.sqlite
```

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
php tools/check_forms.php
php tools/check_client_time_markup.php
php tools/check_bootstrap.php
php tools/check_entrypoints.php
php tools/check_init_database.php
php tools/check_initial_seed.php
```
