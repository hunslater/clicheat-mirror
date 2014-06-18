<?php
/**
 * ClickHeat : Enregistrement d'un clic suivi / Logging of a tracked click
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

/** First of all, check if we are inside PhpMyVisites */
if (!defined('INCLUDE_PATH'))
{
	define('CLICKHEAT_ROOT', './');
	define('IS_PHPMV_MODULE', false);
}
else
{
	define('CLICKHEAT_ROOT', INCLUDE_PATH.'/libs/clickheat/');
	define('IS_PHPMV_MODULE', true);
}

/** If there's no config file, run check script */
include CLICKHEAT_ROOT.'config/config.php';

/** Check parameters */
if (!isset($clickheatConf) || !isset($_GET['x']) || !isset($_GET['y']) || !isset($_GET['w']) || !isset($_GET['p']) || !isset($_GET['b']) || !isset($_GET['c']))
{
	exit('Parameters or config error');
}

/** Check if page and browser are letters-only */
$page = strtolower(substr(preg_replace('/[^a-z_0-9\-]+/i', '.', $_GET['p']), 0, 50));
if ($page === '')
{
	exit('No page specified (clickHeatPage empty)');
}
$browser = preg_replace('/[^a-z]+/', '', strtolower($_GET['b']));
if ($browser === '')
{
	exit('Browser empty');
}
/** Logging the click */
$f = @fopen($clickheatConf['logPath'].$page.'/%%'.date('Y-m-d').'.log%%', 'a');
if ($f === false)
{
	/** Can't open the log, let's try to create the directory */
	if (!is_dir($clickheatConf['logPath']))
	{
		if (!@mkdir(rtrim($clickheatConf['logPath'], '/')))
		{
			exit('Cannot create log directory: '.$clickheatConf['logPath']);
		}
	}
	if (!is_dir($clickheatConf['logPath'].$page))
	{
		if (!@mkdir($clickheatConf['logPath'].$page))
		{
			exit('Cannot create log directory: '.$clickheatConf['logPath']);
		}
		if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '')
		{
			$f = fopen($clickheatConf['logPath'].$page.'/%%url.txt%%', 'w');
			fputs($f, $_SERVER['HTTP_REFERER'].'>0>0>0');
			fclose($f);
		}
	}
	$f = fopen($clickheatConf['logPath'].$page.'/%%'.date('Y-m-d').'.log%%', 'a');
}
if ($f !== false)
{
	echo 'OK';
	fputs($f, ((int) $_GET['x']).'|'.((int) $_GET['y']).'|'.((int) $_GET['w']).'|'.$browser.'|'.((int) $_GET['c'])."\n");
	fclose($f);
}
else
{
	echo 'KO, file not writable';
}
?>