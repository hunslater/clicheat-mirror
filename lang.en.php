<?php
/**
 * ClickHeat : Fichier de langue : français
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

define('LANG_TITLE', 'ClickHeat : what\'s hot on your webpage ?');
define('LANG_H1', 'ClickHeat : what\'s hot on your webpage ?');
define('LANG_AUTHORIZATION', 'Sorry, this webpage is protected, please provide the good password.');
define('LANG_DELETE_LOGS', 'Delete logs older than');
define('LANG_DAYS', 'days');
define('LANG_SURE', 'Sure');
define('LANG_UPDATE', 'Update');
define('LANG_SAVE', 'Save');
define('LANG_PAGE', 'Page');
define('LANG_BROWSER', 'Browser');
define('LANG_ALL', 'All');
define('LANG_UNKNOWN', 'Other/unknown');
define('LANG_DATE', 'Date (YYYY-MM-DD)');
define('LANG_EXAMPLE_URL', 'Webpage <small>(shown under heatmap, absolute or relative)</small>');
define('LANG_DISPLAY_WIDTH', 'Results display size');
define('LANG_SCREENSIZE', 'Screen sizes');
define('LANG_ERROR_PASSWORD', 'Beware ! You didn\'t speficied any password, so anyone has access to this page');
define('LANG_ERROR_PAGE', 'Unknown page');
define('LANG_ERROR_DATA', 'No logs for this date');
define('LANG_ERROR_FILE', 'Can\'t open log file');
define('LANG_ERROR_MEMORY', 'Memory limit not available in ini_get(), please look at config.php');
define('LANG_ERROR_PNG', 'This PNG file has not been created');
define('LANG_ERROR_LOADING', 'Generating image, please wait...');
define('LANG_ERROR_DIRECTORY', 'Logging directory doesn\'t exist => please run the <a href="check.php">check.php script</a> first');
define('LANG_CHECKS', 'Main checks for ClickHeat');
define('LANG_CHECKS_TO_BE', 'All rows must display OK so the script works well (except "Your system")');
define('LANG_CHECK_SYSTEM', 'Your system (just for information)');
define('LANG_CHECK_LOGPATH', 'Checking logs\' path');
define('LANG_CHECK_LOGPATH_DIR', 'can\'t create the directory, please try to create it yourself ('.CLICKHEAT_LOGPATH.')');
define('LANG_CHECK_LOGPATH_CHMOD', 'can\'t change the permissions of the directory (chmod), please check your permissions on it, or delete the directory and recreate it by running this script again ('.CLICKHEAT_LOGPATH.')');
define('LANG_CHECK_LOGPATH_MKDIR', 'can\'t create a new sub-directory, please check permissions of the directory (must be writeable to the Apache user)');
define('LANG_CHECK_LOGPATH_TOUCH', 'can\'t touch a file in the sub-directory (that should not occur)');
define('LANG_CHECK_MEMORY', 'Memory limit');
define('LANG_CHECK_MEMORY_BAD', 'ini_get() is not available, and no (integer) value is defined in the config.php. Please fill the CLICKHEAT_MEMORY value according to php.ini (format to use is mega-bytes, so if php.ini\'s \'memory_limit\' value is \'8M\', please use integer value 8)');
define('LANG_CHECK_MEMORY_INT', 'CLICKHEAT_MEMORY value defined in config.php must be an integerdoit (number without quotes), thanks.');
define('LANG_CHECK_GD', 'GD graphic library');
define('LANG_CHECK_GD_NA', 'GD isn\'t installed');
define('LANG_CHECK_GD_IMG', 'imagecreatetruecolor() unavailable, can\'t create images (with good quality)');
define('LANG_CHECK_GD_ALPHA', 'imagecolorallocatealpha() unavailable, can\'t create transparent images (you can ignore this, but transparency is really recommended)');
define('LANG_CHECK_OK', 'OK');
?>