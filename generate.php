<?php
/**
 * ClickHeat : Crée les fichiers PNG transparents et répond à l'appel Ajax / Create transparent PNG files and reply to the Ajax call
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

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
$memoryLimit = (int) @ini_get('memory_limit') * 1048576;
/** ini_get is not available ! Use the CLICKHEAT_MEMORY value */
if ($memoryLimit === 0)
{
	$memoryLimit = $clickheatConf['memory'] * 1048576;
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
/** Calculating height from memory consumption, and add a 100% security margin : 10 => 20 */
$height = floor(($memoryLimit - 500000 - 100 * ($clickheatConf['dot'] * 360 + 6000)) / (20 * $width));
/** Limit height to 1000px max, with a modulo of 10 */
$height = max(100, min(1000, $height - $height % 10));

/** Selected page */
$page = isset($_GET['page']) ? str_replace(array('.', '/'), array('', ''), $_GET['page']) : '****dead_directory****';
if (!is_dir($clickheatConf['logPath'].$page))
{
	errorGenerate(LANG_ERROR_GROUP);
}
/** Get some data for the current page (centered and/or fixed layout) */
if (file_exists($clickheatConf['logPath'].$page.'/%%url.txt%%'))
{
	$f = @fopen($clickheatConf['logPath'].$page.'/%%url.txt%%', 'r');
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
$range = isset($_GET['range']) && in_array($_GET['range'], array('d', 'w', 'm')) ? $_GET['range'] : 'd';
$days = $range === 'd' ? 1 : ($range === 'w' ? 7 : date('t', $dateStamp));
$date = date('Y-m-d', $dateStamp);

$imagePath = $clickheatConf['logPath'].$page.'/%%'.$date.'-'.$range.'-'.$screen.'-'.$browser.'-'.$heatmap;

/** If images are already created, just stop script here if these have less than 120 seconds */
if (file_exists($imagePath.'.html%%') && filemtime($imagePath.'.html%%') > time() - 120)
{
	readfile($imagePath.'.html%%');
	exit;
}

$startStep = floor(($clickheatConf['step'] - 1) / 2);
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
		$green = imagecolorallocate($img, 0, 220, 0);
		$grey = imagecolorallocate($img, CLICKHEAT_GREY_COLOR, CLICKHEAT_GREY_COLOR, CLICKHEAT_GREY_COLOR);
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
		if (!file_exists($clickheatConf['logPath'].$page.'/%%'.$currentDate.'.log%%'))
		{
			continue;
		}
		/** Read clicks in the log file */
		$f = @fopen($clickheatConf['logPath'].$page.'/%%'.$currentDate.'.log%%', 'r');
		if ($f === false)
		{
			errorGenerate(LANG_ERROR_FILE.': '.$clickheatConf['logPath'].$page.'/%%'.$currentDate.'.log%%');
		}

		$buffer = '';
		$count = 0;
		while (true)
		{
			$buffer .= fgets($f, 1024);
			/** Grouping by 1000 clicks */
			if (feof($f) === false && $count++ !== 1000)
			{
				continue;
			}
			/** Do a regular match (faster and easier for large volume of data) */
			preg_match_all('~^(\d+)\|(\d+)\|(\d+)\|'.($browser === 'all' ? '[a-z]+' : $browser).'\|(\d)$~m', $buffer, $clicks);
			$buffer = '';

			for ($i = 0, $max = count($clicks[1]); $i < $max; $i++)
			{
				$x = (int) $clicks[1][$i]; // X
				$y = (int) $clicks[2][$i]; // Y
				$w = (int) $clicks[3][$i]; // display width
				$c = ($clicks[4][$i] < 3); // left click
				/** X is not in the range of sizes, or right click for heatmap, or X is greater than screen size, the website is too large for the window, so we don't know where the click is... ignore those clicks */
				if ($x <= $minScreen || $x > $maxScreen || $heatmap === 1 && $c === false || $x > $w)
				{
					continue;
				}
				/** Correction of X for liquid and/or fixed layouts */
				if ($leftWidth !== 0 && $centerWidth === 0 && $rightWidth === 0)
				{
					/** Left fixed menu */
					if ($x <= $leftWidth)
					{
						/** Click in the left menu : X is good */
					}
					else
					{
						/** Apply a percentage on the rest of the screen */
						$x = $leftWidth + ceil(($width - $leftWidth) * ($x - $leftWidth) / ($w - $leftWidth));
					}
				}
				elseif ($leftWidth === 0 && $centerWidth === 0 && $rightWidth !== 0)
				{
					/** Right fixed menu */
					if ($w - $x <= $rightWidth)
					{
						/** Click in the right menu : X is good, but relative to the right border */
						$x = $width - ($w - $x);
					}
					else
					{
						/** Apply a percentage on the rest of the screen */
						$x = ceil(($width - $rightWidth) * $x / ($w - $rightWidth));
					}
				}
				elseif ($leftWidth !== 0 && $centerWidth === 0 && $rightWidth !== 0)
				{
					/** Left and right fixed menus */
					if ($w - $x <= $rightWidth)
					{
						/** Click in the right menu : X is good, but relative to the right border */
						$x = $width - ($w - $x);
					}
					elseif ($x <= $leftWidth)
					{
						/** Click in the left menu : X is good */
					}
					else
					{
						/** Apply a percentage on the rest of the screen */
						$x = $leftWidth + ceil(($width - $leftWidth - $rightWidth) * ($x - $leftWidth) / ($w - $leftWidth - $rightWidth));
					}
				}
				elseif ($leftWidth === 0 && $centerWidth !== 0 && $rightWidth === 0)
				{
					/** Fixed centered content */
					if (abs($x - $w / 2) <= $centerWidth / 2)
					{
						/** Click is in the centered content */
						$x = ($width - $w) / 2 + $x;
					}
					elseif ($x < $w / 2)
					{
						/** Click is at the left of the centered content */
						$x = ($width - $centerWidth) / ($w - $centerWidth) * $x;
					}
					else
					{
						/** Click is at the right of the centered content */
						$x = ($width + $centerWidth) / 2 + ($width - $centerWidth) / ($w - $centerWidth) * ($x - ($w + $centerWidth) / 2);
					}
				}
				else
				{
					/** Layout 100% */
					$x = $width / $w * $x;
				}
				if ($image === 0)
				{
					/** Looking for the maximum height of click */
					$maxY = max($y, $maxY);
				}
				if ($y >= $minHeight && $y < $maxHeight && $x >= 0 && $x <= $width)
				{
					if ($heatmap === 1)
					{
						/** Apply a calculus for the step, with increases the speed of rendering : step = 3, then pixel is drawn at x = 2 (center of a 3x3 square) */
						$x -= $x % $clickheatConf['step'] - $startStep;
						$y -= $y % $clickheatConf['step'] - $startStep;
						/** Add 1 to the current color of this pixel (color which represents the sum of clicks on this pixel) */
						$color = imagecolorat($img, $x, $y - $height * $image) + 1;
						imagesetpixel($img, $x, $y - $height * $image, $color);
						$maxClicks = max($maxClicks, $color);
					}
					else
					{
						/** Put a red or green cross at the click location */
						$color = $c === true ? $red : $green;
						imageline($img, $x - 2, $y - $height * $image - 2, $x + 2, $y - $height * $image + 2, $color);
						imageline($img, $x + 2, $y - $height * $image - 2, $x - 2, $y - $height * $image + 2, $color);
					}
				}
			}
			unset($clicks);

			if ($count !== 1001)
			{
				break;
			}
			$count = 0;
		}
		fclose($f);
	}
	if ($image === 0)
	{
		if ($maxY === 0)
		{
			errorGenerate(LANG_ERROR_DATA);
		}
		$nbOfImages = (int) ceil($maxY / $height);
	}

	if ($heatmap === 1)
	{
		imagepng($img, $imagePath.'-'.$image.'.pngs%%');
	}
	else
	{
		/** "No clicks under this line" message */
		if ($image === $nbOfImages - 1)
		{
			$black = imagecolorallocate($img, 0, 0, 0);
			imageline($img, 0, $height - 1, $width, $height - 1, $black);
			imagestring($img, 1, 1, $height - 9, LANG_NO_CLICK_BELOW, $black);
		}
		imagepng($img, $imagePath.'-'.$image.'.png%%');
	}
	imagedestroy($img);

	/** Generate HTML code */
	$html .= '<img id="heatmap-'.$image.'" src="'.CLICKHEAT_INDEX_PATH.'action=png&page='.$page.'&amp;date='.$date.'&amp;range='.$range.'&amp;image='.$image.'&amp;browser='.$browser.'&amp;screen='.$screen.'&amp;heatmap='.$heatmap.'&amp;rand='.(time() + microtime()).'" width="'.$width.'" height="'.$height.'" alt="" /><br />';
}

/** Now, our image is a direct representation of the clicks on each pixel, so create some fuzzy dots to put a nice blur effect if user asked for a heatmap */
if ($heatmap === 1)
{
	for ($i = 0; $i < 128; $i++)
	{
		$dots[$i] = imagecreatetruecolor($clickheatConf['dot'], $clickheatConf['dot']);
		imagealphablending($dots[$i], false);
	}
	for ($x = 0; $x < $clickheatConf['dot']; $x++)
	{
		for ($y = 0; $y < $clickheatConf['dot']; $y++)
		{
			$sinX = sin($x * pi() / $clickheatConf['dot']);
			$sinY = sin($y * pi() / $clickheatConf['dot']);
			for ($i = 0; $i < 128; $i++)
			{
				$alpha = 127 - $i * $sinX * $sinY * $sinX * $sinY;
				imagesetpixel($dots[$i], $x, $y, ((int) $alpha) * 16777216);
			}
		}
	}

	/**
	 * Colors creation :
	 * grey	=> deep blue (rgB)	=> light blue (rGB)	=> green (rGb)		=> yellow (RGb)		=> red (Rgb)
	 * 0	   $__colorLevels[0]	   $__colorLevels[1]	   $__colorLevels[2]	   $__colorLevels[3]	   128
	**/
	sort($__colorLevels);
	$colors = array();
	for ($i = 0; $i < 128; $i++)
	{
		/** Red */
		if ($i < $__colorLevels[0])
		{
			$colors[$i][0] = CLICKHEAT_GREY_COLOR + (CLICKHEAT_LOW_COLOR - CLICKHEAT_GREY_COLOR) * $i / $__colorLevels[0];
		}
		elseif ($i < $__colorLevels[2])
		{
			$colors[$i][0] = CLICKHEAT_LOW_COLOR;
		}
		elseif ($i < $__colorLevels[3])
		{
			$colors[$i][0] = CLICKHEAT_LOW_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $__colorLevels[2]) / ($__colorLevels[3] - $__colorLevels[2]);
		}
		else
		{
			$colors[$i][0] = CLICKHEAT_HIGH_COLOR;
		}
		/** Green */
		if ($i < $__colorLevels[0])
		{
			$colors[$i][1] = CLICKHEAT_GREY_COLOR + (CLICKHEAT_LOW_COLOR - CLICKHEAT_GREY_COLOR) * $i / $__colorLevels[0];
		}
		elseif ($i < $__colorLevels[1])
		{
			$colors[$i][1] = CLICKHEAT_LOW_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $__colorLevels[0]) / ($__colorLevels[1] - $__colorLevels[0]);
		}
		elseif ($i < $__colorLevels[3])
		{
			$colors[$i][1] = CLICKHEAT_HIGH_COLOR;
		}
		else
		{
			$colors[$i][1] = CLICKHEAT_HIGH_COLOR - (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $__colorLevels[3]) / (127 - $__colorLevels[3]);
		}
		/** Blue */
		if ($i < $__colorLevels[0])
		{
			$colors[$i][2] = CLICKHEAT_GREY_COLOR + (CLICKHEAT_HIGH_COLOR - CLICKHEAT_GREY_COLOR) * $i / $__colorLevels[0];
		}
		elseif ($i < $__colorLevels[1])
		{
			$colors[$i][2] = CLICKHEAT_HIGH_COLOR;
		}
		elseif ($i < $__colorLevels[2])
		{
			$colors[$i][2] = CLICKHEAT_HIGH_COLOR - (CLICKHEAT_HIGH_COLOR - CLICKHEAT_LOW_COLOR) * ($i - $__colorLevels[1]) / ($__colorLevels[2] - $__colorLevels[1]);
		}
		else
		{
			$colors[$i][2] = CLICKHEAT_LOW_COLOR;
		}
	}
	for ($image = 0; $image < $nbOfImages; $image++)
	{
		$img = imagecreatetruecolor($width, $height);
		/** We don't use imagefill() because this function is buggy on the French host Free.fr */
		imagealphablending($img, false);
		imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, 0x7FFFFFFF);
		imagealphablending($img, true);

		$imgSrc = imagecreatefrompng($imagePath.'-'.$image.'.pngs%%');
		@unlink($imagePath.'-'.$image.'.pngs%%');
		if ($imgSrc === false)
		{
			errorGenerate('::MEMORY_OVERFLOW::');
		}
		for ($x = $startStep; $x < $width; $x += $clickheatConf['step'])
		{
			for ($y = $startStep; $y < $height; $y += $clickheatConf['step'])
			{
				$dot = (int) ceil(imagecolorat($imgSrc, $x, $y) / $maxClicks * 127);
				if ($dot !== 0)
				{
					imagecopy($img, $dots[$dot], ceil($x - $clickheatConf['dot'] / 2), ceil($y - $clickheatConf['dot'] / 2), 0, 0, $clickheatConf['dot'], $clickheatConf['dot']);
				}
			}
		}
		/** Destroy image source */
		imagedestroy($imgSrc);

		/** Rainbow */
		if ($image === 0)
		{
			for ($i = 1; $i < 128; $i += 2)
			{
				imagefilledrectangle($img, $i/2 + 1, 0, $i/2 + 1, 10, (127 - $i) * 16777216);
			}
		}

		/** Some version of imagetruecolortopalette() don't transform alpha value to non alpha */
		if ($clickheatConf['palette'] === true)
		{
			for ($x = 0; $x < $width; $x++)
			{
				for ($y = 0; $y < $height; $y++)
				{
					/** Get Alpha value (0->127) and transform it to red (divide color by 16777216 and multiply by 65536 * 2 (red is 0->255), so divide it by 128) */
					imagesetpixel($img, $x, $y, (imagecolorat($img, $x, $y) & 0x7F000000) / 128);
				}
			}
		}

		/** Transform true-color to palette, then change palette colors */
		imagetruecolortopalette($img, false, 128);
		for ($i = 0, $max = imagecolorstotal($img); $i < $max; $i++)
		{
			$color = imagecolorsforindex($img, $i);
			imagecolorset($img, $i, $colors[floor(127 - $color['red'] / 2)][0], $colors[floor(127 - $color['red'] / 2)][1], $colors[floor(127 - $color['red'] / 2)][2]);
		}

		/** maxClicks */
		if ($image === 0)
		{
			$white = imagecolorallocate($img, 255, 255, 255);
			$black = imagecolorallocate($img, 0, 0, 0);
			imagerectangle($img, 0, 0, 65, 11, $white);
			imagestring($img, 1, 1, 2, '0', $black);
			imagestring($img, 1, 65 - strlen($maxClicks) * 5, 2, $maxClicks, $black);
		}

		/** "No clicks under this line" message */
		if ($image === $nbOfImages - 1)
		{
			$black = imagecolorallocate($img, 0, 0, 0);
			imageline($img, 0, $height - 1, $width, $height - 1, $black);
			imagestring($img, 1, 1, $height - 9, LANG_NO_CLICK_BELOW, $black);
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

/** Save the HTML code to speed up following queries (only over two minutes) */
$f = fopen($imagePath.'.html%%', 'w');
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