<?php
/**
 * ClickHeat : Fichier de langue : allemand / german
 *
 * @author Christoph Kielhorn - www.steuper.com
 * @author Daniel Schlicker - Design, Animation und Websoftware - www.danielschlicker.de
 * @since 05/03/2007
**/
define('LANG_TITLE', 'ClickHeat');
define('LANG_INDEX', 'Ergebnisse');
define('LANG_TOOLS', 'Werkzeuge');
define('LANG_AUTHORIZATION', 'Sorry, diese Webseite ist gesch&ouml;tzt. Bitte geben Sie das Passwort ein');
define('LANG_DELETE_LOGS', 'L&ouml;sche Protokolle, die &auml;lter sind als');
define('LANG_DELETE_LOG_DIR', 'L&ouml;sche alle Protokolle inklusive dem Protokoll-Ordner (n&uuml;tzlich, wenn Sie ClickHeat komplett l&ouml;schen m&ouml;chten)');
define('LANG_UPDATE_OLD_LOGS', 'Update der alten Protokolle (vor Version 0.9) zur Version 0.9 oder neuer, da das Format der Protokolle ge&auml;ndert wurde um die Sicherheit zu erh&ouml;hen');
define('LANG_DELETED_FILES', 'Dateien wurden gel&ouml;scht');
define('LANG_RENAMED_FILES', 'Dateien wurden umbenannt');
define('LANG_LOG_DELETED', 'Der Protokoll-Ordner wurde gel&ouml;scht');
define('LANG_LOG_NOT_DELETED', 'Der Protokoll-Ordner konnte nicht gel&ouml;scht werden');
define('LANG_DAYS', 'Tag(e)');
define('LANG_TO', 'bis');
define('LANG_SURE', 'Sicher');
define('LANG_UPDATE', 'Update');
define('LANG_SAVE', 'Speichern');
define('LANG_PAGE', 'Seite');
define('LANG_BROWSER', 'Browser');
define('LANG_ALL', 'Alle');
define('LANG_UNKNOWN', 'Andere/unbekannt');
define('LANG_DATE', 'Datum');
define('LANG_OK', 'OK');
define('LANG_FOR_PAGE', 'f&uuml;r Seite');
define('LANG_EXAMPLE_URL', 'Webseite');
define('LANG_LAYOUT_WIDTH', 'Layout Breiten : Links, Mitte, Rechts');
define('LANG_DISPLAY_WIDTH', 'Darstellungsgr&ouml;&szlig;e der Ergebnisse');
define('LANG_SCREENSIZE', 'Bildschirmgr&ouml;&szlig;e der Besucher');
define('LANG_HEATMAP', 'Zeige als W&auml;rmekarte');
define('LANG_CHECK_LATEST', 'Suche nach der neuesten verf&uuml;gbaren Version');
define('LANG_LATEST_VERSION', 'Neueste Version');
define('LANG_YOUR_VERSION', 'Ihre Version');
define('LANG_NO_CLICK_BELOW', 'No clicks recorded beneath this line');
define('LANG_ERROR_PASSWORD', 'Achtung ! Sie haben kein Passwort festgelegt oder das vorgegebene nicht ge&auml;ntert, sodass jeder Zugriff auf diese Seite hat.');
define('LANG_ERROR_PAGE', 'Unbekannte Seite');
define('LANG_ERROR_DATA', 'Keine Aufzeichnung f&uuml;r die gew&auml;hlte Periode');
define('LANG_ERROR_FILE', 'Protokolldatei kann nicht ge&ouml;ffnet werden');
define('LANG_ERROR_MEMORY', 'Speicherlimit (Memory limit) ist per ini_get() nicht verf&uuml;gbar. Bitte in config.php eintragen.');
define('LANG_ERROR_PNG', 'Diese PNG-Datei wurde nicht generiert');
define('LANG_ERROR_LOADING', 'Erzeuge Grafik, bitte warten...');
define('LANG_ERROR_DIRECTORY', 'Protokoll-Ordner existiert nicht => bitte zuerst das <a href="check.php">check.php Script</a> aufrufen');
define('LANG_ERROR_FIXED', 'Alle Breiten sind fixiert, das ist nicht m&ouml;glich. Bitte stellen Sie oben eine Breite auf 0.');
define('LANG_ERROR_TODAY', 'Die Daten des heutigen Tages sind in der Demo nicht abrufbar, da diese nicht gecached werden. Daher k&ouml;nnten sonst einige Leute den Server &uuml;berlasten.');
define('LANG_CHECKS', 'Haupt-&Uuml;berpr&uuml;fung f&uuml;r ClickHeat');
define('LANG_CHECKS_TO_BE', 'Alle Zeilen m&uuml;ssen OK anzeigen damit das Script ordnungsgem&auml;&szlig; funktioniert (au&szlig;er "Ihr System")');
define('LANG_CHECK_SYSTEM', 'Ihr System (nur zur Information)');
define('LANG_CHECK_LOGPATH', 'Pr&uuml;fe Protokoll-Pfad');
define('LANG_CHECK_LOGPATH_DIR', 'Der Ordner kann nicht angelegt werden. Bitte versuchen Sie ihn selbst manuell anzulegen ('.CLICKHEAT_LOGPATH.')');
define('LANG_CHECK_LOGPATH_MKDIR', 'Unterordner kann nicht angelegt werden. Bitte &uuml;berpr&uuml;fen Sie die Ordnerrechte des Ordners \'logs\ (muss schreibbar f&uuml;r den Apache Benutzer sein [CHMOD 0777]).');
define('LANG_CHECK_LOGPATH_TOUCH', 'Eine Datei in einem Unterordner kann nicht bearbeitet werden. (Das sollte nicht auftreten)');
define('LANG_CHECK_MEMORY', 'Speicher Limit');
define('LANG_CHECK_MEMORY_BAD', 'ini_get() ist nicht verf&uuml;gbar und kein Ganzzahl-Wert ist in the config.php definiert. Bitte geben Sie einen Wert f&uuml;r CLICKHEAT_MEMORY an, der dem in der php.ini entspricht (das Format sind Mega-Bytes, also wenn das \'memory_limit\' in der php.ini einen Wert von \'8M\' besitzt, benutze bitte den Ganzzahl-Wert \'8\')');
define('LANG_CHECK_MEMORY_INT', 'CLICKHEAT_MEMORY Wert, der in der config.php definiert ist, muss eine Ganzzahl ohne Anf&uuml;hrungszeichen sein. Danke.');
define('LANG_CHECK_GD', 'GD Grafik-Bibliothek');
define('LANG_CHECK_GD_IMG', 'imagecreatetruecolor() ist nicht verf&uml;gbar. Grafiken mit guter Qualit&auml;t k&ouml;nnen daher nicht erzeugt werden.');
define('LANG_CHECK_GD_ALPHA', 'imagecolorallocatealpha() ist nicht verf&uml;gbar. Transparente Grafiken k&ouml;nnen daher nicht erzeugt werden. (Das k&ouml;nnen Sie ignorieren, aber Transparenz wird unbedingt empfohlen)');
define('LANG_CHECK_GD_PNG', 'imagepng() unavailable, can\'t create PNG images, sorry');
define('LANG_CHECK_OK', 'OK');
$__jsHelp = array(
	'layout' => 'Seiten-Layout : 0 = automatische Breite, andernfalls entspricht der Wert der Spaltenbreite in Pixeln.<br />Beispiele : Fixes 100 Pixel breites Men&uuml; links, der Content f&uuml;llt den restlichen Bereich aus : 100 0 0<br />Content 750 Pixel breit, zentriert (mit einem optionalen Men&uuml; innerhalb der 750 Pixel) : 0 750 0<br />Links fixiertes 100 Pixel breites Men&uuml;, Content 650 Pixel breit und ebenfalls links ausgerichtet : 100 650 0 or 750 0 0 (da alles links ist)<br />Links und rechts Men&uuml;s mit der Breite von 100 Pixeln, zentrierter Content nutzt den verbleibenden Platz : 100 0 100<br />100% Content : 0 0 0<br /><br />Wenn Ihr Content nicht fixiert ist, sollten Sie dieselbe Darstellungsgr&ouml;&szlig;e der Ergebnisse w&auml;hlen wie die Bildschirmgr&ouml;&szlig;e der Besucher. Dann sind die Klicks in einer guten Position.',
	'page' => 'Dies ist der Tag, den Sie beim installieren des Javascript-Codes angegeben haben : initClickheat(\'seite\');',
	'date' => 'W&auml;hlen Sie das Auswertungsdatum, Format ist JJJJ-MM-TT. Wenn Sie wenige Klicks haben, ist es unter Umst&auml;nden interessant, die Ergebnisse auf mehrere Tage auszuweiten : F&uuml;llen Sie dazu das zweite Feld aus.',
	'heatmap' => 'Choose display format: only clicks position (default, very fast, left clicks are red, right clicks are green), or an heatmap (slower, only left clicks)',
	'web' => 'Geben Sie hier eine Internetadresse ein, die den links stehenden Tag \'Seite\' besitzt. Diese Seite wird dann unter der W&auml;rmekarte angezeigt. Standard-Adresse ist "../", was vermutlich Ihrer Startseite entsprechen wird... Sie k&ouml;nnen absolute Adressen (http://www.mein-seite.de/seite.html oder einfach /seite.html) or relative Adressen (../seite.html) benutzen.'
);
?>