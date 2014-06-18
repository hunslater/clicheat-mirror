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
include './login.php';

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
@set_time_limit(120);
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

/** Show clicks or heatmap */
$heatmap = isset($_GET['heatmap']) ? (int) $_GET['heatmap']: 0;

/** Date and days */
$dateStamp = isset($_GET['date']) ? strtotime($_GET['date']) : time();
$date = date('Y-m-d', $dateStamp);
$days = isset($_GET['days']) ? (int) $_GET['days'] : 1;

$imagePath = CLICKHEAT_LOGPATH.$page.'/'.$date.'-'.$days.'-'.$screen.'-'.($width + 40).'-'.$browser.'-'.$heatmap;

/** If images are already created and older than the current day, just stop script here */
if (file_exists($imagePath.'.html') && filemtime($imagePath.'.html') > mktime(23, 59, 59, date('m', $dateStamp), date('d', $dateStamp) + $days - 1, date('Y', $dateStamp)))
{
	readfile($imagePath.'.html');
	exit;
}

$nbOfImages = 1; /** Will be modified after the first image is created */
$maxClicks = 1; /** Must not be zero for divisions */
$maxAlias = 1;
$html = '';
for ($image = 0; $image < $nbOfImages; $image++)
{
	$minHeight = $image * $height;
	$maxHeight = ($image + 1) * $height - 1;
	/** Image creation */
	$img = imagecreatetruecolor($width, $height);
	if ($heatmap === 0)
	{
		$red = imagecolorallocate($img, 255, 0, 0);
		$grey = imagecolorallocatealpha($img, CLICKHEAT_GREY_COLOR, CLICKHEAT_GREY_COLOR, CLICKHEAT_GREY_COLOR, CLICKHEAT_ALPHA);
		imagealphablending($img, false);
		imagesavealpha($img, true);
		imagefill($img, 0, 0, $grey);
	}
	else
	{
		/** Image is filled in the color "0", which means 0 click */
		imagefill($img, 0, 0, 0);
	}

	$maxY = 0;
	for ($day = 0; $day < $days; $day++)
	{
		$currentDate = date('Y-m-d', mktime(0, 0, 0, date('m', $dateStamp), date('d', $dateStamp) + $day, date('Y', $dateStamp)));
		if (!file_exists(CLICKHEAT_LOGPATH.$page.'/'.$currentDate.'.log'))
		{
			continue;
		}
		/** Read clicks in the log file */
		$f = @fopen(CLICKHEAT_LOGPATH.$page.'/'.$currentDate.'.log', 'r');
		if ($f === false)
		{
			errorGenerate(LANG_ERROR_FILE.': '.CLICKHEAT_LOGPATH.$page.'/'.$currentDate.'.log');
		}

		while (feof($f) === false)
		{
			$click = explode('|', trim(fgets($f, 1024)));
			if (count($click) === 4 && $click[2] > $minScreen && $click[2] <= $maxScreen && ($browser === 'all' || $browser === $click[3]))
			{
				/** If X-position is greater than screen size, the website is too large for the window, so we don't know where the click is... so treat is as absolute */
				$click[0] = (int) $click[0];
				$click[1] = (int) $click[1];
				$click[2] = (int) $click[2];
				if ($click[0] < $click[2])
				{
					$x = ceil($width / $click[2] * $click[0]);
				}
				elseif ($click[0] <= $width)
				{
					$x = $click[0];
				}
				else
				{
					$x = -1;
				}
				$y = $click[1];
				if ($image === 0)
				{
					/** Look for the maximum height of click */
					$maxY = max($y, $maxY);
				}
				if ($y >= $minHeight && $y < $maxHeight && $x >= 0)
				{
					if ($heatmap === 1)
					{
						/** Add 10 to the current color of this pixel (color which represents the sum of clicks on this pixel multiplied by 10, because of the PHP gaussian filter that reduces a lot the color of a lonely pixel) */
						$color = imagecolorat($img, $x, $y - $height * $image) + 10;
						imagesetpixel($img, $x, $y - $height * $image, $color);
						$maxClicks = max($maxClicks, $color / 10);
					}
					else
					{
						/** Put a red cross at the click location */
						imageline($img, $x - 2, $y - $height * $image - 2, $x + 2, $y - $height * $image + 2, $red);
						imageline($img, $x + 2, $y - $height * $image - 2, $x - 2, $y - $height * $image + 2, $red);
					}
				}
			}
		}
		fclose($f);
	}
	if ($image === 0)
	{
		if ($maxY === 0)
		{
			errorGenerate(LANG_ERROR_DATA);
		}
		$nbOfImages = ceil($maxY / $height);
	}

	/** If anti-alias is selected and PHP is 5+ */
	if ($heatmap === 1 && CLICKHEAT_ANTIALIAS === true && function_exists('imagefilter'))
	{
		imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
		/** Maximum color value must be evaluated again */
		for ($x = 0; $x < $width; $x++)
		{
			for ($y = 0; $y < $height; $y++)
			{
				$maxAlias = max(imagecolorat($img, $x, $y), $maxAlias);
			}
		}
	}
	else
	{
		$maxAlias = $maxClicks * 10;
	}

	if ($heatmap === 1)
	{
		imagepng($img, $imagePath.'-'.$image.'.pngs');
	}
	else
	{
		imagepng($img, $imagePath.'-'.$image.'.png');
	}
	imagedestroy($img);

	/** Generate HTML code */
	$html .= '<img src="./png.php?page='.$page.'&amp;date='.$date.'&amp;days='.$days.'&amp;image='.$image.'&amp;browser='.$browser.'&amp;screen='.$screen.'&amp;width='.($width + 40).'&amp;heatmap='.$heatmap.'&amp;rand='.(time() + microtime()).'" width="'.$width.'" height="'.$height.'" alt="" /><br />';
}

/**
 * Now, our image is a direct representation of the clicks on each pixel (with antialias if asked for).
 * But colors must be changed to be visible, so let's generate a clean palette of colors
 * Colors creation : grey => deep blue (rgB) => light blue (rGB) => green (rGb) => yellow (RGb) => red (Rgb), 25 colors between each of these
**/
for ($image = 0; $image < $nbOfImages && $heatmap === 1; $image++)
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
		$white = imagecolorallocate($img, 255, 255, 255);
		$black = imagecolorallocate($img, 0, 0, 0);
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