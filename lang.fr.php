<?php
/**
 * ClickHeat : Fichier de langue : français
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

define('LANG_TITLE', 'ClickHeat');
define('LANG_INDEX', 'Résultats');
define('LANG_TOOLS', 'Outils');
define('LANG_AUTHORIZATION', 'D&eacute;sol&eacute;, mais cette page est prot&eacute;g&eacute;e, vous devez entrer le bon mot de passe');
define('LANG_DELETE_LOGS', 'Supprimer les enregistrements de plus de');
define('LANG_DELETE_LOG_DIR', 'Supprimer tous les enregistrements y compris le répertoire des enregistrements (utile quand vous souhaitez supprimer complètement clickheat)');
define('LANG_UPDATE_OLD_LOGS', 'Mettre à jour les anciens enregistrements (avant la version 0.9) vers la version 0.9 ou plus récente, puisque le format des enregistrements a changé pour améliorer la sécurité');
define('LANG_DELETED_FILES', 'fichiers ont été supprimés');
define('LANG_RENAMED_FILES', 'fichiers ont été renommés');
define('LANG_LOG_DELETED', 'Le répertoire des enregistrements a été supprimé');
define('LANG_LOG_NOT_DELETED', 'Le répertoire des enregistrements n\'a pas pu être supprimé');
define('LANG_DAYS', 'jour(s)');
define('LANG_SURE', 'Sûr');
define('LANG_UPDATE', 'Actualiser');
define('LANG_SAVE', 'Sauver');
define('LANG_PAGE', 'Page');
define('LANG_BROWSER', 'Navigateur');
define('LANG_ALL', 'Tous');
define('LANG_UNKNOWN', 'Autres/inconnus');
define('LANG_DATE', 'Date de début');
define('LANG_OK', 'OK');
define('LANG_FOR', 'sur');
define('LANG_FOR_PAGE', 'pour les pages');
define('LANG_EXAMPLE_URL', 'Page internet');
define('LANG_LAYOUT_WIDTH', 'Mise en page : gauche, centre, droite');
define('LANG_DISPLAY_WIDTH', 'Taille de l\'affichage ci-dessous');
define('LANG_SCREENSIZE', 'Taille d\'écran');
define('LANG_HEATMAP', 'Afficher les "températures"');
define('LANG_CHECK_LATEST', 'Vérification de la dernière version disponible');
define('LANG_LATEST_VERSION', 'Dernière version');
define('LANG_YOUR_VERSION', 'Votre version');
define('LANG_ERROR_PASSWORD', 'Attention ! Vous n\'avez pas spécifié de mot de passe ou laissé celui par défaut, donc n\'importe qui a accès à cette page');
define('LANG_ERROR_PAGE', 'Cette page est inconnue');
define('LANG_ERROR_DATA', 'Pas d\'enregistrements pour la période choisie');
define('LANG_ERROR_FILE', 'Impossible d\'ouvrir le fichier de suivi');
define('LANG_ERROR_MEMORY', 'Limite mémoire non disponible, merci de jeter un oeil à config.php');
define('LANG_ERROR_PNG', 'Ce fichier PNG n\'a pas été créé');
define('LANG_ERROR_LOADING', 'Création de l\'image en cours, patience...');
define('LANG_ERROR_DIRECTORY', 'Le répertoire des fichiers de suivi n\'existe pas => merci de lancer le <a href="check.php">script check.php</a> dans un premier temps');
define('LANG_ERROR_FIXED', 'Toutes les largeurs sont fixes, ce qui est impossible. Merci de changer une des largeurs de votre mise en page ci-dessus.');
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
define('LANG_CHECK_OK', 'OK');
$__jsHelp = array(
	'layout' => 'Mise en page du site : 0 = taille automatique, sinon c\'est la valeur en pixels de la colonne.<br />Exemples : menu gauche fixe de 100px, le contenu prend la place restante : 100 0 0<br />Contenu 750px, le tout centré (avec un menu éventuel dans les 750px) : 0 750 0<br />Menu gauche fixe 100px, contenu collé à gauche de 650px : 100 650 0 ou 750 0 0 (car tout est à gauche)<br />Menus à gauche et à droite, fixes de 100px, le contenu central prend toute la place : 100 0 100<br />Contenu à 100% : 0 0 0<br /><br />Si votre contenu principal n\'est pas fixe, vous feriez bien de sélectionner la même "Taille d\'écran" que la "Taille de l\'affichage ci-dessous" pour que les clics soient biens placés.',
	'page' => 'Il s\'agit du tag choisi lors de l\'installation du code Javascript : initClickheat(\'page\');',
	'date' => 'Choisissez ici la date à étudier, au format AAAA-MM-JJ. Si vous avez peu de données, il peut être intéressant d\'étendre l\'étude sur plusieurs jours : remplissez le 2e champ pour cela. Exemple : 2007-02-01, sur 7 jours, affichera la somme des clics enregistrés entre le 1er février et le 6 février 2007.',
	'web' => 'Indiquez ici une adresse internet qui a le marqueur correspondant à la page en cours (paramètre "Page" ci-contre), cette page sera ainsi affichée sous la carte de température. Par défaut, l\'adresse est "../" qui est normalement la racine de votre site. Vous pouvez indiquer une adresse absolue (http://www.mon-site.com/page.html ou plus simplement /page.html) ou relative (../page.html)'
);
?>