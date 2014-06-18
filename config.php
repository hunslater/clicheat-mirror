<?php
/**
 * Clickheat : Fichier d'édition de la configuration / Config editor
 * 
 * @author Yvan Taviaud / Labsmedia
 * @since 02/04/2007
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

/** Save or send the new config */
if (isset($_POST['save']) && isset($_POST['config']))
{
	$data = @unserialize(stripslashes($_POST['config']));
	if ($data !== false)
	{
		if (function_exists('var_export'))
		{
			$config = '<?php $clickheatConf = '.var_export($data, true).'; ?>';
		}
		else
		{
			$config = '<?php $clickheatConf = unserialize(\''.str_replace('\'', '\\\'', serialize($data)).'\'); ?>';
		}

		if ($_POST['save'] === 'false')
		{
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="config.php"');
			echo $config;
			exit;
		}
		else
		{
			$f = @fopen(CLICKHEAT_ROOT.'config/config.php', 'w');
			if ($f !== false)
			{
				fputs($f, $config, strlen($config));
				fclose($f);
				header('Location: index.php?action=view');
				exit;
			}
		}
	}
}

$check = isset($_POST['check']);
$checks = true;

$memoryLimit = (int) @ini_get('memory_limit');
/** Set default values if nothing is set in the config file */
$clickheatConf = array(
'logPath' => CLICKHEAT_ROOT.'logs/',
'adminLogin' => '',
'adminPass' => '',
'viewerLogin' => '',
'viewerPass' => '',
'memory' => $memoryLimit,
'step' => 5,
'dot' => 19,
'flush' => 40,
'start' => 'm',
'palette' => false,
'heatmap' => true,
'yesterday' => false,
'alpha' => 80);
if (file_exists(CLICKHEAT_ROOT.'config/config.php'))
{
	include CLICKHEAT_ROOT.'config/config.php';
}
/** Overwrite value with POST */
if (isset($_POST['logPath']))
{
	$clickheatConf['logPath'] = rtrim($_POST['logPath'], '/').'/';
}
if (isset($_POST['adminLogin']))
{
	$clickheatConf['adminLogin'] = $_POST['adminLogin'];
}
if (isset($_POST['_adminPass']) && isset($_POST['_adminPass2']) && $_POST['_adminPass'] !== '' && $_POST['_adminPass'] === $_POST['_adminPass2'])
{
	$clickheatConf['adminPass'] = md5($_POST['_adminPass']);
}
elseif (isset($_POST['adminPass']))
{
	$clickheatConf['adminPass'] = $_POST['adminPass'];
}
if (isset($_POST['viewerLogin']))
{
	$clickheatConf['viewerLogin'] = $_POST['viewerLogin'];
}
if (isset($_POST['_viewerPass']) && isset($_POST['_viewerPass2']) && $_POST['_viewerPass'] !== '' && $_POST['_viewerPass'] === $_POST['_viewerPass2'])
{
	$clickheatConf['viewerPass'] = md5($_POST['_viewerPass']);
}
elseif (isset($_POST['viewerPass']))
{
	$clickheatConf['viewerPass'] = $_POST['viewerPass'];
}
if (isset($_POST['memory']))
{
	$clickheatConf['memory'] = $_POST['memory'];
}
if (isset($_POST['step']))
{
	$clickheatConf['step'] = $_POST['step'];
}
if (isset($_POST['dot']))
{
	$clickheatConf['dot'] = $_POST['dot'];
}
if (isset($_POST['flush']))
{
	$clickheatConf['flush'] = $_POST['flush'];
}
if (isset($_POST['start']))
{
	$clickheatConf['start'] = $_POST['start'];
}
if (isset($_POST['palette']))
{
	$clickheatConf['palette'] = $_POST['palette'] === '1';
}
if (isset($_POST['heatmap']))
{
	$clickheatConf['heatmap'] = $_POST['heatmap'] === '1';
}
if (isset($_POST['yesterday']))
{
	$clickheatConf['yesterday'] = $_POST['yesterday'] === '1';
}
if (isset($_POST['alpha']))
{
	$clickheatConf['alpha'] = $_POST['alpha'];
}
/** Change type according to configuration needs */
$clickheatConf['memory'] = (int) abs($clickheatConf['memory']);
$clickheatConf['step'] = (int) abs($clickheatConf['step']);
$clickheatConf['dot'] = (int) abs($clickheatConf['dot']);
$clickheatConf['flush'] = (int) abs($clickheatConf['flush']);
$clickheatConf['start'] = in_array($clickheatConf['start'], array('m', 's')) ? $clickheatConf['start'] : 'm';
$clickheatConf['palette'] = (bool) $clickheatConf['palette'];
$clickheatConf['heatmap'] = (bool) $clickheatConf['heatmap'];
$clickheatConf['yesterday'] = (bool) $clickheatConf['yesterday'];
$clickheatConf['alpha'] = min(100, (int) abs($clickheatConf['alpha']));
?>
<span class="float-right"><img src="<?php echo CLICKHEAT_PATH ?>images/logo170.png" width="170" height="35" alt="ClickHeat" /></span>
<div id="clickheat-box">
<h1><?php echo LANG_CONFIG ?></h1>
<br /><br />
<form action="index.php?action=config" method="post">
<table cellpadding="0" cellspacing="5" border="0">
<tr><th><?php echo LANG_CONFIG_LOGPATH ?></th><td><input type="text" name="logPath" value="<?php echo htmlentities($clickheatConf['logPath']) ?>" />
<?php
if ($check === true)
{
	if (is_dir($clickheatConf['logPath']) === false)
	{
		mkdir(rtrim($clickheatConf['logPath'], '/'));
		if (is_dir($clickheatConf['logPath']) === false)
		{
			$checks = false;
			echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CHECK_LOGPATH_DIR;
		}
	}
	if (is_dir($clickheatConf['logPath']) === true)
	{
		/** Check if creation of directories is allowed */
		if (is_dir($clickheatConf['logPath'].'test_dir') === false && @mkdir($clickheatConf['logPath'].'test_dir') === false)
		{
			$checks = false;
			echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CHECK_LOGPATH_MKDIR;
		}
		else
		{
			/** Check if creation of a file is allowed */
			$f = fopen($clickheatConf['logPath'].'test_dir/test.txt', 'a');
			if ($f === false)
			{
				$checks = false;
				echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CHECK_LOGPATH_TOUCH;
			}
			else
			{
				fclose($f);
				@unlink($clickheatConf['logPath'].'test_dir/test.txt');
				echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
			}
			@rmdir($clickheatConf['logPath'].'test_dir');
		}
		/** Remove logs directory if empty, as tests may create many directories. There's no risk in doing this, as a filled directory won't be removed */
		@rmdir($clickheatConf['logPath']);
	}
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_MEMORY ?></th><td><input type="text" name="memory" value="<?php echo $clickheatConf['memory'] ?>" size="3" /> (<?php echo LANG_DEFAULT ?>: 8)
<?php
if ($check === true)
{
	if ($clickheatConf['memory'] === 0)
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_MEMORY_KO, ' <strong>', $memoryLimit, '<strong>';
	}
	else
	{
		echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
	}
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_STEP ?></th><td><input type="text" name="step" value="<?php echo $clickheatConf['step'] ?>" size="3" /> (<?php echo LANG_DEFAULT ?>: 5)
<?php
if ($check === true)
{
	if ($clickheatConf['step'] === 0)
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_STEP_KO;
	}
	else
	{
		echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
	}
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_DOT ?></th><td><input type="text" name="dot" value="<?php echo $clickheatConf['dot'] ?>" size="3" /> (<?php echo LANG_DEFAULT ?>: 19)
<?php
if ($check === true)
{
	if ($clickheatConf['dot'] === 0)
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_DOT_KO;
	}
	else
	{
		echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
	}
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_PALETTE ?></th><td><input type="hidden" name="palette" value="off" /><input type="checkbox" name="palette"<?php if ($clickheatConf['palette'] === true) echo ' checked="checked"' ?> value="1" /> (<?php echo LANG_DEFAULT ?>: off)
<?php
if ($check === true)
{
	echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_HEATMAP ?></th><td><input type="hidden" name="heatmap" value="off" /><input type="checkbox" name="heatmap"<?php if ($clickheatConf['heatmap'] === true) echo ' checked="checked"' ?> value="1" /> (<?php echo LANG_DEFAULT ?>: on)
<?php
if ($check === true)
{
	echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_YESTERDAY ?></th><td><input type="hidden" name="yesterday" value="off" /><input type="checkbox" name="yesterday"<?php if ($clickheatConf['yesterday'] === true) echo ' checked="checked"' ?> value="1" /> (<?php echo LANG_DEFAULT ?>: off)
<?php
if ($check === true)
{
	echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_ALPHA ?></th><td><input type="text" name="alpha" value="<?php echo $clickheatConf['alpha'] ?>" size="3" /> (<?php echo LANG_DEFAULT ?>: 80)
<?php
if ($check === true)
{
	echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_FLUSH ?></th><td><input type="text" name="flush" value="<?php echo $clickheatConf['flush'] ?>" size="3" /> (<?php echo LANG_DEFAULT ?>: 40)
<?php
if ($check === true)
{
	echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_START ?></th><td><input type="radio" name="start" value="m"<?php if ($clickheatConf['start'] === 'm') echo ' checked="checked"' ?> /><?php echo LANG_CONFIG_START_M ?> <input type="radio" name="start" value="s"<?php if ($clickheatConf['start'] === 's') echo ' checked="checked"' ?> /><?php echo LANG_CONFIG_START_S ?>
<?php
if ($check === true)
{
	echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_ADMIN_LOGIN ?></th><td><input type="text" name="adminLogin" value="<?php echo htmlentities($clickheatConf['adminLogin']) ?>" />
<?php
if ($check === true)
{
	if (strlen($clickheatConf['adminLogin']) < 4)
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_LOGIN;
	}
	else
	{
		echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
	}
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_ADMIN_PASS ?></th><td><input type="password" name="_adminPass" /><br /><input type="password" name="_adminPass2" /><input type="hidden" name="adminPass" value="<?php echo htmlentities($clickheatConf['adminPass']) ?>" />
<?php
if ($check === true)
{
	if (isset($_POST['_adminPass']) && isset($_POST['_adminPass2']) && $_POST['_adminPass'] !== '' && $_POST['_adminPass'] !== $_POST['_adminPass2'])
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_MATCH;
	}
	elseif ($clickheatConf['adminPass'] === '')
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_PASS;
	}
	else
	{
		echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
	}
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_VIEWER_LOGIN ?></th><td><input type="text" name="viewerLogin" value="<?php echo htmlentities($clickheatConf['viewerLogin']) ?>" />
<?php
if ($check === true)
{
	if (strlen($clickheatConf['viewerLogin']) < 4 && $clickheatConf['viewerLogin'] !== '')
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_LOGIN;
	}
	else
	{
		echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
	}
}
?></td></tr>
<tr><th><?php echo LANG_CONFIG_VIEWER_PASS ?></th><td><input type="password" name="_viewerPass" /><br /><input type="password" name="_viewerPass2" /><input type="hidden" name="viewerPass" value="<?php echo htmlentities($clickheatConf['viewerPass']) ?>" />
<?php
if ($check === true)
{
	if (isset($_POST['_viewerPass']) && isset($_POST['_viewerPass2']) && $_POST['_viewerPass'] !== '' && $_POST['_viewerPass'] !== $_POST['_viewerPass2'])
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_MATCH;
	}
	elseif ($clickheatConf['viewerPass'] === '' && $clickheatConf['viewerLogin'] !== '')
	{
		$checks = false;
		echo '</td><td><img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CONFIG_PASS;
	}
	else
	{
		echo '</td><td><img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
	}
}
?></td></tr>
<tr><td colspan="<?php echo $check === true ? 4 : 2 ?>" class="center">&nbsp;<br /><br />
	<input type="hidden" name="check" value="1" />
	<input type="submit" value="<?php echo LANG_CONFIG_CHECK ?>" />
</td></tr>
</table>
</form>
<br />
<form action="index.php?action=config" method="post" class="center">
<input type="hidden" name="config" value="<?php echo htmlentities(serialize($clickheatConf)) ?>" />
<?php
if ($check === true && $checks === true)
{
	/** Test if config path is writable for config.php : */
	$f = fopen(CLICKHEAT_ROOT.'config/temp.tmp', 'w');
	if ($f === false)
	{
		echo '<input type="hidden" name="save" value="false" />', LANG_CONFIG_DL, '<br /><input type="submit" value="', LANG_CONFIG_DOWNLOAD, '" />';
	}
	else
	{
		fputs($f, 'delete this file');
		fclose($f);
		@unlink(CLICKHEAT_ROOT.'config/temp.tmp');
		echo '<input type="hidden" name="save" value="true" /><input type="submit" value="', LANG_CONFIG_SAVE, '" />';
	}
}
?>
</form>
</div>