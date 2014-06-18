<?php
/**
 * ClickHeat : vérification admin / Login check
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 03/01/2007
**/

/** Login check */
if (CLICKHEAT_PASSWORD !== '' && (!isset($_COOKIE['clickheat']) || $_COOKIE['clickheat'] !== md5(CLICKHEAT_PASSWORD)))
{
	/** Password is ok */
	if (isset($_POST['pass']) && $_POST['pass'] === CLICKHEAT_PASSWORD)
	{
		setcookie('clickheat', md5(CLICKHEAT_PASSWORD), 0, '/');
		/** Content-Type for IIS only, else a CGI error appears */
		header('Content-Type: text/html');
		header('Location: '.$_SERVER['PHP_SELF']);
		echo 'Location: '.$_SERVER['PHP_SELF'];
		exit;
	}
	/** Content-Type for IIS only, else a CGI error appears */
	header('Content-Type: text/html');
	echo '<html><body><form method="post">', LANG_AUTHORIZATION, ' : <input type="password" name="pass"/><input type="submit" value="', LANG_CHECK_OK, '" /></form></body></html>';
	exit;
}