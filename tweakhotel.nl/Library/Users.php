<?php
class Users
{
	public static $Session = false;

	public static function CheckLogin()
	{
		if (isset($_SESSION['username']))
		{
			self::$Session = new Session($_SESSION['username']);
		}
	}

	public static function SessionHasRank($Rank)
	{
		return (self::$Session !== false && self::$Session->Data['rank'] >= $Rank);
	}

	public static function Login($Name_Mail, $Password)
	{
		CMS::$MySql->Prepare("SELECT username, password, rank FROM users WHERE username = ? OR mail = ?");
		CMS::$MySql->Execute($Name_Mail, $Name_Mail);
		$Row = CMS::$MySql->FetchOne('username', 'password', 'rank');

		if ($Row === false)
		{
			return 1;
		}

		if ($Row['password'] != Site::Hash($Password))
		{
			return 2;
		}

		if ($Row['rank'] < CMS::$Config['cms.nobanrank'])
		{
			CMS::$MySql->Prepare('SELECT bantype, reason, expire FROM bans WHERE value = ? OR value = ? LIMIT 1');
			CMS::$MySql->Execute($Row['username'], RemoteIp);
			$BanRow = CMS::$MySql->FetchOne('bantype', 'reason', 'expire');

			if ($BanRow !== false)
			{
				if ($BanRow['expire'] > time())
				{
					$_SESSION['ban'] = $BanRow;
					return 3;
				}

				CMS::$MySql->Prepare('DELETE FROM bans WHERE expire < ?');
				CMS::$MySql->Execute(time());
			}
		}

		$_SESSION['username'] = $Row['username'];
		return 0;
	}

	public static function Logout()
	{
		unset($_SESSION['username']);
		self::$Session = false;
	}

	public static function Id2Name($Id)
	{
		CMS::$MySql->Prepare("SELECT username FROM users WHERE id = ?");
		CMS::$MySql->Execute($Id);
		$Row = CMS::$MySql->FetchOne('username');
		return $Row['username'];
	}
	
	public static function getRank($rankId, $var)
	{
		$sql = CMS::$MySql->Query("SELECT * FROM ranks WHERE id = '".$rankId."' LIMIT 1");
		$row = $sql->fetch_assoc();
		return $row[$var];
	}

	public static function LastOnline($Time)
	{
		if (is_numeric($Time))
		{
			return date('d-m-y H:i:s', $Time);
		}

		return $Time;
	}

	public static function ValidName($Name)
	{
		return (ctype_alnum($Name) && strlen($Name) >= 3 && strlen($Name) <= 32);
	}

	public static function NameFree($Name)
	{
		CMS::$MySql->Prepare("SELECT COUNT(*) FROM users WHERE username = ? LIMIT 1");
		CMS::$MySql->Execute($Name);
		$Row = CMS::$MySql->FetchOne('COUNT(*)');
		return ($Row['COUNT(*)'] == 0);
	}

	public static function ValidMail($Mail)
	{
		return (preg_match("/^[a-zA-Z0-9_\.-]+@([a-zA-Z0-9]+([\-]+[a-zA-Z0-9]+)*\.)+[a-z]{2,7}$/i", $Mail)
			&& strlen($Mail) >= 3 && strlen($Mail) <= 64);
	}

	public static function MailFree($Mail)
	{
		CMS::$MySql->Prepare("SELECT COUNT(*) FROM users WHERE mail = ? LIMIT 1");
		CMS::$MySql->Execute($Mail);
		$Row = CMS::$MySql->FetchOne('COUNT(*)');
		return ($Row['COUNT(*)'] == 0);
	}

	public static function ToMuchIP()
	{
		CMS::$MySql->Prepare("SELECT COUNT(*) FROM users WHERE ip_reg = ?");
		CMS::$MySql->Execute(RemoteIp);
		$Row = CMS::$MySql->FetchOne('COUNT(*)');
		return ($Row['COUNT(*)'] > 100);
	}

	public static function CheckAdd($Name, $Mail, $Pass, $Pass2 = false)
	{
		if (!self::ValidName($Name))
		{
			return 1;
		}

		if (!self::NameFree($Name))
		{
			return 2;
		}

		if (!self::ValidMail($Mail))
		{
			return 3;
		}

		if (!self::MailFree($Mail))
		{
			return 4;
		}

		if ($Pass2 !== false && $Pass != $Pass2)
		{
			return 5;
		}

		if (!isset($Pass[5]) || isset($Pass[32]))
		{
			return 6;
		}

		if (self::ToMuchIP())
		{
			return 7;
		}

		return 0;
	}
	
	public static function UserInfo($username, $var)
	{
		$sql = CMS::$MySql->Query("SELECT * FROM users WHERE username = '".$username."' LIMIT 1");
		$row = $sql->fetch_assoc();
		return $row[$var];
	}
	
	public static function Add($Name, $Mail, $Pass, $Gender, $Birth = null)
	{
		$Looks = Array(
			'm' => 'hd-209-1.lg-285-82.sh-906-62.hr-100-61.wa-2001-62.ch-225-66',
			'f' => 'hd-629-1.lg-715-82.wa-2008-92.ch-635-66.hr-515-32.sh-730-92'
		);

		CMS::$MySql->Insert('users', Array(
			'username' => $Name,
			'password' => Site::Hash($Pass),
			'mail' => $Mail,
			'auth_ticket' => '-',
			'rank' => 1,
			'credits' => intval(Site::Config('register_credits', "value")),
			'vip_points' => 0,
			'activity_points' => intval(Site::Config('register_pixels', "value")),
			'look' => $Looks[$Gender],
			'gender' => $Gender,
			'motto' => Site::Config('register_motto', "value"),
			'last_online' => time(),
			'online' => '0',
			'ip_last' => RemoteIp,
			'ip_reg' => RemoteIp,
			'home_room' => Site::Config('register_homeroom', "value"),
			'account_created' => time()
		));
	}
}
?>