<?php
/**
 * ClickHeat : Fichier PNG transparent affiché au-dessus du site pour visualiser les lieux de clic / Transparent PNG file shown over the website to display click locations
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

include './config.php';

/** Loading language according to browser's Accept-Language */
$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) : 'en';
if (!in_array($lang, $availableLanguages))
{
	$lang = $availableLanguages[0];
}
include './lang.'.$lang.'.php';

/** Loading... */
if (isset($_GET['load']))
{
	errorPng($_GET['load'] === '0' ? LANG_ERROR_PAGELOADING : LANG_ERROR_LOADING);
}

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

/** Screen size */
$screen = isset($_GET['screen']) ? (int) $_GET['screen'] : 0;
$minScreen = 0;
if (!in_array($screen, $screenSizes) || $screen === 0)
{
	$maxScreen = $screenSizes[count($screenSizes) - 1];
}
else
{
	$maxScreen = $screen;
	for ($i = 1; $i < count($screenSizes); $i++)
	{
		if ($screenSizes[$i] === $screen)
		{
			$minScreen = $screenSizes[$i - 1];
			break;
		}
	}
}

/** Width */
$width = isset($_GET['width']) ? (int) $_GET['width'] : 0;
$width = max(100, $width - 25);

/** Height */
$height = isset($_GET['height']) ? (int) $_GET['height'] : 0;
$height = max(100, $height);

/** Browser */
$browser = isset($_GET['browser']) ? $_GET['browser'] : '';
if (!isset($browsersList[$browser]))
{
	$browser = 'all';
}

/** Time and memory limits */
set_time_limit(60);
$initialMemoryLimit = ini_get('memory_limit');
@ini_set('memory_limit', CLICKHEAT_MEMORY.'M');
$memoryLimit = (int) ini_get('memory_limit') * 1048576;

/**
 * Memory consumption :
 * imagecreate	: about 200,000 + 5 * $width * $height bytes
 * Antialias	: double this value (because another image is used to remember the values, but this image is deleted before the call to imagepng)
 * imagepng		: about 4 * $width * $height bytes
**/
$memory = max((5 * $width * $height + 200000) * (CLICKHEAT_ANTIALIAS !== 0 ? 2 : 1), 9 * $width * $height + 200000);
if ($memory > $memoryLimit)
{
	errorPng(LANG_ERROR_MEMORY.number_format($memory / 1048576, 2).'M (ini_set='.ini_get('memory_limit').')');
}

/** Selected page */
$page = isset($_GET['page']) ? $_GET['page'] : '';
$d = dir(CLICKHEAT_LOGPATH);
while (($file = $d->read()) !== false)
{
	if ($file === '.' || $file === '..') continue;
	if ($file === $page)
	{
		break;
	}
}
if ($file !== $page)
{
	errorPng(LANG_ERROR_PAGE);
}

/** Date */
$date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : date('Y-m-d');
if ($date === '1970-01-01')
{
	$date = date('Y-m-d');
}
if (!file_exists(CLICKHEAT_LOGPATH.$page.'/'.$date.'.log'))
{
	errorPng(LANG_ERROR_DATA);
}

/** Image creation */
$img = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 0, 0, 0);
imagealphablending($img, false);
imagesavealpha($img, true);

/** Image is filled in the color "0", which means 0 click */
imagefill($img, 0, 0, 0);

/** Read clicks in the log file */
$f = @fopen(CLICKHEAT_LOGPATH.$page.'/'.$date.'.log', 'r');
if ($f === false)
{
	errorPng(LANG_ERROR_FILE);
}

$maxClicks = 1; /** Must not be zero for divisions */
while (!feof($f))
{
	$click = explode('|', trim(fgets($f, 1024)));
	if (count($click) === 4 && $click[2] > $minScreen && $click[2] <= $maxScreen && ($browser === 'all' || $browser === $click[3]))
	{
		/** If X-position is greater than screen size, the website is too large for the window, so we don't know where the click is... so treat is as absolute */
		if ((int) $click[0] < (int) $click[2])
		{
			$x = $width / (int) $click[2] * (int) $click[0];
		}
		elseif ((int) $click[0] <= $width)
		{
			$x = (int) $click[0];
		}
		else
		{
			$x = -1;
		}
		$y = (int) $click[1];
		if ($y < $height && $y >= 0 && $x >= 0)
		{
			/** Add 1 to the current color of this pixel (color which represents the sum of clicks on this pixel) */
			$color = imagecolorat($img, $x, $y) + 1;
			imagesetpixel($img, $x, $y, $color);
			$maxClicks = max($maxClicks, $color);
		}
	}
}
fclose($f);

$maxAlias = $maxClicks;
/** If anti-alias is selected */
if (CLICKHEAT_ANTIALIAS !== 0)
{
	/** First of all, make a copy of the current map */
	$copy = imagecreatetruecolor($width, $height);
	imagecopy($copy, $img, 0, 0, 0, 0, $width, $height);

	/** Now, apply some sort of antialias */
	$power = array();
	for ($dx = -CLICKHEAT_ANTIALIAS; $dx <= CLICKHEAT_ANTIALIAS; $dx++)
	{
		for ($dy = -CLICKHEAT_ANTIALIAS; $dy <= CLICKHEAT_ANTIALIAS; $dy++)
		{
			$power[$dx][$dy] = 2 * CLICKHEAT_ANTIALIAS - abs($dx) - abs($dy);
		}
	}

	for ($x = CLICKHEAT_ANTIALIAS; $x < $width - CLICKHEAT_ANTIALIAS; $x++)
	{
		for ($y = CLICKHEAT_ANTIALIAS; $y < $height - CLICKHEAT_ANTIALIAS; $y++)
		{
			$color = 0;
			for ($dx = -CLICKHEAT_ANTIALIAS; $dx <= CLICKHEAT_ANTIALIAS; $dx++)
			{
				for ($dy = -CLICKHEAT_ANTIALIAS; $dy <= CLICKHEAT_ANTIALIAS; $dy++)
				{
					$color += imagecolorat($copy, $x + $dx, $y + $dy) * $power[$dx][$dy];
				}
			}
			$color = ceil($color);
			imagesetpixel($img, $x, $y, $color);
			$maxAlias = max($maxAlias, $color);
		}
	}

	/** Delete copy image for memory */
	imagedestroy($copy);
}

/**
 * Now, our image is a direct representation of the clicks on each pixel (with antialias if asked for).
 * But colors must be changed to be visible, so let's generate a clean palette of colors
 * Colors creation : grey => deep blue (rgB) => light blue (rGB) => green (rGb) => yellow (RGb) => red (Rgb), 25 colors between each of these
**/
for ($i = 0; $i < 110; $i++)
{
	/** Red */
	if ($i < 10)
	{
		$red = CLICKHEAT_GREY_COLOR + (CLICKHEAT_LOW_COLOR - CLICKHEAT_GREY_COLOR) * $i / 10;
	}
	elseif ($i < 60)
	{
		$red = CLICKHEAT_LOW_COLOR;
	}
	elseif ($i < 85)
	{
		$red = CLICKHEAT_LOW_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - 60) / 35;
	}
	else
	{
		$red = CLICKHEAT_HIGH_COLOR;
	}
	/** Green */
	if ($i < 10)
	{
		$green = CLICKHEAT_GREY_COLOR + (CLICKHEAT_LOW_COLOR - CLICKHEAT_GREY_COLOR) * $i / 10;
	}
	elseif ($i < 35)
	{
		$green = CLICKHEAT_LOW_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * $i / 35;
	}
	elseif ($i < 85)
	{
		$green = CLICKHEAT_HIGH_COLOR;
	}
	else
	{
		$green = CLICKHEAT_HIGH_COLOR - (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - 85) / 35;
	}
	/** Blue */
	if ($i < 10)
	{
		$blue = CLICKHEAT_GREY_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_GREY_COLOR) * $i / 10;
	}
	elseif ($i < 35)
	{
		$blue = CLICKHEAT_HIGH_COLOR;
	}
	elseif ($i < 60)
	{
		$blue = CLICKHEAT_HIGH_COLOR - (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - 35) / 35;
	}
	else
	{
		$blue = CLICKHEAT_LOW_COLOR;
	}
	$colors[$i] = imagecolorallocatealpha($img, ceil($red), ceil($green), ceil($blue), CLICKHEAT_ALPHA);
}

for ($x = 0; $x < $width; $x++)
{
	for ($y = 0; $y < $height; $y++)
	{
		imagesetpixel($img, $x, $y, $colors[ceil(imagecolorat($img, $x, $y) / $maxAlias * 109)]);
	}
}

/** Rainbow and maxClicks */
for ($i = 1; $i < 110; $i += 2)
{
	imagefilledrectangle($img, $i/2 + 1, 0, $i/2 + 1, 10, $colors[$i]);
}
imagerectangle($img, 0, 0, 56, 11, $white);
imagestring($img, 1, 1, 2, '0', $black);
imagestring($img, 1, 56 - strlen($maxClicks) * 5, 2, $maxClicks, $black);
//header('Content-Type: image/png');
imagepng($img);
imagedestroy($img);
ini_set('memory_limit', $initialMemoryLimit);

/**
 * Retourne un PNG affichant une erreur / Returns an error as a PNG-file
 *
 * @param string $error
**/
function errorPng($error)
{
	$img = imagecreatetruecolor(450, 30);
	$red = imagecolorallocate($img, 255, 0, 0);
	$white = imagecolorallocate($img, 255, 255, 255);
	imagefill($img, 0, 0, $red);
	imagestring($img, 2, 10, 10, utf8_decode($error), $white);
	header('Content-Type: image/png');
	imagepng($img);
	exit;
}
?>