<?php
/**
 * ClickHeat : Enregistrement d'un clic suivi / Logging of a tracked click
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
 * @update 27/02/2007 - Yvan Taviaud : ajout du référant dans url.txt s'il n'existe pas encore.
**/

include './config.php';

/** Check parameters */
if (!isset($_GET['x']) || !isset($_GET['y']) || !isset($_GET['w']) || !isset($_GET['p']) || !isset($_GET['b']) || !isset($_GET['c']))
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
		if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '')
		{
			$f = @fopen(CLICKHEAT_LOGPATH.$page.'/%%url.txt%%', 'w');
			fputs($f, $_SERVER['HTTP_REFERER'].'>0>0>0');
			fclose($f);
		}
	}
	$f = @fopen(CLICKHEAT_LOGPATH.$page.'/%%'.date('Y-m-d').'.log%%', 'a');
}
if ($f !== false)
{
	fputs($f, ((int) $_GET['x']).'|'.((int) $_GET['y']).'|'.((int) $_GET['w']).'|'.$browser.'|'.((int) $_GET['c'])."\n");
	fclose($f);
}
?>