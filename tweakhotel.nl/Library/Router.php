<?php

class Router

{

	public $Request;

	private $Maps = Array(), $CurrentMap;

	public $Navigation = Array();



	public function MapPackage($Package)

	{

		$FirstChar = substr($Package, 0, 1);

		if ($FirstChar == '.' || $FirstChar == '#')

		{

			return;

		}



		$MapLink = CORE.'/Packages/'.$Package.'/Maps.php';



		if (file_exists($MapLink))

		{

			$this->CurrentMap = $Package;

			require $MapLink;

		}

	}



	private function Map($Url, $Page, $Override = false)

	{

		if (!isset($this->Maps[$Url]) || $Override)

		{

			$this->Maps[$Url] = Array(

				'Package' => $this->CurrentMap,

				'Page' => $Page

			);

		}

	}



	public function Load($Url)

	{

		$this->Request = new Request($Url);



		$i = 10;

		while (!isset($this->Maps[$this->Request->Url]))

		{

			$this->Request->PopSubUrl();

			if ($i-- == 0) exit; //security, can be removed

		}



		return $this->Maps[$this->Request->Url];

	}



	public function AddNav($Title, $Subs)

	{

		if (!is_array($Subs))

		{

			$Subs = Array($Subs);

		}



		$this->Navigation[$Title] = $Subs;

	}

	public function AddNavAPP($Title, $Subs)

	{

		if (!is_array($Subs))

		{

			$Subs = Array($Subs);

		}



		$this->Navigation[$Title] = $Subs;

	}


}

?>