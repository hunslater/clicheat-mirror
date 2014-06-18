<?php
/**
 * ClickHeat : Administration
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 08/02/2007
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
$page = isset($_POST['page']) ? str_replace(array('.', '/'), array('', ''), $_POST['page']) : '';
$days = isset($_POST['days']) ? (int) $_POST['days'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (count($_POST) !== 0)
{
	switch ($action)
	{
		case 'checklatest' :
			{
				$error = 'latest';
				break;
			}

		case 'deletelogs' :
			{
				$result = cleanLogDir(CLICKHEAT_LOGPATH.$page.'/', $days);
				if ($result === false)
				{
					$error = 'Wrong parameters or demo server';
					break;

				}
				$error = $result[0].' '.LANG_DELETED_FILES;
				break;
			}

		case 'deletelogdir' :
			{
				$deleted = true;
				$d = dir(CLICKHEAT_LOGPATH);
				while (($dir = $d->read()) !== false)
				{
					if ($dir === '.' || $dir === '..' || !is_dir($d->path.$dir)) continue;
					$result = cleanLogDir($d->path.$dir.'/', -1);
					if ($result !== false)
					{
						$deleted = $deleted && $result[1];
					}
				}
				$deleted = $deleted && @rmdir(CLICKHEAT_LOGPATH);
				$error = $deleted === true ? LANG_LOG_DELETED : LANG_LOG_NOT_DELETED;
				break;
			}

		case 'updateoldlogs' :
			{
				$renamed = 0;
				$d = dir(CLICKHEAT_LOGPATH);
				while (($dir = $d->read()) !== false)
				{
					if ($dir === '.' || $dir === '..' || !is_dir($d->path.$dir)) continue;
					$dir = dir($d->path.$dir.'/');
					while (($file = $dir->read()) !== false)
					{
						if ($file === '.' || $file === '..' || strpos($file, '%%') === 0) continue;
						$date = strtotime(substr($file, 0, 10));
						/** The date is not valid (no reason for that, but hey, must check) */
						if ($date === false && $file !== 'url.txt')
						{
							continue;
						}
						/** File already exists, so delete old one */
						if (file_exists($dir->path.'%%'.$file.'%%'))
						{
							@unlink($dir->path.$file);
							continue;
						}
						@rename($dir->path.$file, $dir->path.'%%'.$file.'%%');
						$renamed++;
					}
					$dir->close();
				}
				$d->close();
				$error = $renamed.' '.LANG_RENAMED_FILES;
				break;
			}

		default :
			{
				$error = 'Wrong call';
				break;
			}
	}

	/** Return to the tools page */
	header('Location: tools.php?error='.rawurlencode($error));
	exit;
}

/** List of available pages */
$selectPages = '';
$firstPage = '';
$pageExists = false;
$d = @dir(CLICKHEAT_LOGPATH);
if ($d !== false)
{

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
}
if ($pageExists === false)
{
	$page = $firstPage !== '' ? $firstPage : '';
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
<h1><?php echo LANG_TITLE ?> : <?php echo LANG_TOOLS ?> - <a href="index.php"><?php echo LANG_INDEX ?></a></h1>
<br />
<?php
if (isset($_GET['error']) && $_GET['error'] !== '')
{
	echo '<div id="helpDiv" style="display:block;">', $_GET['error'] === 'latest' ? LANG_YOUR_VERSION.' : <iframe src="./VERSION" frameborder="0" scrolling="no" width="35" height="20"></iframe><br />'.LANG_LATEST_VERSION.' : <iframe src="http://www.labsmedia.com/clickheat/VERSION" frameborder="0" scrolling="no" width="35" height="20"></iframe>' : strip_tags($_GET['error']), '</div><br />';
}
?>
<form action="tools.php" method="post">
	<input type="hidden" name="action" value="checklatest" />
	<?php echo LANG_CHECK_LATEST ?>
	<input type="submit" value="<?php echo LANG_OK ?>" />
</form>
<br />
<form action="tools.php" method="post" onsubmit="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">
	<input type="hidden" name="action" value="deletelogs" />
	<?php echo LANG_DELETE_LOGS ?>
	<select name="days">
		<option value="1">1</option>
		<option value="7">7</option>
		<option value="15">15</option>
		<option value="30" selected="selected">30</option>
	</select>
	<?php echo LANG_DAYS ?>, <?php echo LANG_FOR_PAGE ?>
	<select name="page"><?php echo $selectPages ?></select>
	<input type="submit" value="<?php echo LANG_OK ?>" />
</form>
<br />
<form action="tools.php" method="post" onsubmit="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">
	<input type="hidden" name="action" value="deletelogdir" />
	<?php echo LANG_DELETE_LOG_DIR ?>
	<input type="submit" value="<?php echo LANG_OK ?>" />
</form>
<br />
<form action="tools.php" method="post" onsubmit="if(!confirm('<?php echo LANG_SURE ?> ?')) return false;">
	<input type="hidden" name="action" value="updateoldlogs" />
	<?php echo LANG_UPDATE_OLD_LOGS ?>
	<input type="submit" value="<?php echo LANG_OK ?>" />
</form>
</body>
</html>
<?php
/**
 * Clean a log dir
 *
 * @param string $dir
 * @param integer $days
**/
function cleanLogDir($dir, $days)
{
	global $demoServer;
	if ($demoServer === true || !in_array($days, array(-1, 1, 7, 15, 30)) || is_dir($dir) === false)
	{
		return false;
	}
	$d = dir(trim($dir, '/').'/');
	$deletedFiles = 0;
	$deletedAll = true;
	$oldestDate = mktime(0, 0, 0, date('m'), date('d') - $days, date('Y'));
	while (($file = $d->read()) !== false)
	{
		if ($file === '.' || $file === '..' || $file === '%%url.txt%%') continue;
		$date = strtotime(substr($file, 2, 10));
		/** The date is not valid (no reason for that, but hey, must check) */
		if ($date === false)
		{
			$deletedAll = false;
			continue;
		}
		/** Too old, must be deleted */
		if ($date <= $oldestDate)
		{
			@unlink($d->path.$file);
			$deletedFiles++;
			continue;
		}
		$deletedAll = false;
	}
	$d->close();
	/** If every log file (but the %%url.txt%%) has been deleted, then we should delete the directory too */
	if ($deletedAll === true)
	{
		@unlink($dir.'/%%url.txt%%');
		@rmdir($dir);
	}
	return array($deletedFiles, $deletedAll);
}
?>