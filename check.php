<?php
/**
 * ClickHeat : Test de la configuration / Configuration check
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 04/12/2006
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

$checks = true;
?>
<div id="clickheat-box">
<h1><?php echo LANG_CHECKS ?></h1>
<br /><br />
<table cellpadding="0" cellspacing="5" border="0">
<tr><th><?php echo LANG_CHECK_WRITABLE ?><br />(<?php echo dirname(__FILE__) ?>/config/)</th><td>
<?php
/** Test if current path is writable for config.php : */
$f = fopen(CLICKHEAT_ROOT.'config/temp.tmp', 'w');
if ($f === false)
{
	echo '<img src="./images/warning.png" width="16" height="16" alt="Warning" /></td><td>', LANG_CHECK_NOT_WRITABLE;
}
else
{
	fputs($f, 'delete this file');
	fclose($f);
	@unlink(CLICKHEAT_ROOT.'config/temp.tmp');
	echo '<img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><th><?php echo LANG_CHECK_GD ?></th><td>
<?php
if (function_exists('imagecreatetruecolor') === false)
{
	$checks = false;
	echo '<img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CHECK_GD_IMG;
}
elseif (function_exists('imagecolorallocatealpha') === false)
{
	$checks = false;
	echo '<img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CHECK_GD_ALPHA;
}
elseif (function_exists('imagepng') === false)
{
	$checks = false;
	echo '<img src="./images/ko.png" width="16" height="16" alt="KO" /></td><td>', LANG_CHECK_GD_PNG;
}
else
{
	echo '<img src="./images/ok.png" width="16" height="16" alt="OK" /></td><td>&nbsp;';
}
?></td></tr>
<tr><td colspan="3" align="center">&nbsp;<br /><br />
<?php
if ($checks === false)
{
	echo LANG_CHECKS_KO;
}
else
{
	echo LANG_CHECKS_OK, ' <a href="index.php?action=config"><img src="./images/next.png" width="16" height="16" alt="Next" /></a>';
}
?></td></tr>
</table>
</div>