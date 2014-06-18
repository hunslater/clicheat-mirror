<?php
/**
 * ClickHeat : Fonctions diverses / Various functions
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 01/04/2007
**/

/**
 * Clean the logs' directory according to configuration data
**/
function cleanLogDirs()
{
	global $demoServer;
	if (CLICKHEAT_ADMIN === false && $days !== 30 || !in_array($days, array(-1, 1, 7, 15, 30)) || is_dir($dir) === false)
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