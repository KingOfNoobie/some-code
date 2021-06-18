<?php
class Template
{
	private $Package;

	private $Output = '';
	private $Vars = Array();

	public $Title = '';
	private $CSS = Array();
	private $JS = Array();
	private $Favicon = '';
	private $HeadData = '';

	private $Body = Array(
		'id' => '',
		'class' => '',
		'content' => ''
	);

	public function __construct($Package)
	{
		$this->Package = CORE.'/Packages/'.$Package;
	}

	public function Define($Var, $Value)
	{
		$this->Vars['$'.$Var] = $Value;
	}

	public function DefineArray($Prefix, $Array)
	{
		foreach ($Array as $Key => $Value)
		{
			$this->Define($Prefix.'_'.$Key, $Value);
		}
	}

	public function DefineColumn($Prefix, $Column)
	{
		$ColumnCount = count($Column);
		for ($i = 0; $i < $ColumnCount; $i++)
		{
			$this->DefineArray(($i + 1).'_'.$Prefix, $Column[$i]);
		}
	}

	public function Output($Page, $Html)
	{
		if (file_exists($this->Package.'/Start.php'))
		{
			require $this->Package.'/Start.php';
		}

		require $this->Package.'/Pages/'.$Page;

		if (file_exists($this->Package.'/End.php'))
		{
			require $this->Package.'/End.php';
		}

		$this->Body['content'] = strtr($this->Output, $this->Vars);
		$this->Output = null;

		require HTDOCS.'/'.$Html;
	}

	public function AddCSS($Link)
	{
		$this->CSS[] = '<link rel="stylesheet" type="text/css" href="'.$Link.'" />';
	}

	public function AddJS($Link)
	{
		$this->CSS[] = '<script type="text/javascript" src="'.$Link.'"></script>';
	}

	public function SetFavicon($Link)
	{
		$this->Favicon = '<link rel="shortcut icon" href="'.$Link.'">';
	}

	public function AddHeadData($Data)
	{
		$this->HeadData .= $Data;
	}

	private function Write($Text)
	{
		$this->Output .= $Text;
	}

	private function WriteInc($IncName, $Args = Array())
	{
		extract($Args);

		ob_start();
		require CORE.'/Includes/'.$IncName.'.php';
		$this->Write(ob_get_clean());
	}

	private function LoadTpl($Name, $Args = Array())
	{
		extract($Args);

		ob_start();
		require $this->Package.'/Templates/'.$Name.'.tpl';
		$this->Write(ob_get_clean());
	}
}
?>