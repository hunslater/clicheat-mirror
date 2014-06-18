<?php
/**
 * ClickHeat : Fichier de configuration principal / Main configuration file
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

/**
 * Répertoire de sauvegarde des fichiers de suivi :
 * - peut être relatif ou absolu, mais DOIT finir par un slash `/`
 * - contient les fichiers générés par les appels du javascript
 * - peut être invisible des visiteurs (un répertoire non accessible par http://..../logs/)
 * 
 * Tracking-logs path :
 * - may be relative or absolute, but MUST end with a slash `/`
 * - contains logs created by javascript calls
 * - may be hidden to the web users (a directory not avalaible using http://..../logs/)
**/
define('CLICKHEAT_LOGPATH', './logs/');

/**
 * Mot de passe permettant d'accéder aux statistiques
 * 
 * Password required for statistics
**/
define('CLICKHEAT_PASSWORD', 'demo');

/**
 * Tailles d'écran (pour regroupement des clics), la valeur maximale définit la taille maximum d'écran qui sera affichée
 * 
 * Screen sizes (for click grouping), maximum value defines the largest screen-size that would be shown
**/
$screenSizes = array(0 /** Must start with 0 */, 640, 800, 1024, 1280, 1600);

/**
 * Navigateurs / Browsers
**/
$browsersList = array('all' => '', 'firefox' => 'Firefox', 'msie' => 'Internet Explorer', 'safari' => 'Safari', 'opera' => 'Opera', 'kmeleon' => 'K-meleon', 'unknown' => '');

/**
 * Langues / Languages
**/
$availableLanguages = array('en', 'fr');

/**
 * Paramètres du PNG : antialias, limite mémoire...
 * 
 * PNG parameters : antialias, memory limit...
**/
define('CLICKHEAT_MEMORY', 0); /** Limite mémoire de PHP, si ini_get() ne renvoie rien cette valeur sera utilisée, il faudra donc la renseigner en accord avec le php.ini si le check.php en parle / PHP memory limit, if ini_get() doesn't return anything this value will be used, so change it accordingly to the php.ini if check.php complains about it */
define('CLICKHEAT_ANTIALIAS', true); /** Antialias, nécessite PHP 5 / Antialias, PHP 5 required */
define('CLICKHEAT_LOW_COLOR', 0); /** Niveau minimal de couleur RVB / Lower RGB level of color */
define('CLICKHEAT_HIGH_COLOR', 255); /** Niveau maximal de couleur RVB / Higher RGB level of color */
define('CLICKHEAT_GREY_COLOR', 240); /** Niveau du gris (couleur du 0 clic) / Grey level (color of no-click) */
define('CLICKHEAT_ALPHA', 20); /** Niveau d'alpha (transparence) / Alpha-level (transparency) */
?>