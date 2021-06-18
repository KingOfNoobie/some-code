<?php
class Cache
{
	private static $Running = false;

	public static function Load($Name, $Time)
	{
		$Name = CORE.'/Cache/widget.'.md5($Name).'.cache';

		if (self::$Running)
		{
			file_put_contents($Name, ob_get_flush());

			self::$Running = false;
			return false;
		}

		if (file_exists($Name) && filemtime($Name) > time() - $Time)
		{
			echo file_get_contents($Name);
			return false;
		}

		ob_start();
		self::$Running = true;
		return true;
	}
}
?>