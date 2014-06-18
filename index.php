<?php
/**
 * ClickHeat : Fichier de résultats / Results file
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

include './config.php';

/** Loading language according to browser's Accept-Language */
$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) : '';
if (!in_array($lang, $availableLanguages))
{
	$lang = $availableLanguages[0];
}
include './lang.'.$lang.'.php';

/** Login check */
include './login.php';

/** Against people that just don't understand that a demo is not a tool to promote their url... */
$demoServer = strpos($_SERVER['SERVER_NAME'], '.labsmedia.com') !== false;

/** Input variables */
$page = isset($_GET['page']) ? $_GET['page'] : '';
$screen = isset($_GET['screen']) ? (int) $_GET['screen'] : 0;
$width = isset($_GET['width']) ? (int) $_GET['width'] : 0;
$browser = isset($_GET['browser']) ? $_GET['browser'] : '';
$heatmap = isset($_GET['heatmap']);

/** List of available pages */
$selectPages = '';
$firstPage = '';
$pageExists = false;
$d = @dir(CLICKHEAT_LOGPATH);
if ($d === false)
{
	echo LANG_ERROR_DIRECTORY;
	die();
}
while (($file = $d->read()) !== false)
{
	if (strpos($file, '.') !== false) continue;
	$firstPage = $firstPage === '' ? $file : '';
	if ($page === $file)
	{
		$selectPages .= '<option value="'.$file.'" selected="selected">'.$file.'</option>';
		$pageExists = true;
	}
	else
	{
		$selectPages .= '<option value="'.$file.'">'.$file.'</option>';
	}
}
$d->close();
if ($pageExists === false)
{
	$page = $firstPage !== '' ? $firstPage : '';
}
$webPage = '';
if ($page !== '')
{
	if (file_exists(CLICKHEAT_LOGPATH.$page.'/%%url.txt%%'))
	{
		$f = @fopen(CLICKHEAT_LOGPATH.$page.'/%%url.txt%%', 'r');
		$webPage = trim(fgets($f, 1024));
		fclose($f);
	}
	else
	{
		$webPage = '';
	}
	$webPageGet = isset($_GET['webpage']) && is_array($_GET['webpage']) && count($_GET['webpage']) === 4 && $_GET['webpage'][0] !== '' ? $_GET['webpage'][0].'>'.((int) $_GET['webpage'][1]).'>'.((int) $_GET['webpage'][2]).'>'.((int) $_GET['webpage'][3]) : '';
	if ($demoServer === false)
	{
		if ($webPage !== $webPageGet && $webPageGet !== '')
		{
			$webPage = $webPageGet;
			$f = @fopen(CLICKHEAT_LOGPATH.$page.'/%%url.txt%%', 'w');
			fputs($f, $webPage);
			fclose($f);
		}
	}
}
$webPage = explode('>', $webPage);
if (count($webPage) !== 4)
{
	$webPage = array('../', 0, 0, 0);
}
$webPage[1] = (int) $webPage[1];
$webPage[2] = (int) $webPage[2];
$webPage[3] = (int) $webPage[3];

/** Date and days */
$date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : '1970-01-01';
if ($date === '1970-01-01')
{
	$date = date('Y-m-d');
}
$days = isset($_GET['days']) ? (int) $_GET['days'] : 1;

asort($screenSizes);
/** Width of display */
$selectWidths = '';
if (!in_array($width, $screenSizes))
{
	/** Looking for the closest width in the list */
	$futureWidth = 0;
	for ($i = 0; $i < count($screenSizes) - 1; $i++)
	{
		if ($width > $screenSizes[$i])
		{
			$futureWidth = $screenSizes[$i + 1];
		}
	}
	$width = $futureWidth;
	unset($futureWidth);
}
for ($i = 1; $i < count($screenSizes); $i++)
{
	$selectWidths .= '<option value="'.$screenSizes[$i].'"'.($screenSizes[$i] === $width ? ' selected="selected"' : '').'>'.$screenSizes[$i].'px</option>';
}

/** Screen sizes */
$selectScreens = '';
if (!in_array($screen, $screenSizes))
{
	$screen = $width;
}
for ($i = 0; $i < count($screenSizes); $i++)
{
	$selectScreens .= '<option value="'.$screenSizes[$i].'"'.($screenSizes[$i] === $screen ? ' selected="selected"' : '').'>'.($screenSizes[$i] === 0 ? LANG_ALL : $screenSizes[$i].'px').'</option>';
}

/** Browsers */
$selectBrowsers = '';
if (!isset($browsersList[$browser]))
{
	$browser = 'all';
}
foreach ($browsersList as $label => $name)
{
	$selectBrowsers .= '<option value="'.$label.'"'.($label === $browser ? ' selected="selected"' : '').'>'.($label === 'all' ? LANG_ALL : ($label === 'unknown' ? LANG_UNKNOWN : $name)).'</option>';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title><?php echo LANG_TITLE ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="./clickheat.css" />
</head>
<body>
<?php
if (CLICKHEAT_PASSWORD === '' || CLICKHEAT_PASSWORD === 'demo')
{
	echo '<small class="error" style="float:right;">'.LANG_ERROR_PASSWORD.'</small>';
}
?>
<span id="float-right"><a href="http://www.labsmedia.com/clickheat/"><img src="./logo.png" width="80" height="15" alt="ClickHeat" /></a></span>
<h1><?php echo LANG_TITLE ?> : <?php echo LANG_INDEX ?> - <a href="tools.php"><?php echo LANG_TOOLS ?></a></h1>
<form action="index.php" method="get" id="clickForm">
<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr>
	<th><?php echo LANG_PAGE ?> <acronym onmouseover="showHelp('page');" onmouseout="showHelp('');">?</acronym></th><td><select name="page" id="formPage" onchange="document.getElementById('webpage0').value = ''; document.getElementById('clickForm').submit();"><?php echo $selectPages ?></select></td>
	<?php if ($demoServer === false) { ?><th><?php echo LANG_EXAMPLE_URL ?> <acronym onmouseover="showHelp('web');" onmouseout="showHelp('');">?</acronym></th><td><input type="text" id="webpage0" name="webpage[0]" value="<?php echo htmlentities($webPage[0])?>" size="15" /> <input type="submit" value="<?php echo LANG_SAVE ?>" /></td></tr><?php } else { ?><th></th><td></td></tr><?php } ?>
<tr>
	<th><?php echo LANG_DATE ?> <acronym onmouseover="showHelp('date');" onmouseout="showHelp('');">?</acronym></th><td><input type="text" name="date" id="formDate" size="10" value="<?php echo $date ?>" /> <?php echo LANG_FOR ?> <input type="text" name="days" id="formDays" size="2" value="<?php echo $days ?>" /> <?php echo LANG_DAYS ?> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
	<?php if ($demoServer === false) { ?><th><?php echo LANG_LAYOUT_WIDTH ?> <acronym onmouseover="showHelp('layout');" onmouseout="showHelp('');">?</acronym></th><td><input type="text" name="webpage[1]" value="<?php echo $webPage[1] ?>" size="3" /> <input type="text" name="webpage[2]" value="<?php echo $webPage[2] ?>" size="3" /> <input type="text" name="webpage[3]" value="<?php echo $webPage[3] ?>" size="3" /> <input type="submit" value="<?php echo LANG_SAVE ?>" /></td></tr><?php } else { ?><th></th><td></td></tr><?php } ?>
</tr>
<tr>
	<th><?php echo LANG_BROWSER ?></th><td><select name="browser" id="formBrowser"><?php echo $selectBrowsers ?></select> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
	<th><?php echo LANG_DISPLAY_WIDTH ?></th><td><select name="width" id="formWidth"><?php echo $selectWidths ?></select> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
</tr>
<tr>
	<th><?php echo LANG_HEATMAP ?></th><td><input type="checkbox" id="formHeatmap" name="heatmap"<?php if ($heatmap === true) echo ' checked="checked"'; ?> /> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
	<th><?php echo LANG_SCREENSIZE ?></th><td><select name="screen" id="formScreen"><?php echo $selectScreens ?></select> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
</tr>
</table>
</form>
<div id="overflowDiv">
	<div id="helpDiv"></div>
	<div id="pngDiv"></div>
	<p><iframe src="<?php echo $webPage[0] ?>" id="webPageFrame" onload="cleanIframe();" frameborder="0" scrolling="no" width="<?php echo $width - 40 ?>" height="100"></iframe></p>
</div>
<!--[if lt IE 7.]>
<script defer type="text/javascript">var correctPng = true;</script>
<![endif]-->
<script type="text/javascript">
var correctPng = (correctPng == undefined ? false : true);
/** Resize the main div to the height of the current page */
oD = document.documentElement != undefined && document.documentElement.clientHeight != 0 ? document.documentElement : document.body;
iH = oD.innerHeight != undefined ? oD.innerHeight : oD.clientHeight;
document.getElementById('overflowDiv').style.height = (iH < 300 ? 400 : iH) - 130 + 'px';
/** Width of main display */
iW = oD.innerWidth != undefined ? oD.innerWidth : oD.clientWidth;
/** Must reload if width is not defined */
<?php if ($width === 0 && !isset($_GET['width'])): ?>
window.location.href = 'index.php?width=' + (iW < 300 ? 400 : iW);
<?php endif; ?>

/** Ajax requests to update PNGs */
function getNewPngs()
{
	document.getElementById('pngDiv').innerHTML = '<span class="error"><?php echo addslashes(LANG_ERROR_LOADING); ?></span>';
	try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
	catch (e)
	{
		try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	}
		catch (oc) { xmlhttp = null; }
	}
	if (!xmlhttp && typeof XMLHttpRequest != undefined) xmlhttp = new XMLHttpRequest();
	xmlhttp.open('GET', './generate.php?page=' + document.getElementById('formPage').value + '&screen=' + document.getElementById('formScreen').value + '&width=' + document.getElementById('formWidth').value + '&browser=' + document.getElementById('formBrowser').value + '&date=' + document.getElementById('formDate').value + '&days=' + document.getElementById('formDays').value + '&heatmap=' + (document.getElementById('formHeatmap').checked ? '1' : '0') + '&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			document.getElementById('pngDiv').innerHTML = xmlhttp.responseText;
			document.getElementById('webPageFrame').height = document.getElementById('pngDiv').offsetHeight < 100 ? 100 : document.getElementById('pngDiv').offsetHeight;
			/**
			* Correctly handle PNG transparency in Win IE 5.5 & 6. http://homepage.ntlworld.com/bobosola. Updated 18-Jan-2006.
			* I've modified it a lot to meet my needs :-)
			**/
			if (correctPng == false) return true;

			var arVersion = navigator.appVersion.split("MSIE");
			var version = parseFloat(arVersion[1]);
			if (version < 5.5 || document.body.filters == undefined) return true;

			for (i = 0; i < document.images.length; i++)
			{
				var img = document.images[i];
				if (img.src.search(/png\.php/) != -1)
				{
					img.outerHTML = '<span style="display:inline-block; margin-bottom:-1px; width:' + img.width + 'px; height:' + img.height + 'px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + img.src + '\', sizingMethod=\'scale\');"></span>';
					i--;
				}
			}
		}
	}
	xmlhttp.send(null);
}
var xmlhttp;

getNewPngs();

/** Hide iframe's flashes and iframes */
function cleanIframe()
{
	try
	{
		var currentIframe = document.getElementById('webPageFrame');
		if (currentIframe.contentDocument)
		{
			currentIframeContent = currentIframe.contentDocument;
		}
		else if (currentIframe.Document)
		{
			currentIframeContent = currentIframe.Document;
		}
		/** Hide iframes and flashes content */
		if (currentIframeContent != undefined)
		{
			aIframes = currentIframeContent.body.getElementsByTagName('iframe');
			for (i = 0; i < aIframes.length; i++)
			{
				aIframes[i].src='';
			}
			aFlashes = currentIframeContent.body.getElementsByTagName('object');
			for (i = 0; i < aFlashes.length; i++)
			{
				aFlashes[i].src='';
			}
		}
	}
	catch(e) {}
}

/** Show contextual help */
var helpText = new Array();
<?php
foreach ($__jsHelp as $key => $text)
{
	echo 'helpText[\'', $key, '\'] = \'', addslashes($text), '\';';
}
?>
function showHelp(id)
{
	if (id == '')
	{
		document.getElementById('helpDiv').style.display = 'none';
	}
	else
	{
		document.getElementById('helpDiv').innerHTML = helpText[id];
		document.getElementById('helpDiv').style.display = 'block';
	}
}
</script>
</body>
</html>