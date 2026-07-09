# site

Dieses Verzeichnis ist das Deployment-Verzeichnis.

Es ist bewusst komplett flach: keine Unterverzeichnisse.

Kopiere den kompletten Inhalt von `site/` auf den Webserver.

Wichtige Einstiegspunkte:

- `index.php`
- `personal.php`

Die SQLite-Datenbank wird durch `tools/init_database.php` als `database.sqlite` direkt in diesem Verzeichnis erzeugt.

Hinweis: Da die Struktur vollständig flach ist, liegen auch interne PHP-Dateien im Webroot. PHP-Dateien werden vom Server ausgeführt, nicht als Quelltext ausgeliefert, sofern PHP korrekt konfiguriert ist.


Windows-Hinweis:

Die Konfigurationswerte liegen in `app_config.php`. Der Name ist bewusst nicht `app_config.php`, damit er auf Windows nicht mit der Klassen-Datei `config_class.php` kollidiert.
