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
if (CLICKHEAT_USER !== 'allow everyone' && CLICKHEAT_PASSWORD !== 'allow everyone' &&
(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_USER']) ||
$_SERVER['PHP_AUTH_USER'] !== CLICKHEAT_USER || $_SERVER['PHP_AUTH_PW'] !== CLICKHEAT_PASSWORD))
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

/** Ask for page logs deletion */
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
			$date = strtotime(str_replace('.log', '', $file));
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
$d = dir(CLICKHEAT_LOGPATH);
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
	if (isset($_GET['webpage']) && $webPage !== $_GET['webpage'] && $_GET['webpage'] !== '')
	{
		$webPage = $_GET['webpage'];
		$f = @fopen(CLICKHEAT_LOGPATH.$page.'/url.txt', 'w');
		fputs($f, $_GET['webpage']);
		fclose($f);
	}
}
/** Against people that just don't understand that a demo is not a tool to promote their url... */
if (strpos($_SERVER['HTTP_HOST'], 'www.labsmedia.com') === 0)
{
	$webPage = 'http://www.labsmedia.com/clickheat/';
}
elseif ($webPage === '')
{
	$webPage = 'http://'.substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], ':')).'/';
}

/** Date */
$date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : date('Y-m-d');
if ($date === '1970-01-01')
{
	$date = date('Y-m-d');
}

/** Screen sizes */
$selectScreens = '';
if (!in_array($screen, $screenSizes))
{
	$screen = 0;
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
if (CLICKHEAT_USER === 'allow everyone' || CLICKHEAT_PASSWORD === 'allow everyone')
{
	echo '<small style="background-color:red; color:white">'.LANG_ERROR_PASSWORD.'</small>';
}
?>
<form action="index.php" method="get" id="clickForm">
<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr>
	<th><?php echo LANG_PAGE ?></th><td><select name="page" id="formPage" onchange="document.getElementById('webpage').value = ''; document.getElementById('clickForm').submit();"><?php echo $selectPages ?></select> <small><?php echo LANG_DELETE_LOGS ?> <a href="?delete_logs=1&amp;page=<?php echo $page ?>" onclick="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">1</a> <a href="?delete_logs=7&amp;page=<?php echo $page ?>" onclick="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">7</a> <a href="?delete_logs=15&amp;page=<?php echo $page ?>" onclick="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">15</a> <?php echo LANG_DAYS ?></small></td>
	<?php if (strpos($_SERVER['HTTP_HOST'], 'www.labsmedia.com') !== 0) { ?><th><?php echo LANG_EXAMPLE_URL ?></th><td><input type="text" id="webpage" name="webpage" value="<?php echo htmlentities($webPage)?>" size="30" /> <input type="submit" value="<?php echo LANG_SAVE ?>" /></td></tr><?php } else { ?><th></th><td></td></tr><?php } ?>
<tr>
	<th><?php echo LANG_DATE ?></th><td><input type="text" name="date" id="formDate" size="10" value="<?php echo $date ?>" /> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
	<th><?php echo LANG_DISPLAY_WIDTH ?></th><td><input type="text" name="width" id="formWidth" size="4" value="<?php echo $width ?>" /> <input type="submit" value="<?php echo LANG_UPDATE ?>" /></td>
</tr>
<tr>
	<th><?php echo LANG_BROWSER ?></th><td><select name="browser" id="formBrowser" onchange="showPng();"><?php echo $selectBrowsers ?></select></td>
	<th><?php echo LANG_SCREENSIZE ?></th><td><select name="screen" id="formScreen" onchange="showPng();"><?php echo $selectScreens ?></select></td>
</tr>
</table>
</form>
<div style="position:absolute; left:0; top:130px; text-align:center; width:100%; overflow:auto;" id="overflowDiv">
	<img id="pngTag" src="./png.php?load=0" alt="" style="position:absolute;" />
	<iframe src="<?php echo $webPage?>" onload="showPng();" id="webPageFrame" frameborder="0" scrolling="no" style="z-index:1; border-top:1px solid #888; <?php if ($width !== 0) echo 'width:'.($width - 25).'px;' ?>"></iframe>
</div>
<script type="text/javascript">
/** Resize the main div to the height of the current page */
oD = document.documentElement != undefined && document.documentElement.clientHeight != 0 ? document.documentElement : document.body;
iH = oD.innerHeight != undefined ? oD.innerHeight : oD.clientHeight;
document.getElementById('overflowDiv').style.height = ((iH < 300 ? 400 : iH) - 135) + 'px';
/** Width of main display */
if (document.getElementById('formWidth').value == 0)
{
	iW = oD.innerWidth != undefined ? oD.innerWidth : oD.clientWidth;
	document.getElementById('formWidth').value = (iW < 300 ? 400 : iW);
	document.getElementById('webPageFrame').style.width = (iW < 300 ? 400 : iW) - 25 + 'px';
}
/** Update the PNG file */
function showPng()
{
	document.getElementById('pngTag').onload = null;
	if (document.getElementById('pngTag').src.search(/load=1/) == -1)
	{
		/** Displays a temporary PNG */
		document.getElementById('pngTag').onload = showPng;
		document.getElementById('pngTag').src = './png.php?load=1';
		return true;
	}
	/** Set the iframe to the height of its content */
	try
	{
		var currentIframe = document.getElementById('webPageFrame');
		currentIframe.style.display = 'block';
		if (currentIframe.contentDocument && currentIframe.contentDocument.body.offsetHeight)
		{
			currentIframeContent = currentIframe.contentDocument;
			currentIframe.height = currentIframeContent.body.offsetHeight + 20;
		}
		else if (currentIframe.Document && currentIframe.Document.body.scrollHeight)
		{
			currentIframeContent = currentIframe.Document;
			currentIframe.height = currentIframeContent.body.scrollHeight + 20;
		}
		newHeight = currentIframe.height;
		currentIframe.style.display = 'inline';
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
	catch(e)
	{
		newHeight = 450;
		document.getElementById('webPageFrame').height = 450;
		document.getElementById('webPageFrame').style.display = 'inline';
	}
	/** Update the PNG */
	document.getElementById('pngTag').src = './png.php?page=' + document.getElementById('formPage').value + '&screen=' + document.getElementById('formScreen').value + '&width=' + document.getElementById('formWidth').value + '&height=' + newHeight + '&browser=' + document.getElementById('formBrowser').value + '&date=' + document.getElementById('formDate').value + '&rand=' + Date();
}
</script>
</body>
</html>