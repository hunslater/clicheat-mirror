<?php
/**
 * Compress a JS file
 * 
 * BEWARE! This compressor has only be tested against ClickHeat.js file, so use it at your own risk! No warranties are given!
 * Known problems are comments starting with «//», these are not supported
 * 
 * @author Yvan Taviaud
 * @since 29/11/2007
**/
/** Code copié de notre classe String::obfuscate() */
$str = file_get_contents('../js/clickheat-original.js');
if ($str === false)
{
	exit('No JS file, really strange!');
}
/** Compression du code pour le Javascript */
$str = str_replace("\t", '', $str);
/** Commentaires */
$str = preg_replace('~/\*.*?\*/~s', '', $str);
/** Tests et fins de ligne */
$str = preg_replace('~ (==|!=|=|\+|\?|\:|\-|\|\||&&|>|<|>=|=<|) ~', '\\1', $str);
$str = preg_replace('~;(\n| )~', ';', $str);
$str = preg_replace('~if \(~', 'if(', $str);
/** Retours à la ligne et accolades de fonction */
$str = preg_replace('~\n{2,}~', "\n", $str);
$str = preg_replace('~\n{\n~', '{', $str);
$str = preg_replace('~\n}\n~', "}\n", $str);
$str = preg_replace('~}\n}~', '}}', $str);

file_put_contents('../js/clickheat.js', '/** Code by www.labsmedia.com */'.$str);
?>