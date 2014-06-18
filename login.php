<?php
/**
 * ClickHeat : formulaire de connexion / Login form
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 03/01/2007
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

?>
<span class="float-right"><img src="<?php echo CLICKHEAT_PATH ?>images/logo170.png" width="170" height="35" alt="ClickHeat" /></span>
<div id="clickheat-box">
	<h1><?php echo LANG_LOGIN ?></h1>
	<br />
	<form action="index.php?action=view" method="post">
	<table cellpadding="0" cellspacing="5" border="0">
	<tr><th><?php echo LANG_USER ?></th><td><input type="text" name="login" size="15" /></td></tr>
	<tr><th><?php echo LANG_PASSWORD ?></th><td><input type="password" name="pass" size="15" /></td></tr>
	<tr><td colspan="2" class="center">
		<input type="submit" value="<?php echo LANG_LOGIN ?>" />
		<?php if (isset($_POST['login'])) echo '<br /><br /><span class="error">', LANG_LOGIN_ERROR, '</span>'; ?>
	</td></tr>
	</table>
	</form>
</div>