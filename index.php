<?php
/**
 * ClickHeat : Fichier principal / Main file
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 27/10/2006
**/

$__action = isset($_GET['action']) && $_GET['action'] !== '' ? $_GET['action'] : 'view';

/** First of all, check if we are inside PhpMyVisites */
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['SCRIPT_NAME'] !== '')
{
	$realPath = &$_SERVER['REQUEST_URI'];
}
elseif (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] !== '')
{
	$realPath = &$_SERVER['SCRIPT_NAME'];
}
else
{
	exit(LANG_UNKNOWN_DIR);
}
if (substr($realPath, -1, 1) === '/')
{
	header('Location: '.$realPath.'index.php');
	exit;
}

if (!defined('INCLUDE_PATH'))
{
	define('CLICKHEAT_PATH', rtrim(dirname($realPath), '/').'/');
	define('CLICKHEAT_INDEX_PATH', rtrim(dirname($realPath), '/').'/index.php?');
	define('CLICKHEAT_ROOT', './');
	define('IS_PHPMV_MODULE', false);
}
else
{
	define('CLICKHEAT_PATH', rtrim(dirname($realPath), '/').'/libs/clickheat/');
	define('CLICKHEAT_INDEX_PATH', rtrim(dirname($realPath), '/').'/index.php?mod=view_clickheat&');
	define('CLICKHEAT_ROOT', INCLUDE_PATH.'/libs/clickheat/');
	define('IS_PHPMV_MODULE', true);
}

/** Improve buffer usage and compression */
if (function_exists('ob_start') && IS_PHPMV_MODULE === false)
{
	if (function_exists('ob_gzhandler'))
	{
		ob_start('ob_gzhandler');
	}
	else
	{
		ob_start();
	}
}

/** Loading language according to browser's Accept-Language */
$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) : '';
if (!in_array($lang, array('fr', 'en', 'ru')))
{
	$lang = 'en';
}
define('CLICKHEAT_LANGUAGE', $lang);
unset($lang);
include CLICKHEAT_ROOT.'languages/'.CLICKHEAT_LANGUAGE.'.php';

/** If there's no config file, run check script */
if (!file_exists(CLICKHEAT_ROOT.'config/config.php'))
{
	if ($__action !== 'check' && $__action !== 'config')
	{
		$__action = 'check';
	}
}
else
{
	include CLICKHEAT_ROOT.'config/config.php';

	/** Login check */
	if (IS_PHPMV_MODULE === true)
	{
		/** Consider that we are already logged */
		define('CLICKHEAT_ADMIN', true);
	}
	elseif (isset($_COOKIE['clickheat']))
	{
		if ($_COOKIE['clickheat'] === $clickheatConf['adminLogin'].'||'.$clickheatConf['adminPass'])
		{
			/** Everything is fine, admin logged */
			define('CLICKHEAT_ADMIN', true);
		}
		elseif ($_COOKIE['clickheat'] === $clickheatConf['viewerLogin'].'||'.$clickheatConf['viewerPass'])
		{
			/** Viewer logged, force it to 'view' action if not view|generate|png */
			if ($__action !== 'generate' && $__action !== 'png' && $__action !== 'iframe' && $__action !== 'cleaner' && $__action !== 'logout')
			{
				$__action = 'view';
			}
		}
		else
		{
			/** Not logged, send him to login form */
			$__action = 'logout';
		}
	}
	else
	{
		if (isset($_POST['login']) && isset($_POST['pass']))
		{
			if ($_POST['login'] === $clickheatConf['adminLogin'] && md5($_POST['pass']) === $clickheatConf['adminPass'])
			{
				/** Set a session cookie */
				setcookie('clickheat', $clickheatConf['adminLogin'].'||'.$clickheatConf['adminPass'], 0, '/');
				/** Redirect to index.php */
				header('Content-Type: text/html');
				header('Location: index.php?action=view');
				exit;
			}
			elseif ($clickheatConf['viewerLogin'] !== '' && $_POST['login'] === $clickheatConf['viewerLogin'] && md5($_POST['pass']) === $clickheatConf['viewerPass'])
			{
				/** Set a session cookie */
				setcookie('clickheat', $clickheatConf['viewerLogin'].'||'.$clickheatConf['viewerPass'], 0, '/');
				/** Redirect to index.php */
				header('Content-Type: text/html');
				header('Location: index.php?action=view');
				exit;
			}
		}
		$__action = 'login';
	}
}
if (!defined('CLICKHEAT_ADMIN'))
{
	define('CLICKHEAT_ADMIN', false);
}

/** Specific definitions */
$__screenSizes = array(0 /** Must start with 0 */, 640, 800, 1024, 1280, 1600, 1800);
$__browsersList = array('all' => '', 'firefox' => 'Firefox', 'msie' => 'Internet Explorer', 'safari' => 'Safari', 'opera' => 'Opera', 'kmeleon' => 'K-meleon', 'unknown' => '');
define('CLICKHEAT_LOW_COLOR', 0); /** Niveau minimal de couleur RVB / Lower RGB level of color */
define('CLICKHEAT_HIGH_COLOR', 255); /** Niveau maximal de couleur RVB / Higher RGB level of color */
define('CLICKHEAT_GREY_COLOR', 240); /** Niveau du gris (couleur du 0 clic) / Grey level (color of no-click) */
$__colorLevels = array(50, 70, 90, 110, 120); /** Dégradé de couleurs, 5 valeurs entre 0 et 127 / Color gradient, 5 values between 0 and 127 */

switch ($__action)
{
	case 'check':
	case 'config':
	case 'view':
	case 'login':
		{
			header('Content-Type: text/html; charset=utf-8');
			if (IS_PHPMV_MODULE === false) include CLICKHEAT_ROOT.'header.php';
			include CLICKHEAT_ROOT.$__action.'.php';
			if (IS_PHPMV_MODULE === false) include CLICKHEAT_ROOT.'footer.php';
			break;
		}
	case 'generate':
	case 'layout':
	case 'javascript':
	case 'latest':
	case 'cleaner':
		{
			header('Content-Type: text/html; charset=utf-8');
			include CLICKHEAT_ROOT.$__action.'.php';
			break;
		}
	case 'iframe':
		{
			$page = isset($_GET['page']) ? str_replace(array('.', '/'), array('', ''), $_GET['page']) : '';
			if (is_dir($clickheatConf['logPath'].$page))
			{
				$webPage = array('/');
				if (file_exists($clickheatConf['logPath'].$page.'/%%url.txt%%'))
				{
					$f = @fopen($clickheatConf['logPath'].$page.'/%%url.txt%%', 'r');
					if ($f !== false)
					{
						$webPage = explode('>', trim(fgets($f, 1024)));
						fclose($f);
					}
				}
				echo $webPage[0];
			}
			break;
		}
	case 'png':
		{
			$page = isset($_GET['page']) ? str_replace(array('.', '/'), array('', ''), $_GET['page']) : '';
			$date = isset($_GET['date']) ? str_replace(array('.', '/'), array('', ''), $_GET['date']) : '1970-01-01';
			$range = isset($_GET['range']) && in_array($_GET['range'], array('d', 'w', 'm')) ? $_GET['range'] : 'd';
			$browser = isset($_GET['browser']) && isset($browsersList[$_GET['browser']]) ? $_GET['browser'] : 'all';
			$screen = isset($_GET['screen']) ? (int) $_GET['screen'] : 0;
			$image = isset($_GET['image']) ? (int) $_GET['image'] : 0;
			$heatmap = isset($_GET['heatmap']) ? (int) $_GET['heatmap'] : 0;

			$imagePath = $clickheatConf['logPath'].$page.'/%%'.$date.'-'.$range.'-'.$screen.'-'.$browser.'-'.$heatmap.'-'.$image.'.png%%';

			header('Content-Type: image/png');
			if (file_exists($imagePath))
			{
				readfile($imagePath);
			}
			else
			{
				readfile(CLICKHEAT_ROOT.'images/warning.png');
			}
			break;
		}
	case 'layoutupdate':
		{
			$page = isset($_GET['page']) ? str_replace(array('.', '/'), array('', ''), $_GET['page']) : '';
			$url = isset($_GET['url']) ? $_GET['url'] : '';
			$left = isset($_GET['left']) ? (int) $_GET['left'] : 0;
			$center = isset($_GET['center']) ? (int) $_GET['center'] : 0;
			$right = isset($_GET['right']) ? (int) $_GET['right'] : 0;

			if (!is_dir($clickheatConf['logPath'].$page) || $url === '')
			{
				exit('Error');
			}

			$f = @fopen($clickheatConf['logPath'].$page.'/%%url.txt%%', 'w');
			fputs($f, $url.'>'.$left.'>'.$center.'>'.$right);
			fclose($f);

			echo 'OK';
			break;
		}
	case 'logout':
		{
			setcookie('clickheat', '', time() - 30 * 86400, '/');
			header('Location: index.php');
			exit;
			break;
		}
	default:
		{
			header('HTTP/1.0 404 Not Found');
			exit('Error, page not found');
			break;
		}
}
?>