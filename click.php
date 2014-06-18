<?php
/**
 * ClickHeat : Enregistrement d'un clic suivi / Logging of a tracked click
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

/** First of all, check if we are inside PhpMyVisites */
if (defined('INCLUDE_PATH'))
{
	define('CLICKHEAT_ROOT', INCLUDE_PATH.'/libs/clickheat/');
	define('IS_PHPMV_MODULE', true);
	define('CLICKHEAT_CONFIG', INCLUDE_PATH.'/config/clickheat.php');
}
else
{
	define('CLICKHEAT_ROOT', './');
	define('IS_PHPMV_MODULE', false);
	define('CLICKHEAT_CONFIG', CLICKHEAT_ROOT.'config/config.php');
}

/** Include config file */
include CLICKHEAT_CONFIG;

/** Check parameters */
if (!isset($clickheatConf) || !isset($_GET['x']) || !isset($_GET['y']) || !isset($_GET['w']) || !isset($_GET['g']) || !isset($_GET['s']) || !isset($_GET['b']) || !isset($_GET['c']))
{
	exit('Parameters or config error');
}

/** Check referers */
if (is_array($clickheatConf['referers']))
{
	if (!isset($_SERVER['HTTP_REFERER']))
	{
		exit('No referer');
	}
	$referer = parse_url($_SERVER['HTTP_REFERER']);
	if (!in_array($referer['host'], $clickheatConf['referers']))
	{
		exit('Forbidden referer');
	}
}

/** Check if group, site and browser are letters-only */
$site = substr(preg_replace('/[^a-z_0-9\-]+/', '.', strtolower($_GET['s'])), 0, 50);
$group = substr(preg_replace('/[^a-z_0-9\-]+/', '.', strtolower($_GET['g'])), 0, 50);
if ($group === '')
{
	exit('No group specified (clickHeatGroup empty)');
}
/** Check group */
if (is_array($clickheatConf['groups']))
{
	if (!in_array($group, $clickheatConf['groups']))
	{
		exit('Forbidden group');
	}
}
$browser = preg_replace('/[^a-z]+/', '', strtolower($_GET['b']));
if ($browser === '')
{
	exit('Browser empty');
}
$final = ltrim($site.','.$group, ',');
/** Limit file size */
if ($clickheatConf['filesize'] !== 0)
{
	if (@filesize($clickheatConf['logPath'].$final.'/'.date('Y-m-d').'.log') > $clickheatConf['filesize'])
	{
		exit('Filesize reached limit');
	}
}
/** Logging the click */
$f = @fopen($clickheatConf['logPath'].$final.'/'.date('Y-m-d').'.log', 'a');
if ($f === false)
{
	/** Can't open the log, let's try to create the directory */
	if (!is_dir(rtrim($clickheatConf['logPath'], '/')))
	{
		if (!@mkdir(rtrim($clickheatConf['logPath'], '/')))
		{
			exit('Cannot create log directory: '.$clickheatConf['logPath']);
		}
	}
	if (!is_dir($clickheatConf['logPath'].$final))
	{
		if (!@mkdir($clickheatConf['logPath'].$final))
		{
			exit('Cannot create log directory: '.$clickheatConf['logPath'].$final);
		}
		if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '')
		{
			$f = fopen($clickheatConf['logPath'].$final.'/url.txt', 'w');
			fputs($f, str_replace('debugclickheat', '', $_SERVER['HTTP_REFERER']).'>0>0>0');
			fclose($f);
		}
	}
	$f = fopen($clickheatConf['logPath'].$final.'/'.date('Y-m-d').'.log', 'a');
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