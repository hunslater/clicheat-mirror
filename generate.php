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

/** Against people that just don't understand that a demo is not a tool to do harm to servers... */
$demoServer = strpos($_SERVER['SERVER_NAME'], '.labsmedia.com') !== false;

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
 * dots			: about 6,000 + 360 * CLICKHEAT_DOT_WIDTH bytes each (100 dots)
 * imagepng		: about 4 * $width * $height bytes
 * So a rough idea of the memory is 10 * $width * $height + 500,000 (2 images) + 100 * (CLICKHEAT_DOT_WIDTH * 360 + 6000)
**/
/** Calculating height from memory consumption, and add a 50% security margin : 10 => 15 */
$height = floor(($memoryLimit - 500000 - 100 * (CLICKHEAT_DOT_WIDTH * 360 + 6000)) / (15 * $width));
/** Limit height to 1000px max, with a modulo of 10 */
$height = max(100, min(1000, $height - $height % 10));

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
/** Get some data for the current page (centered and/or fixed layout) */
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
$webPage = explode('>', $webPage);
if (count($webPage) !== 4)
{
	$leftWidth = 0;
	$centerWidth = 0;
	$rightWidth = 0;
}
else
{
	$leftWidth = (int) $webPage[1];
	$centerWidth = (int) $webPage[2];
	$rightWidth = (int) $webPage[3];
}
if ($leftWidth !== 0 && $centerWidth !== 0 && $rightWidth === 0)
{
	/** Fixed left menu and fixed center ? Fall back to a fixed left menu only */
	$leftWidth += $centerWidth;
	$centerWidth = 0;
}
elseif ($leftWidth === 0 && $centerWidth !== 0 && $rightWidth !== 0)
{
	/** Fixed right menu and fixed center ? Fall back to a fixed right menu only */
	$rightWidth += $centerWidth;
	$centerWidth = 0;
}
elseif ($leftWidth !== 0 && $centerWidth !== 0 && $rightWidth !== 0)
{
	/** Everything is fixed ?? */
	errorGenerate(LANG_ERROR_FIXED);
}
unset($webPage);

/** Show clicks or heatmap */
$heatmap = isset($_GET['heatmap']) ? (int) $_GET['heatmap']: 0;

/** Date and days */
$dateStamp = isset($_GET['date']) ? strtotime($_GET['date']) : time();
$date = date('Y-m-d', $dateStamp);
$days = isset($_GET['days']) ? (int) $_GET['days'] : 1;

/** Against rude people on my demo server */
if ($date === date('Y-m-d') && $demoServer === true)
{
	errorGenerate(LANG_ERROR_TODAY);
}

$imagePath = CLICKHEAT_LOGPATH.$page.'/%%'.$date.'-'.$days.'-'.$screen.'-'.($width + 40).'-'.$browser.'-'.$heatmap;

/** If images are already created and older than the current day, just stop script here */
if (file_exists($imagePath.'.html%%') && filemtime($imagePath.'.html%%') > mktime(23, 59, 59, date('m', $dateStamp), date('d', $dateStamp) + $days - 1, date('Y', $dateStamp)))
{
	readfile($imagePath.'.html%%');
	exit;
}

$nbOfImages = 1; /** Will be modified after the first image is created */
$maxClicks = 1; /** Must not be zero for divisions */
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
		if (!file_exists(CLICKHEAT_LOGPATH.$page.'/%%'.$currentDate.'.log%%'))
		{
			continue;
		}
		/** Read clicks in the log file */
		$f = @fopen(CLICKHEAT_LOGPATH.$page.'/%%'.$currentDate.'.log%%', 'r');
		if ($f === false)
		{
			errorGenerate(LANG_ERROR_FILE.': '.CLICKHEAT_LOGPATH.$page.'/'.$currentDate.'.log');
		}

		while (feof($f) === false)
		{
			$click = explode('|', trim(fgets($f, 1024)));
			if (count($click) === 4 && $click[2] > $minScreen && $click[2] <= $maxScreen && ($browser === 'all' || $browser === $click[3]))
			{
				$click[0] = (int) $click[0]; // X
				$click[1] = (int) $click[1]; // Y
				$click[2] = (int) $click[2]; // display width
				if ($click[0] > $click[2])
				{
					/** If X is greater than screen size, the website is too large for the window, so we don't know where the click is... ignore it */
					continue;
				}
				/** Correction of X for liquid and/or fixed layouts */
				if ($leftWidth !== 0 && $centerWidth === 0 && $rightWidth === 0)
				{
					/** Left fixed menu */
					if ($click[0] <= $leftWidth)
					{
						/** Click in the left menu : X is good */
						$x = $click[0];
					}
					else
					{
						/** Apply a percentage on the rest of the screen */
						$x = $leftWidth + ceil(($width - $leftWidth) * ($click[0] - $leftWidth) / ($click[2] - $leftWidth));
					}
				}
				elseif ($leftWidth === 0 && $centerWidth === 0 && $rightWidth !== 0)
				{
					/** Right fixed menu */
					if ($click[2] - $click[0] <= $rightWidth)
					{
						/** Click in the right menu : X is good, but relative to the right border */
						$x = $width - ($click[2] - $click[0]);
					}
					else
					{
						/** Apply a percentage on the rest of the screen */
						$x = ceil(($width - $rightWidth) * $click[0] / ($click[2] - $rightWidth));
					}
				}
				elseif ($leftWidth !== 0 && $centerWidth === 0 && $rightWidth !== 0)
				{
					/** Left and right fixed menus */
					if ($click[2] - $click[0] <= $rightWidth)
					{
						/** Click in the right menu : X is good, but relative to the right border */
						$x = $width - ($click[2] - $click[0]);
					}
					elseif ($click[0] <= $leftWidth)
					{
						/** Click in the left menu : X is good */
						$x = $click[0];
					}
					else
					{
						/** Apply a percentage on the rest of the screen */
						$x = $leftWidth + ceil(($width - $leftWidth - $rightWidth) * ($click[0] - $leftWidth) / ($click[2] - $leftWidth - $rightWidth));
					}
				}
				elseif ($leftWidth === 0 && $centerWidth !== 0 && $rightWidth === 0)
				{
					/** Fixed centered content */
					if (abs($click[0] - $click[2] / 2) <= $centerWidth / 2)
					{
						/** Click is in the centered content */
						$x = ($width - $click[2]) / 2 + $click[0];
					}
					elseif ($click[0] < $click[2] / 2)
					{
						/** Click is at the left of the centered content */
						$x = ($width - $centerWidth) / ($click[2] - $centerWidth) * $click[0];
					}
					else
					{
						/** Click is at the right of the centered content */
						$x = ($width + $centerWidth) / 2 + ($width - $centerWidth) / ($click[2] - $centerWidth) * ($click[0] - ($click[2] + $centerWidth) / 2);
					}
				}
				else
				{
					/** Layout 100% */
					$x = $width / $click[2] * $click[0];
				}
				$y = $click[1];
				if ($image === 0)
				{
					/** Looking for the maximum height of click */
					$maxY = max($y, $maxY);
				}
				if ($y >= $minHeight && $y < $maxHeight && $x >= 0 && $x <= $width)
				{
					if ($heatmap === 1)
					{
						/** Add 1 to the current color of this pixel (color which represents the sum of clicks on this pixel) */
						$color = imagecolorat($img, $x, $y - $height * $image) + 1;
						imagesetpixel($img, $x, $y - $height * $image, $color);
						$maxClicks = max($maxClicks, $color);
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

	if ($heatmap === 1)
	{
		imagepng($img, $imagePath.'-'.$image.'.pngs%%');
	}
	else
	{
		imagepng($img, $imagePath.'-'.$image.'.png%%');
	}
	imagedestroy($img);

	/** Generate HTML code */
	$html .= '<img src="./png.php?page='.$page.'&amp;date='.$date.'&amp;days='.$days.'&amp;image='.$image.'&amp;browser='.$browser.'&amp;screen='.$screen.'&amp;width='.($width + 40).'&amp;heatmap='.$heatmap.'&amp;rand='.(time() + microtime()).'" width="'.$width.'" height="'.$height.'" alt="" /><br />';
}

/** Now, our image is a direct representation of the clicks on each pixel, so create some fuzzy dots to put a nice blur effect if user asks for a heatmap */
if ($heatmap === 1)
{
	for ($i = 0; $i < 100; $i++)
	{
		$dots[$i] = imagecreatetruecolor(CLICKHEAT_DOT_WIDTH, CLICKHEAT_DOT_WIDTH);
		imagealphablending($dots[$i], false);
	}
	for ($x = 0; $x < CLICKHEAT_DOT_WIDTH; $x++)
	{
		for ($y = 0; $y < CLICKHEAT_DOT_WIDTH; $y++)
		{
			$sinX = sin($x * pi() / CLICKHEAT_DOT_WIDTH);
			$sinY = sin($y * pi() / CLICKHEAT_DOT_WIDTH);
			for ($i = 0; $i < 100; $i++)
			{
				/** Alpha range is only 27 => 127 to limit the effect on nearby pixels */
				$alpha = 127 - $i * $sinX * $sinY * $sinX * $sinY;
				imagesetpixel($dots[$i], $x, $y, ((int) $alpha) * 16777216);
			}
		}
	}
	/**
	 * Colors creation :
	 * grey	=> deep blue (rgB)	=> light blue (rGB)	=> green (rGb)		=> yellow (RGb)		=> red (Rgb)
	 * 0	   $colorLevels[0]	   $colorLevels[1]	   $colorLevels[2]	   $colorLevels[3]	   128
	**/
	sort($colorLevels);
	$colors = array();
	for ($i = 0; $i < 128; $i++)
	{
		/** Red */
		if ($i < $colorLevels[0])
		{
			$colors[$i][0] = CLICKHEAT_GREY_COLOR + (CLICKHEAT_LOW_COLOR - CLICKHEAT_GREY_COLOR) * $i / $colorLevels[0];
		}
		elseif ($i < $colorLevels[2])
		{
			$colors[$i][0] = CLICKHEAT_LOW_COLOR;
		}
		elseif ($i < $colorLevels[3])
		{
			$colors[$i][0] = CLICKHEAT_LOW_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $colorLevels[2]) / ($colorLevels[3] - $colorLevels[2]);
		}
		else
		{
			$colors[$i][0] = CLICKHEAT_HIGH_COLOR;
		}
		/** Green */
		if ($i < $colorLevels[0])
		{
			$colors[$i][1] = CLICKHEAT_GREY_COLOR + (CLICKHEAT_LOW_COLOR - CLICKHEAT_GREY_COLOR) * $i / $colorLevels[0];
		}
		elseif ($i < $colorLevels[1])
		{
			$colors[$i][1] = CLICKHEAT_LOW_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $colorLevels[0]) / ($colorLevels[1] - $colorLevels[0]);
		}
		elseif ($i < $colorLevels[3])
		{
			$colors[$i][1] = CLICKHEAT_HIGH_COLOR;
		}
		else
		{
			$colors[$i][1] = CLICKHEAT_HIGH_COLOR - (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $colorLevels[3]) / (127 - $colorLevels[3]);
		}
		/** Blue */
		if ($i < $colorLevels[0])
		{
			$colors[$i][2] = CLICKHEAT_GREY_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_GREY_COLOR) * $i / $colorLevels[0];
		}
		elseif ($i < $colorLevels[1])
		{
			$colors[$i][2] = CLICKHEAT_HIGH_COLOR;
		}
		elseif ($i < $colorLevels[2])
		{
			$colors[$i][2] = CLICKHEAT_HIGH_COLOR - (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $colorLevels[1]) / ($colorLevels[2] - $colorLevels[1]);
		}
		else
		{
			$colors[$i][2] = CLICKHEAT_LOW_COLOR;
		}
	}
	for ($image = 0; $image < $nbOfImages; $image++)
	{
		$img = imagecreatetruecolor($width, $height);
		imagesavealpha($img, true);
		/** We don't use imagefill() because this function is buggy on the French host Free.fr */
		imagealphablending($img, false);
		imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, 0x7FFFFFFF);
		imagealphablending($img, true);

		$imgSrc = imagecreatefrompng($imagePath.'-'.$image.'.pngs%%');
		unlink($imagePath.'-'.$image.'.pngs%%');
		for ($x = 0; $x < $width; $x++)
		{
			for ($y = 0; $y < $height; $y++)
			{
				$dot = (int) ceil(imagecolorat($imgSrc, $x, $y) / $maxClicks * 99);
				if ($dot !== 0)
				{
					imagecopy($img, $dots[$dot], ceil($x - CLICKHEAT_DOT_WIDTH / 2), ceil($y - CLICKHEAT_DOT_WIDTH / 2), 0, 0, CLICKHEAT_DOT_WIDTH, CLICKHEAT_DOT_WIDTH);
				}
			}
		}
		/** Destroy image source */
		imagedestroy($imgSrc);

		/** Change the palette, and create the 128 colors */
		$allocatedColors = array();
		for ($i = 0; $i < 128; $i++)
		{
			$allocatedColors[$i] = imagecolorallocatealpha($img, $colors[$i][0], $colors[$i][1], $colors[$i][2], CLICKHEAT_ALPHA);
		}
		imagealphablending($img, false);
		for ($x = 0; $x < $width; $x++)
		{
			for ($y = 0; $y < $height; $y++)
			{
				/** Set a pixel with the new color, while reading the current alpha level */
				imagesetpixel($img, $x, $y, $allocatedColors[127 - ((imagecolorat($img, $x, $y) & 0x7F000000) >> 24)]);
			}
		}

		/** Rainbow and maxClicks */
		if ($image === 0)
		{
			$white = imagecolorallocate($img, 255, 255, 255);
			$black = imagecolorallocate($img, 0, 0, 0);
			for ($i = 1; $i < 128; $i += 2)
			{
				imagefilledrectangle($img, $i/2 + 1, 0, $i/2 + 1, 10, $allocatedColors[$i]);
			}
			imagerectangle($img, 0, 0, 65, 11, $white);
			imagestring($img, 1, 1, 2, '0', $black);
			imagestring($img, 1, 65 - strlen($maxClicks) * 5, 2, $maxClicks, $black);
		}
		/** Save PNG file */
		imagepng($img, $imagePath.'-'.$image.'.png%%');
		imagedestroy($img);
	}
	for ($i = 0; $i < 100; $i++)
	{
		imagedestroy($dots[$i]);
	}
}
echo $html;

/** Save the HTML code to speed up following queries */
$f = fopen($imagePath.'.html%%', 'w');
fputs($f, $html);
fclose($f);
touch($imagePath.'.html%%');

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