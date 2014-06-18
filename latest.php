<?php
/**
 * Clickheat : vérification de la dernière version disponible / Latest available version
 * 
 * @author Yvan Taviaud / Labsmedia
 * @since 16/04/2007
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

?>
<span class="float-right"><a href="#" onclick="hidePageLayout(); return false;"><img src="<?php echo CLICKHEAT_PATH ?>images/ko.png" width="16" height="16" alt="Close" /></a></span>
<h1><?php echo LANG_LATEST_CHECK ?></h1>
<?php
$f = @fsockopen('www.labsmedia.com', 80, $errno, $errstr, 5);
$F = @fopen(CLICKHEAT_ROOT.'VERSION', 'r');
if ($f === false || $F === false)
{
	echo LANG_LATEST_KO, ' <a href="http://www.labsmedia.com/clickheat/index.html">ClickHeat</a>';
}
else
 {
 	$current = trim(fgets($F));
 	fclose($F);
	fputs($f, "GET /clickheat/VERSION HTTP/1.1\r\nHost: www.labsmedia.com\r\n");
	fputs($f, "Connection: close\r\n\r\n");
	while (!feof($f) && trim(fgets($f)) !== '') {}
	$latest = trim(fgets($f));
	fclose($f);
	if ($current === $latest)
	{
		echo sprintf(LANG_LATEST_OK, $latest);
	}
	else 
	{
		echo sprintf(LANG_LATEST_NO, $current, $latest), ' <a href="http://www.labsmedia.com/clickheat/index.html">ClickHeat</a>';
	}
}
?>