<?php
/**
 * ClickHeat : Test de la configuration / Configuration check
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 04/12/2006
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

/** Protection of Labsmedia servers (hide critical info as the password is public) */
$demoServer = strpos($_SERVER['SERVER_NAME'], '.labsmedia.com') !== false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title><?php echo LANG_TITLE ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="./clickheat.css" />
</head>
<body>
<h1><?php echo LANG_CHECKS ?></h1>
<table cellpadding="0" cellspacing="5" border="0">
<tr><th><?php echo LANG_CHECK_SYSTEM ?></th><td>OS = <?php echo $demoServer === false ? PHP_OS : 'hidden' ?>, PHP = <?php echo $demoServer === false ? PHP_VERSION : 'hidden' ?></td></tr>
<tr><th><?php echo LANG_CHECK_LOGPATH ?></th><td>
<?php
/** Test of log directory : */
if (is_dir(CLICKHEAT_LOGPATH) === false)
{
	mkdir(CLICKHEAT_LOGPATH);
	if (is_dir(CLICKHEAT_LOGPATH) === false)
	{
		echo LANG_CHECK_LOGPATH_DIR;
	}
}
if (is_dir(CLICKHEAT_LOGPATH) === true)
{
	/** Check if creation of directories is allowed */
	if (is_dir(CLICKHEAT_LOGPATH.'test_dir') === false && @mkdir(CLICKHEAT_LOGPATH.'test_dir') === false)
	{
		echo LANG_CHECK_LOGPATH_MKDIR;
	}
	else
	{
		/** Check if creation of a file is allowed */
		if (@touch(CLICKHEAT_LOGPATH.'test_dir/test.txt') === false)
		{
			echo LANG_CHECK_LOGPATH_TOUCH;
		}
		else
		{
			@unlink(CLICKHEAT_LOGPATH.'test_dir/test.txt');
			echo LANG_CHECK_OK;
		}
		@rmdir(CLICKHEAT_LOGPATH.'test_dir');
	}
}
?></td></tr>
<tr><th><?php echo LANG_CHECK_MEMORY ?></th><td>
<?php
$memory = (int) @ini_get('memory_limit');
if ($memory === 0)
{
	if (CLICKHEAT_MEMORY === 0)
	{
		echo LANG_CHECK_MEMORY_BAD;
	}
	elseif (gettype(CLICKHEAT_MEMORY) !== 'integer')
	{
		echo LANG_CHECK_MEMORY_INT;
	}
	else
	{
		echo LANG_CHECK_OK;
	}
}
else
{
	echo LANG_CHECK_OK;
}
?></td></tr>
<tr><th><?php echo LANG_CHECK_GD ?></th><td>
<?php
if (function_exists('imagecreate') === false)
{
	echo LANG_CHECK_GD_NA;
}
elseif (function_exists('imagecreatetruecolor') === false)
{
	echo LANG_CHECK_GD_IMG;
}
elseif (function_exists('imagecolorallocatealpha') === false)
{
	echo LANG_CHECK_GD_ALPHA;
}
else
{
	echo LANG_CHECK_OK;
}
?></td></tr>
</table>
</body>
</html>