<?php
/**
 * ClickHeat : Fonctions diverses / Various functions
 * 
 * @author Yvan Taviaud - LabsMedia - www.labsmedia.com
 * @since 01/04/2007
**/

/** Direct call forbidden */
if (!defined('CLICKHEAT_LANGUAGE'))
{
	exit;
}

/**
 * Clean the logs' directory according to configuration data
**/
if (CLICKHEAT_ADMIN === false || $clickheatConf['flush'] === 0 || is_dir($clickheatConf['logPath']) === false)
{
	return false;
}
$logDir = dir($clickheatConf['logPath']);
$deletedFiles = 0;
$deletedDirs = 0;
while (($dir = $logDir->read()) !== false)
{
	if ($dir === '.' || $dir === '..' || !is_dir($logDir->path.$dir))
	{
		continue;
	}

	$d = dir($clickheatConf['logPath'].$dir.'/');
	$deletedAll = true;
	$oldestDate = mktime(0, 0, 0, date('m'), date('d') - $clickheatConf['flush'], date('Y'));
	while (($file = $d->read()) !== false)
	{
		if ($file === '.' || $file === '..' || $file === '%%url.txt%%')
		{
			continue;
		}
		$ext = explode('.', $file);
		if (count($ext) !== 2)
		{
			$deletedAll = false;
			continue;
		}
		$filemtime = filemtime($d->path.$file);
		switch ($ext[1])
		{
			case 'log%%':
				{
					/** Too old, must be deleted */
					if ($filemtime <= $oldestDate)
					{
						@unlink($d->path.$file);
						$deletedFiles++;
						continue;
					}
					break;
				}
			case 'html%%':
			case 'png%%':
				{
					if ($filemtime + 120 < time())
					{
						@unlink($d->path.$file);
						$deletedFiles++;
						continue;
					}
					break;
				}
		}
		$deletedAll = false;
	}
	/** If every log file (but the %%url.txt%%) has been deleted, then we should delete the directory too */
	if ($deletedAll === true)
	{
		@unlink($d->path.'/%%url.txt%%');
		$deletedFiles++;
		@rmdir($d->path);
		$deletedDirs++;
	}
	$d->close();
}
$logDir->close();

if ($deletedDirs + $deletedFiles === 0)
{
	echo 'OK';
	return true;
}
echo sprintf(LANG_CLEANER_RUN, $deletedFiles, $deletedDirs);
?>