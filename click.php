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
$f = @fopen(CLICKHEAT_LOGPATH.$page.'/%%'.date('Y-m-d').'.log%%', 'a');
if ($f === false)
{
	/** Can't open the log, let's try to create the directory */
	if (!is_dir(CLICKHEAT_LOGPATH))
	{
		@mkdir(CLICKHEAT_LOGPATH);
	}
	if (!is_dir(CLICKHEAT_LOGPATH.$page))
	{
		@mkdir(CLICKHEAT_LOGPATH.$page.'/');
	}
	$f = @fopen(CLICKHEAT_LOGPATH.$page.'/%%'.date('Y-m-d').'.log%%', 'a');
}
if ($f !== false)
{
	fputs($f, ((int) $_GET['x']).'|'.((int) $_GET['y']).'|'.((int) $_GET['w']).'|'.$browser."\n");
	fclose($f);
}
?>