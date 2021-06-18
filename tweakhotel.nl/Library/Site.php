<?php
class Site
{
	public static function Stop($Redirect = false)
	{
		if ($Redirect !== false)
		{
			header('Location: '.$Redirect);
		}

		exit();
	}

	public static function GetUsersOnline()
	{
		$Result = CMS::$MySql->Query('SELECT users_online FROM server_status LIMIT 1')->fetch_assoc();
		return $Result['users_online'];
	}

	public static function Hash($input)
    {
        return md5($input.'b6t7G^&vtTV^%F45FGgB&YBNbtb&&7b');
    }

	public static function RandomMD5($Length, $Seed = '')
	{
		$Times = floor($Length / 32);
		$Hash = '';
		for ($i = 0; $i < $Times; $i++)
		{
			$Hash .= md5(sha1(microtime(true)).sha1($Seed));
		}

		return substr($Hash, 0, $Length);
	}

	public static function MUS($Header, $Data)
	{
		$Packet = $Header.chr(1).$Data;
		$socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
		socket_connect($socket, CMS::$Config['emulator.mushost'], CMS::$Config['emulator.musport']);
		socket_send($socket, $Packet, strlen($Packet), MSG_DONTROUTE);
		socket_close($socket);
	}
	
	public static function Config($variable, $var)
	{
		$sql = CMS::$MySql->Query("SELECT * FROM cms_settings WHERE variable = '".$variable."' LIMIT 1");
		$row = $sql->fetch_assoc();
		return $row[$var];
	}
	
	public static function RadioConfig($variable, $var)
	{
		$sql = CMS::$MySql->Query("SELECT * FROM radio_settings WHERE variable = '".$variable."' LIMIT 1");
		$row = $sql->fetch_assoc();
		return $row[$var];
	}
	
	public static function GangConfig($variable, $var)
	{
		$sql = CMS::$MySql->Query("SELECT * FROM gang_settings WHERE variable = '".$variable."' LIMIT 1");
		$row = $sql->fetch_assoc();
		return $row[$var];
	}
}
?>