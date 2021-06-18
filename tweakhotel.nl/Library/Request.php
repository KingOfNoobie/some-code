<?php
class Request
{
	public $Url, $SubUrls = Array();
	public $Get, $Post;

	public function __construct($Url)
	{
		$Split = explode('?', trim($Url, '/'), 2);
		$this->Url = '/'.preg_replace('#/{2,}#', '/', $Split[0]);

		if ($this->Url == '/')
		{
			$this->Url = '/index';
		}

		$this->Get = $_GET;
		$this->Post = $_POST;
	}

	public function PopSubUrl()
	{
		$LastPart = strrchr($this->Url, '/');

		$this->Url = substr($this->Url, 0, 0 - strlen($LastPart));
		if (empty($this->Url))
		{
			$this->Url = '/error';
			$this->SubUrls = Array();
		}
		else
		{
			array_unshift($this->SubUrls, $LastPart);
		}
	}
}
?>