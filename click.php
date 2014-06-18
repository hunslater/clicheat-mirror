<?php
/**
 * ClickHeat : Enregistrement d'un clic suivi / Logging of a tracked click
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

include './config.php';

/** Check parameters */
if (!isset($_POST['x']) || !isset($_POST['y']) || !isset($_POST['w']) || !isset($_POST['p']) || !isset($_POST['b']))
{
	exit;
}

/** Check if page and browser are letters-only */
$page = preg_replace('/[^a-z_0-9]+/i', '', $_POST['p']);
if ($page === '')
{
	$page = 'none';
}
$browser = preg_replace('/[^a-z]+/', '', strtolower($_POST['b']));
if ($browser === '')
{
	$browser = 'unknown';
}
/** Logging the click */
if (@error_log(((int) $_POST['x']).'|'.((int) $_POST['y']).'|'.((int) $_POST['w']).'|'.$browser."\n", 3, CLICKHEAT_LOGPATH.$page.'/'.date('Y-m-d').'.log') === false)
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
	@error_log(((int) $_POST['x']).'|'.((int) $_POST['y']).'|'.((int) $_POST['w']).'|'.$browser."\n", 3, CLICKHEAT_LOGPATH.$page.'/'.date('Y-m-d').'.log');
}
?>