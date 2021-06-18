<?php
class ConfigParser
{
	public static function Scan()
	{
		$Scan = scandir(CORE.'/Cache/');
		unset($Scan[0], $Scan[1]);

		if (count($Scan) == 0)
		{
			foreach (glob(CORE.'/Configuration/*.txt') as $ConfigFile)
			{
				$ConfigFile = basename($ConfigFile);
				$ConfigFile = substr($ConfigFile, 0, strlen($ConfigFile) - 4);
				$Hash = 'config.'.md5($ConfigFile.sha1(microtime())).'.cache';
				self::Load($ConfigFile, $Hash);

				$Scan[] = $Hash;
			}
		}

		return $Scan;
	}

	private static function Load($Name, $Hash)
	{	
		$ConfigData = file_get_contents(CORE.'/Configuration/'.$Name.'.txt');
		$Explode = explode("\n", $ConfigData);
		$Output = '';

		foreach ($Explode as $Part)
		{
			$KVP = explode('=', $Part);
			if (isset($KVP[1]))
			{
				$Output .= "CMS::\$Config['".strtolower($Name.".".trim($KVP[0]))."'] = '".trim($KVP[1])."';";
			}
		}

		file_put_contents(CORE.'/Cache/'.$Hash, '<?php '.$Output.' ?>');
	}
}
?>