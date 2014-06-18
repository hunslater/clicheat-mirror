<?php
/**
 * Clickheat : Code javascript à coller sur les pages / Javascript code to be paste on pages
 * 
 * @author Yvan Taviaud / Labsmedia
 * @since 08/04/2007
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

?>
<span class="float-right"><a href="#" onclick="hidePageLayout(); return false;"><img src="<?php echo CLICKHEAT_PATH ?>images/ko.png" width="16" height="16" alt="Close" /></a></span>
<h1><?php echo LANG_JAVASCRIPT ?></h1>
<form action="#" method="get" onsubmit="return false;">
<table cellpadding="0" cellspacing="2" border="0">
<tr><td><?php echo LANG_JAVASCRIPT_PAGE ?></td><td>
	<input type="radio" name="jsPage" id="jsPage1" value="0" checked="checked" onclick="updateJs();" /> <?php echo LANG_JAVASCRIPT_PAGE1 ?><br />
	<input type="radio" name="jsPage" id="jsPage2" value="1" onclick="updateJs();" /> <?php echo LANG_JAVASCRIPT_PAGE2 ?><br />
	<input type="radio" name="jsPage" id="jsPage3" value="2" onclick="updateJs();" /> <?php echo LANG_JAVASCRIPT_PAGE3 ?></td></tr>
<tr><td><?php echo LANG_JAVASCRIPT_QUOTA ?></td><td><input type="text" name="js-quota" id="jsQuota" value="0" size="3" onchange="updateJs();" onkeyup="updateJs();" /></td></tr>
<tr><td><?php echo LANG_JAVASCRIPT_IMAGE ?><img src="<?php echo CLICKHEAT_PATH ?>images/logo.png" width="80" height="15" border="0" alt="ClickHeat : track clicks" /></td><td><input type="checkbox" name="js-image" id="jsShowImage" checked="checked" onclick="updateJs();" /></td></tr>
</table>
<br />
<?php echo LANG_JAVASCRIPT_PASTE ?><br />
<pre id="clickheat-js"></pre>
<img src="<?php echo CLICKHEAT_PATH ?>images/warning.png" width="16" height="16" alt="Warning" /> <?php echo LANG_JAVASCRIPT_DEBUG ?>
</form>