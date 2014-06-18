<?php
/**
 * ClickHeat : Fichier de langue : français
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

define('LANG_TITLE', 'ClickHeat : visualisation et analyse des clics');
define('LANG_H1', 'ClickHeat : analyse des clics');
define('LANG_AUTHORIZATION', 'Désolé, mais cette page est protégée, vous devez entrer le bon mot de passe');
define('LANG_DELETE_LOGS', 'Supprimer les enregistrements de plus de');
define('LANG_DAYS', 'jour(s)');
define('LANG_SURE', 'Sûr');
define('LANG_UPDATE', 'Actualiser');
define('LANG_SAVE', 'Sauver');
define('LANG_PAGE', 'Page');
define('LANG_BROWSER', 'Navigateur');
define('LANG_ALL', 'Tous');
define('LANG_UNKNOWN', 'Autres/inconnus');
define('LANG_DATE', 'Date de début');
define('LANG_FOR', 'pour');
define('LANG_EXAMPLE_URL', 'Page internet <small>(affichée sous l\'image des clics, absolue ou relative)</small>');
define('LANG_DISPLAY_WIDTH', 'Taille de l\'affichage ci-dessous');
define('LANG_SCREENSIZE', 'Taille d\'écran');
define('LANG_HEATMAP', 'Afficher les "températures"');
define('LANG_ERROR_PASSWORD', 'Attention ! Vous n\'avez pas spécifié de mot de passe ou laissé celui par défaut, donc n\'importe qui a accès à cette page');
define('LANG_ERROR_PAGE', 'Cette page est inconnue');
define('LANG_ERROR_DATA', 'Pas d\'enregistrements pour la période choisie');
define('LANG_ERROR_FILE', 'Impossible d\'ouvrir le fichier de suivi');
define('LANG_ERROR_MEMORY', 'Limite mémoire non disponible, merci de jeter un oeil à config.php');
define('LANG_ERROR_PNG', 'Ce fichier PNG n\'a pas été créé');
define('LANG_ERROR_LOADING', 'Création de l\'image en cours, patience...');
define('LANG_ERROR_DIRECTORY', 'Le répertoire des fichiers de suivi n\'existe pas => merci de lancer le <a href="check.php">script check.php</a> dans un premier temps');
define('LANG_CHECKS', 'Vérifications principales pour ClickHeat');
define('LANG_CHECKS_TO_BE', 'Tous les champs doivent afficher OK pour que le programme fonctionne bien (sauf "Votre système")');
define('LANG_CHECK_SYSTEM', 'Votre système (pour information)');
define('LANG_CHECK_LOGPATH', 'Vérification du répertoire des fichiers de suivi');
define('LANG_CHECK_LOGPATH_DIR', 'impossible de créer le répertoire, merci d\'essayer de le créer vous-mêmes ('.CLICKHEAT_LOGPATH.')');
define('LANG_CHECK_LOGPATH_MKDIR', 'impossible de créer un sous-répertoire, merci de vérifier les permissions du répertoire (il doit avoir les droits d\'écriture pour l\'utilisateur Apache)');
define('LANG_CHECK_LOGPATH_TOUCH', 'impossible de créer un fichier dans le sous-répertoire (ceci ne devrait pas arriver normalement)');
define('LANG_CHECK_MEMORY', 'Limite mémoire');
define('LANG_CHECK_MEMORY_BAD', 'ini_get() n\'est pas disponible, et aucune valeur (entière/integer) n\'est définie dans le config.php. Merci de remplir la valeur de CLICKHEAT_MEMORY en accord avec le php.ini (le format à utiliser est le mégaoctet, donc si la valeur \'memory_limit\' du php.ini est \'8M\', merci de mettre la valeur entière 8)');
define('LANG_CHECK_MEMORY_INT', 'la valeur de CLICKHEAT_MEMORY définie dans config.php doit être entière (nombre sans guillemets), merci.');
define('LANG_CHECK_GD', 'Librairie graphique GD');
define('LANG_CHECK_GD_NA', 'GD n\'est pas installé');
define('LANG_CHECK_GD_IMG', 'imagecreatetruecolor() non disponible, impossible de créer des images (de bonne qualité)');
define('LANG_CHECK_GD_ALPHA', 'imagecolorallocatealpha() non disponible, impossible de créer des images transparentes (vous pouvez vous en passer, mais c\'est fortement recommandé)');
define('LANG_CHECK_GD_ANTIALIAS', 'imagefilter() non disponible (nécessite PHP 5+), impossible d\'appliquer un effet d\'antialias/fondu (vous pouvez vous en passer sans souci)');
define('LANG_CHECK_OK', 'OK');
?>