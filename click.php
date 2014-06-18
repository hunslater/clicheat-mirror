<?php
/**
 * ClickHeat : Enregistrement d'un clic suivi / Logging of a tracked click
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

include './config.php';

/** Check parameters */
if (!isset($_GET['x']) || !isset($_GET['y']) || !isset($_GET['w']) || !isset($_GET['p']) || !isset($_GET['b']))
{
	exit;
}

/** Check if page and browser are letters-only */
$page = preg_replace('/[^a-z_0-9]+/i', '', $_GET['p']);
if ($page === '')
{
	$page = 'none';
}
$browser = preg_replace('/[^a-z]+/', '', strtolower($_GET['b']));
if ($browser === '')
{
	$browser = 'unknown';
}
/** Logging the click */
if (@error_log(((int) $_GET['x']).'|'.((int) $_GET['y']).'|'.((int) $_GET['w']).'|'.$browser."\n", 3, CLICKHEAT_LOGPATH.$page.'/'.date('Y-m-d').'.log') === false)
{
	/** Can't write the log, let's try to create the directory */
	if (!is_dir(CLICKHEAT_LOGPATH))
	{
		@mkdir(CLICKHEAT_LOGPATH);
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
		{
			@chmod(CLICKHEAT_LOGPATH, 0755);
		}
	}
	if (!is_dir(CLICKHEAT_LOGPATH.$page))
	{
		@mkdir(CLICKHEAT_LOGPATH.$page.'/');
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
		{
			@chmod(CLICKHEAT_LOGPATH.$page.'/', 0755);
		}
	}
	@error_log(((int) $_GET['x']).'|'.((int) $_GET['y']).'|'.((int) $_GET['w']).'|'.$browser."\n", 3, CLICKHEAT_LOGPATH.$page.'/'.date('Y-m-d').'.log');
}
?>