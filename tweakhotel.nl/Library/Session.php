<?php
class Session
{
	public $Name;
	public $Data;

	private static $fields = Array(
		'id',
		'username',
		'motto',
		'look',
		'mail',
		'rank',
		'gender',
		'last_online',
		'password',
		'credits',
		'vip_points',
		'activity_points',
		'vip',
		'ip_last',
		'dj'
	);

	public function __construct($Username)
	{
		$this->Name = $Username;

		CMS::$MySql->Prepare('SELECT '.implode(',', self::$fields).' FROM users WHERE username = ? LIMIT 1');
		CMS::$MySql->Execute($this->Name);
		$this->Data = CMS::$MySql->FetchOne(self::$fields);

		if ($this->Data === false)
		{
			Users::Logout();
		}
	}

	public function Update($Var, $Value)
	{
		CMS::$MySql->Prepare('UPDATE users SET '.$Var.' = ? WHERE id = ?');
		CMS::$MySql->Execute($Value, $this->Data['id']);

		if (isset($this->Data[$Var]))
		{
			$this->Data[$Var] = $Value;
		}
	}

	public function GetFromDb($Var)
	{
		CMS::$MySql->Prepare('SELECT '.$Var.' FROM users WHERE username = ?');
		CMS::$MySql->Execute($this->Name);
		$Row = CMS::$MySql->FetchOne($Var);
		return $Row[$Var];
	}
}
?>