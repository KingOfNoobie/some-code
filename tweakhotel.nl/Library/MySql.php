<?php
class MySql
{
	private $Link;
	private $Statement;
	public $Result = null;
	private $FetchRows = Array();

	public function __construct($Data)
	{
		$this->Link = new MySQLi($Data['mysql.hostname'], $Data['mysql.username'], $Data['mysql.password'], $Data['mysql.database']);
	}

	public function Query($Query)
	{
		if (isset($this->Statement))
		{
			$this->Statement->Close();
			$this->Statement = null;
		}

		return $this->Link->query($Query);
	}

	public function Error()
	{
		if (!empty($this->Link->error))
		{
			exit($this->Link->error);
		}
	}

	public function Prepare($Query)
	{
		if (isset($this->Statement))
		{
			$this->Statement->Close();
		}

		$this->Statement = $this->Link->prepare($Query);
		
	}

	public function Execute($FuncArgs = null)
	{
		if (!is_array($FuncArgs))
		{
			$FuncArgs = func_get_args();
		}

		$Args = Array('');

		foreach ($FuncArgs as &$Arg)
		{
			$Args[0] .= substr(gettype($Arg), 0, 1);
			$Args[] =& $Arg;
		}

		call_user_func_array(Array($this->Statement, 'bind_param'), $Args);
		if (!$this->Statement->Execute())
		{
			exit('Execute Stmt Error: '.$this->Statement->error);
		}

		return $this->Statement;
	}

	public function Fetch($Columns)
	{
		if (!is_array($Columns))
		{
			$Columns = func_get_args();
		}

		if ($this->Result == null)
		{
			$this->Result = array_combine($Columns, $Columns);
			$Args = Array();

			foreach ($Columns as $Column)
			{
				$Args[] =& $this->Result[$Column];
			}

			call_user_func_array(Array($this->Statement, 'bind_result'), $Args);
		}

		$RowsLeft = $this->Statement->fetch();

		if (!$RowsLeft)
		{
			self::Clear();
			return false;
		}

		return $this->Result;
	}

	public function FetchOne()
	{
		$Args = func_get_args();
		$Result = call_user_func_array(Array($this, 'Fetch'), $Args);
		$this->Clear();

		return $Result;
	}

	public function Clear()
	{
		$this->Result = null;
	}

	public function Insert($Table, $Vars)
	{
		$this->Prepare('INSERT INTO '.$Table.' ('.implode(',', array_keys($Vars)).') VALUES ('.substr(str_repeat('?,', count($Vars)), 0, -1).')');
		$this->Execute(array_values($Vars));
	}
}

?>