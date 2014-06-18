<?php
/**
 * ClickHeat : réponse à l'appel Ajax / Reply to the Ajax call
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

/** Main class */
include CLICKHEAT_ROOT.'classes/Heatmap.class.php';
include CLICKHEAT_ROOT.'classes/HeatmapFromClicks.class.php';

/** Screen size */
$screen = isset($_GET['screen']) ? (int) $_GET['screen'] : 0;
$minScreen = 0;
if ($screen < 0)
{
	$width = abs($screen);
	$maxScreen = 3000;
}
else
{
	$maxScreen = $screen;
	if (!in_array($screen, $__screenSizes) || $screen === 0)
	{
		errorGenerate(LANG_ERROR_SCREEN);
	}
	for ($i = 1; $i < count($__screenSizes); $i++)
	{
		if ($__screenSizes[$i] === $screen)
		{
			$minScreen = $__screenSizes[$i - 1];
			break;
		}
	}
	$width = $screen - 25;
}

/** Browser */
$browser = isset($_GET['browser']) ? $_GET['browser'] : '';
if (!isset($__browsersList[$browser]))
{
	$browser = 'all';
}

/** Time and memory limits */
@set_time_limit(120);
@ini_set('memory_limit', $clickheatConf['memory'].'M');

/** Selected Group */
$group = isset($_GET['group']) ? str_replace('/', '', $_GET['group']) : '****dead_directory****';
if (!is_dir($clickheatConf['logPath'].$group))
{
	errorGenerate(LANG_ERROR_GROUP);
}

/** Show clicks or heatmap */
$heatmap = isset($_GET['heatmap']) && $_GET['heatmap'] === '1';

/** Date and days */
$dateStamp = isset($_GET['date']) ? strtotime($_GET['date']) : time();
$range = isset($_GET['range']) && in_array($_GET['range'], array('d', 'w', 'm')) ? $_GET['range'] : 'd';
$days = $range === 'd' ? 1 : ($range === 'w' ? 7 : date('t', $dateStamp));
$date = date('Y-m-d', $dateStamp);

$imagePath = $group.'-'.$date.'-'.$range.'-'.$screen.'-'.$browser.'-'.($heatmap === true ? 'heat' : 'click');

/** If images are already created, just stop script here if these have less than 120 seconds (today's log) or 86400 seconds (old logs) */
$delay = $days === 1 && date('d', $dateStamp) !== date('d') ? 86400 : 120;
if (file_exists($clickheatConf['cachePath'].$imagePath.'.html') && filemtime($clickheatConf['cachePath'].$imagePath.'.html') > time() - $delay)
{
	readfile($clickheatConf['cachePath'].$imagePath.'.html');
	exit;
}

/** Get some data for the current group (centered and/or fixed layout) */
if (file_exists($clickheatConf['logPath'].$group.'/url.txt'))
{
	$f = fopen($clickheatConf['logPath'].$group.'/url.txt', 'r');
	$layout = trim(fgets($f, 1024));
	fclose($f);
}
else
{
	$layout = '';
}
$layout = explode('>', $layout);
if (count($layout) !== 4)
{
	$layout = array('', 0, 0, 0);
}

/** Call the Heatmap class */
$clicksHeatmap = new HeatmapFromClicks();
$clicksHeatmap->browser = $browser;
$clicksHeatmap->minScreen = $minScreen;
$clicksHeatmap->maxScreen = $maxScreen;
$clicksHeatmap->layout = $layout;
$clicksHeatmap->memory = $clickheatConf['memory'] * 1048576;
$clicksHeatmap->step = $clickheatConf['step'];
$clicksHeatmap->dot = $clickheatConf['dot'];
$clicksHeatmap->palette = $clickheatConf['palette'];
$clicksHeatmap->heatmap = $heatmap;
$clicksHeatmap->path = $clickheatConf['cachePath'];
$clicksHeatmap->cache = $clickheatConf['cachePath'];
$clicksHeatmap->file = $imagePath.'-%d.png';
/** Add files */
for ($day = 0; $day < $days; $day++)
{
	$currentDate = date('Y-m-d', mktime(0, 0, 0, date('m', $dateStamp), date('d', $dateStamp) + $day, date('Y', $dateStamp)));
	$clicksHeatmap->addFile($clickheatConf['logPath'].$group.'/'.$currentDate.'.log');
}

$result = $clicksHeatmap->generate($width);
if ($result === false)
{
	errorGenerate($clicksHeatmap->error);
}
$html = '';
$time = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
for ($i = 0; $i < $result['count']; $i++)
{
	$html .= '<img src="'.CLICKHEAT_INDEX_PATH.'action=png&amp;file='.$result['filenames'][$i].'&amp;rand='.$time.'" width="'.$result['width'].'" height="'.$result['height'].'" alt="" id="heatmap-'.$i.'" /><br />';
}
echo $html;

/** Save the HTML code to speed up following queries (only over two minutes) */
$f = fopen($clickheatConf['cachePath'].$imagePath.'.html', 'w');
fputs($f, $html);
fclose($f);

/**
 * Retourne une erreur / Returns an error
 *
 * @param string $error
**/
function errorGenerate($error)
{
	echo '&nbsp;<div style="line-height:20px;"><span class="error">'.$error.'</span></div>';
	exit;
}
?>