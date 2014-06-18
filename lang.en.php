<?php
/**
 * ClickHeat : Fichier de langue : anglais
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

define('LANG_TITLE', 'ClickHeat');
define('LANG_INDEX', 'Results');
define('LANG_TOOLS', 'Tools');
define('LANG_AUTHORIZATION', 'Sorry, this webpage is protected, please provide the good password');
define('LANG_DELETE_LOGS', 'Delete logs older than');
define('LANG_DELETE_LOG_DIR', 'Delete all logs including the log directory (useful when you want to remove clickheat completly)');
define('LANG_UPDATE_OLD_LOGS', 'Update old logs (before version 0.9) to version 0.9 or newer, as the format of logs has changed to improve security');
define('LANG_DELETED_FILES', 'files were deleted');
define('LANG_RENAMED_FILES', 'files were renamed');
define('LANG_LOG_DELETED', 'The log directory has been deleted');
define('LANG_LOG_NOT_DELETED', 'The log directory couldn\'t be deleted');
define('LANG_DAYS', 'day(s)');
define('LANG_TO', 'to');
define('LANG_SURE', 'Sure');
define('LANG_UPDATE', 'Update');
define('LANG_SAVE', 'Save');
define('LANG_PAGE', 'Page');
define('LANG_BROWSER', 'Browser');
define('LANG_ALL', 'All');
define('LANG_UNKNOWN', 'Other/unknown');
define('LANG_DATE', 'Dates');
define('LANG_OK', 'OK');
define('LANG_FOR_PAGE', 'for pages');
define('LANG_EXAMPLE_URL', 'Webpage');
define('LANG_LAYOUT_WIDTH', 'Layout widths: left, center, right');
define('LANG_DISPLAY_WIDTH', 'Results display size');
define('LANG_SCREENSIZE', 'Screen size');
define('LANG_HEATMAP', 'Show as heatmap');
define('LANG_CHECK_LATEST', 'Check latest available version');
define('LANG_LATEST_VERSION', 'Latest version');
define('LANG_YOUR_VERSION', 'Your version');
define('LANG_NO_CLICK_BELOW', 'No clicks recorded beneath this line');
define('LANG_ERROR_PASSWORD', 'Beware ! You didn\'t speficied any password or didn\'t change the default one, so anyone has access to this page');
define('LANG_ERROR_PAGE', 'Unknown page');
define('LANG_ERROR_DATA', 'No logs for the selected period');
define('LANG_ERROR_FILE', 'Can\'t open log file');
define('LANG_ERROR_MEMORY', 'Memory limit not available in ini_get(), please look at config.php');
define('LANG_ERROR_PNG', 'This PNG file has not been created');
define('LANG_ERROR_LOADING', 'Generating image, please wait...');
define('LANG_ERROR_DIRECTORY', 'Logging directory doesn\'t exist => please run the <a href="check.php">check.php script</a> first');
define('LANG_ERROR_FIXED', 'All widths are fixed, that is not possible. Please change one of your layout width above.');
define('LANG_ERROR_TODAY', 'Today\'s data is forbidden in the demo, as some people make the server goes mad (caching is not available for the current day)');
define('LANG_CHECKS', 'Main checks for ClickHeat');
define('LANG_CHECKS_TO_BE', 'All rows must display OK so the script works well (except "Your system")');
define('LANG_CHECK_SYSTEM', 'Your system (just for information)');
define('LANG_CHECK_LOGPATH', 'Checking logs\' path');
define('LANG_CHECK_LOGPATH_DIR', 'can\'t create the directory, please try to create it yourself ('.CLICKHEAT_LOGPATH.')');
define('LANG_CHECK_LOGPATH_MKDIR', 'can\'t create a new sub-directory, please check permissions of the directory (must be writeable to the Apache user)');
define('LANG_CHECK_LOGPATH_TOUCH', 'can\'t touch a file in the sub-directory (that should not occur)');
define('LANG_CHECK_MEMORY', 'Memory limit');
define('LANG_CHECK_MEMORY_BAD', 'ini_get() is not available, and no (integer) value is defined in the config.php. Please fill the CLICKHEAT_MEMORY value according to php.ini (format to use is mega-bytes, so if php.ini\'s \'memory_limit\' value is \'8M\', please use integer value 8)');
define('LANG_CHECK_MEMORY_INT', 'CLICKHEAT_MEMORY value defined in config.php must be an integerdoit (number without quotes), thanks.');
define('LANG_CHECK_GD', 'GD graphic library');
define('LANG_CHECK_GD_IMG', 'imagecreatetruecolor() unavailable, can\'t create images (with good quality), check that GD is installed');
define('LANG_CHECK_GD_ALPHA', 'imagecolorallocatealpha() unavailable, can\'t create transparent images (you can ignore this, but transparency is really recommended)');
define('LANG_CHECK_GD_PNG', 'imagepng() unavailable, can\'t create PNG images, sorry');
define('LANG_CHECK_OK', 'OK');
$__jsHelp = array(
	'layout' => 'Site layout: 0 = automatic width, else it\'s column\'s width in pixels.<br />Examples: fixed 100px left menu, content uses the available space left: 100 0 0<br />Content 750px, centered (with an optional menu within 750px): 0 750 0<br />Left fixed 100px menu, content 650px align to the left: 100 650 0 or 750 0 0 (because everything is on the left)<br />Left and right menus, fixed 100px, central content uses the available space left: 100 0 100<br />100% content: 0 0 0<br /><br />If your main content is not fixed, you should select the same "Screen size" as "Results display size" so that the clicks are at the good position.',
	'page' => 'This is the tag used while installing Javascript code: initClickheat(\'page\');',
	'date' => 'Choose the report date, format is YYYY-MM-DD. If you have few data, it might be interesting to enlarge the report over many days: fill in the second field for this.',
	'heatmap' => 'Choose display format: only clicks position (default, very fast, left clicks are red, right clicks are green), or an heatmap (slower, only left clicks)',
	'web' => 'Provide an internet address here which has the tag relative to the current page (parameter "Page" on the left), this page will be shown under the heatmap. Default is address "../", which probably is the root of your website. You can use an absolute address (http://www.my-site.com/page.html or simply /page.html) or relative (../page.html)'
);
?>