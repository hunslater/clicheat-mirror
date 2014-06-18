<?php
/**
 * ClickHeat : Enregistrement d'un clic pour PMV / Logging of a tracked click for PMV
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 16/05/2007
**/

/** First of all, check if we are inside PhpMyVisites */
define('INCLUDE_PATH', str_replace('/plugins/clickheat/libs', '', str_replace('\\', '/', dirname(__FILE__))));

/** Include click file */
include './click.php';
?>