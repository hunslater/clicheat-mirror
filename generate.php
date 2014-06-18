<?php
/**
 * ClickHeat : Crée les fichiers PNG transparents et répond à l'appel Ajax / Create transparent PNG files and reply to the Ajax call
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

/** Login check */
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
$_SERVER['PHP_AUTH_USER'] !== CLICKHEAT_USER || $_SERVER['PHP_AUTH_PW'] !== CLICKHEAT_PASSWORD)
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
	$screen = 0;
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
if (!in_array($width, $screenSizes) || $width === 0)
{
	$width = $screenSizes[1];
}
$width = $width - 40;

/** Browser */
$browser = isset($_GET['browser']) ? $_GET['browser'] : '';
if (!isset($browsersList[$browser]))
{
	$browser = 'all';
}

/** Time and memory limits */
set_time_limit(120);
$memoryLimit = (int) @ini_get('memory_limit') * 1048576;
/** ini_get is not available ! Use the CLICKHEAT_MEMORY value */
if ($memoryLimit === 0)
{
	$memoryLimit = CLICKHEAT_MEMORY * 1048576;
}
if ($memoryLimit === 0)
{
	errorGenerate(LANG_ERROR_MEMORY);
}

/**
 * Memory consumption :
 * imagecreate	: about 200,000 + 5 * $width * $height bytes
 * Antialias	: double this value (because another image is used to remember the values, but this image is deleted before the call to imagepng)
 * imagepng		: about 4 * $width * $height bytes
 * So a rough idea of the memory is 10 * $width * $height + 500,000
**/
/** Calculating height from memory consumption, with a modulo of 10 */
$height = floor(($memoryLimit - 500000) / (10 * $width));
/** Limit height to 1000px max */
$height = min(1000, $height - $height % 10);

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
	errorGenerate(LANG_ERROR_PAGE);
}

/** Date */
$date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : '1970-01-01';
if (!file_exists(CLICKHEAT_LOGPATH.$page.'/'.$date.'.log'))
{
	errorGenerate(LANG_ERROR_DATA);
}

$imagePath = CLICKHEAT_LOGPATH.$page.'/'.$date.'-'.$screen.'-'.($width + 40).'-'.$browser;

/** If images are already created and older than the log file, just stop script here */
if (file_exists($imagePath.'.html') && filemtime($imagePath.'.html') >= filemtime(CLICKHEAT_LOGPATH.$page.'/'.$date.'.log'))
{
	readfile($imagePath.'.html');
	exit;
}

$nbOfImages = 1; /** Will be modified after the first image is created */
$maxClicks = 1; /** Must not be zero for divisions */
$maxAlias = 1;
for ($image = 0; $image < $nbOfImages; $image++)
{
	$minHeight = $image * $height;
	$maxHeight = ($image + 1) * $height - 1;
	/** Image creation */
	$img = imagecreatetruecolor($width, $height);
	$white = imagecolorallocate($img, 255, 255, 255);
	$black = imagecolorallocate($img, 0, 0, 0);

	/** Image is filled in the color "0", which means 0 click */
	imagefill($img, 0, 0, 0);

	/** Read clicks in the log file */
	$f = @fopen(CLICKHEAT_LOGPATH.$page.'/'.$date.'.log', 'r');
	if ($f === false)
	{
		errorGenerate(LANG_ERROR_FILE);
	}

	$maxY = 0;
	while (feof($f) === false)
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
			if ($image === 0)
			{
				/** Look for the maximum height of click */
				$maxY = max($y, $maxY);
			}
			if ($y >= $minHeight && $y < $maxHeight && $x >= 0)
			{
				/** Add 1 to the current color of this pixel (color which represents the sum of clicks on this pixel) */
				$color = imagecolorat($img, $x, $y - $height * $image) + 1;
				imagesetpixel($img, $x, $y - $height * $image, $color);
				$maxClicks = max($maxClicks, $color);
			}
		}
	}
	fclose($f);
	if ($image === 0)
	{
		$nbOfImages = ceil($maxY / $height);
	}

	$maxAlias = max($maxAlias, $maxClicks);
	/** If anti-alias is selected | This part is really slow, must be rewritten */
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

		/** Delete copied image for memory */
		imagedestroy($copy);
	}
	imagepng($img, $imagePath.'-'.$image.'.pngs');
	imagedestroy($img);
}

/**
 * Now, our image is a direct representation of the clicks on each pixel (with antialias if asked for).
 * But colors must be changed to be visible, so let's generate a clean palette of colors
 * Colors creation : grey => deep blue (rgB) => light blue (rGB) => green (rGb) => yellow (RGb) => red (Rgb), 25 colors between each of these
**/
$html = '';
for ($image = 0; $image < $nbOfImages; $image++)
{
	$img = imagecreatefrompng($imagePath.'-'.$image.'.pngs');
	unlink($imagePath.'-'.$image.'.pngs');
	imagealphablending($img, false);
	imagesavealpha($img, true);
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

	/** Colorize the image according to the new palette */
	for ($x = 0; $x < $width; $x++)
	{
		for ($y = 0; $y < $height; $y++)
		{
			imagesetpixel($img, $x, $y, $colors[ceil(imagecolorat($img, $x, $y) / $maxAlias * 109)]);
		}
	}

	/** Rainbow and maxClicks */
	if ($image === 0)
	{
		for ($i = 1; $i < 110; $i += 2)
		{
			imagefilledrectangle($img, $i/2 + 1, 0, $i/2 + 1, 10, $colors[$i]);
		}
		imagerectangle($img, 0, 0, 56, 11, $white);
		imagestring($img, 1, 1, 2, '0', $black);
		imagestring($img, 1, 56 - strlen($maxClicks) * 5, 2, $maxClicks, $black);
	}
	/** Save PNG file */
	imagepng($img, $imagePath.'-'.$image.'.png');
	imagedestroy($img);

	/** Generate HTML code */
	$html .= '<img src="./png.php?page='.$page.'&amp;date='.$date.'&amp;image='.$image.'&amp;browser='.$browser.'&amp;screen='.$screen.'&amp;width='.($width + 40).'&amp;rand='.(time() + microtime()).'" width="'.$width.'" height="'.$height.'" alt="" /><br />';
}
echo $html;

/** Save the HTML code to speed up following queries */
$f = fopen($imagePath.'.html', 'w');
fputs($f, $html);
fclose($f);
touch($imagePath.'.html');

/**
 * Retourne une erreur Ajax / Returns an Ajax error
 *
 * @param string $error
**/
function errorGenerate($error)
{
	echo '&nbsp;<br /><br /><span class="error">'.$error.'</span>';
	exit;
}
?>