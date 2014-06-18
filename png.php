<?php
/**
 * ClickHeat : Renvoie un fichier PNG / Returns a transparent PNG
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 04/12/2006
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

$page = isset($_GET['page']) ? str_replace(array('.', '/'), array('', ''), $_GET['page']) : '';
$date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : '1970-01-01';
$days = isset($_GET['days']) ? (int) $_GET['days'] : 1;
$browser = isset($_GET['browser']) && isset($browsersList[$_GET['browser']]) ? $_GET['browser'] : '';
$width = isset($_GET['width']) && in_array($_GET['width'], $screenSizes) ? (int) $_GET['width'] : 0;
$screen = isset($_GET['screen']) && in_array($_GET['screen'], $screenSizes) ? (int) $_GET['screen'] : 0;
$image = isset($_GET['image']) ? (int) $_GET['image'] : 0;
$heatmap = isset($_GET['heatmap']) ? (int) $_GET['heatmap'] : 0;

$imagePath = CLICKHEAT_LOGPATH.$page.'/%%'.$date.'-'.$days.'-'.$screen.'-'.$width.'-'.$browser.'-'.$heatmap.'-'.$image.'.png%%';

if (file_exists($imagePath) === false)
{
	errorPng(LANG_ERROR_PNG.' ('.$imagePath.')');
}

header('Content-Type: image/png');
readfile($imagePath);

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