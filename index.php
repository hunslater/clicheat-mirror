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
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
$_SERVER['PHP_AUTH_USER'] !== CLICKHEAT_USER || $_SERVER['PHP_AUTH_PW'] !== CLICKHEAT_PASSWORD)
{
	header('WWW-Authenticate: Basic realm="Click Tracker"');
	header('HTTP/1.0 401 Unauthorized');
	echo LANG_AUTHORIZATION;
	exit;
}

/** Input variables */
$page = isset($_GET['page']) ? $_GET['page'] : '';
$screen = isset($_GET['screen']) ? (int) $_GET['screen'] : 0;
$width = isset($_GET['width']) ? (int) $_GET['width'] : 0;
$browser = isset($_GET['browser']) ? $_GET['browser'] : '';

/** Ask for page logs deletion (and png images too) */
if (isset($_GET['delete_logs']) && in_array($_GET['delete_logs'], array(1, 7, 15)) && $page !== '')
{
	$page = str_replace(array('.', '/'), array('', ''), $page);
	if (is_dir(CLICKHEAT_LOGPATH.$page))
	{
		$d = dir(CLICKHEAT_LOGPATH.$page.'/');
		$deletedAll = true;
		while (($file = $d->read()) !== false)
		{
			if ($file === '.' || $file === '..' || $file === 'url.txt') continue;
			$date = strtotime(substr($file, 0, 10));
			/** The date is not valid (no reason for that, but hey, must check) */
			if ($date === false)
			{
				$deletedAll = false;
				continue;
			}
			/** Too old, must be deleted */
			if ($date <= mktime(0, 0, 0, date('m'), date('d') - $_GET['delete_logs'], date('Y')))
			{
				@unlink($d->path.$file);
				continue;
			}
			$deletedAll = false;
		}
		$d->close();
		/** If every log file (but the url.txt) has been deleted, then we should delete the directory too */
		if ($deletedAll === true)
		{
			@unlink(CLICKHEAT_LOGPATH.$page.'/url.txt');
			@rmdir(CLICKHEAT_LOGPATH.$page);
		}
	}
}

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
	if (file_exists(CLICKHEAT_LOGPATH.$page.'/url.txt'))
	{
		$f = @fopen(CLICKHEAT_LOGPATH.$page.'/url.txt', 'r');
		$webPage = fgets($f, 1024);
		fclose($f);
	}
	else
	{
		$webPage = '';
	}
	/** Against people that just don't understand that a demo is not a tool to promote their url... */
	if (isset($_GET['webpage']) && $webPage !== $_GET['webpage'] && $_GET['webpage'] !== '' && strpos($_SERVER['HOST_NAME'], '.labsmedia.com') === false && strpos($_SERVER['HOST_NAME'], '.lacoccinelle.net') === false)
	{
		$webPage = $_GET['webpage'];
		$f = @fopen(CLICKHEAT_LOGPATH.$page.'/url.txt', 'w');
		fputs($f, $_GET['webpage']);
		fclose($f);
	}
}
if ($webPage === '')
{
	$webPage = '../';
}

/** Date */
$date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : '1970-01-01';
if ($date === '1970-01-01')
{
	$date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
}

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
<span id="url"><a href="http://www.labsmedia.com/clickheat/"><img src="./logo.png" width="80" height="15" alt="ClickHeat" /></a></span>
<h1><?php echo LANG_H1 ?></h1>
<?php
if (CLICKHEAT_USER === 'demo' && CLICKHEAT_PASSWORD === 'demo')
{
	echo '<small class="error">'.LANG_ERROR_PASSWORD.'</small>';
}
?>
<form action="index.php" method="get" id="clickForm">
<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr>
	<th><?php echo LANG_PAGE ?></th><td><select name="page" id="formPage" onchange="document.getElementById('webpage').value = ''; document.getElementById('clickForm').submit();"><?php echo $selectPages ?></select> <small><?php echo LANG_DELETE_LOGS ?> <a href="?delete_logs=1&amp;page=<?php echo $page ?>&amp;width=<?php echo $width ?>" onclick="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">1</a> <a href="?delete_logs=7&amp;page=<?php echo $page ?>&amp;width=<?php echo $width ?>" onclick="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">7</a> <a href="?delete_logs=15&amp;page=<?php echo $page ?>&amp;width=<?php echo $width ?>" onclick="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">15</a> <?php echo LANG_DAYS ?></small></td>
	<?php if (strpos($_SERVER['SERVER_NAME'], '.labsmedia.com') === false && strpos($_SERVER['SERVER_NAME'], '.lacoccinelle.net') === false) { ?><th><?php echo LANG_EXAMPLE_URL ?></th><td><input type="text" id="webpage" name="webpage" value="<?php echo htmlentities($webPage)?>" size="30" /> <input type="submit" value="<?php echo LANG_SAVE ?>" /></td></tr><?php } else { ?><th></th><td></td></tr><?php } ?>
<tr>
	<th><?php echo LANG_DATE ?></th><td><input type="text" name="date" id="formDate" size="10" value="<?php echo $date ?>" /> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
	<th><?php echo LANG_DISPLAY_WIDTH ?></th><td><select name="width" id="formWidth"><?php echo $selectWidths ?></select> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
</tr>
<tr>
	<th><?php echo LANG_BROWSER ?></th><td><select name="browser" id="formBrowser"><?php echo $selectBrowsers ?></select> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
	<th><?php echo LANG_SCREENSIZE ?></th><td><select name="screen" id="formScreen"><?php echo $selectScreens ?></select> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
</tr>
</table>
</form>
<br />
<div id="overflowDiv">
	<div id="pngDiv"></div>
	<p><iframe src="<?php echo $webPage ?>" id="webPageFrame" onload="cleanIframe();" frameborder="0" scrolling="no" width="<?php echo $width - 40 ?>" height="100"></iframe></p>
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
	xmlhttp.open('GET', './generate.php?page=' + document.getElementById('formPage').value + '&screen=' + document.getElementById('formScreen').value + '&width=' + document.getElementById('formWidth').value + '&browser=' + document.getElementById('formBrowser').value + '&date=' + document.getElementById('formDate').value + '&rand=' + Date(), true);
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
</script>
</body>
</html>