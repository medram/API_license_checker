<?php

namespace MR4Web_API\Pattern;

use MR4Web_API\Pattern\NewsObservable;
use MR4Web_API\Pattern\UpdateObservable;
use MR4Web_API\Pattern\Interfaces\IObserver;
use MR4Web_API\Pattern\Interfaces\IObservable;
use MR4Web_API\Connections\DB;

class CustomerServer implements IObserver {

	private $_IP;
	private $_domainData;
	private $_licenseData;
	private static $DB;

	public function __construct($IP)
	{
		self::$DB = &DB::getInstance();
		$this->_IP = $IP;
		$this->_domainData = array();
		$this->_licenseData = array();
		$this->init();
	}

	public function init()
	{
		$stm = self::$DB->prepare("SELECT * FROM `domains` WHERE `IP`=?");
		$stm->execute([$this->_IP]);
		$this->_domainData = $stm->fetch(\PDO::FETCH_ASSOC);

		if (count($this->_domainData))
		{
			$stm = self::$DB->prepare("SELECT * FROM `license` WHERE `license_ID`=?");
			$stm->execute([$this->_domainData['license_ID']]);
			$this->_licenseData = $stm->fetch(\PDO::FETCH_ASSOC);
		}
	}

	public function updateNews(IObservable $oble)
	{
		$time = microtime(true);
		/*
			check if the server customer is alive
			push some information to customer server via curl 
		*/
		if ($this->isOnline())
		{
			console("--> sending News to [{$this->_IP}]...".dots(40 - strlen($this->_IP)));
			$data = [];

			foreach ($oble->getNewsList() as $news)
			{
				$data[] = $news->getAll();
			}

			$data = json_encode($data);
			$res = MyCURL($this->_domainData['listener'], ['action' => 'delete_prev', 'update' => 'news', 'data' => $data]);
			if (isset($res['received']) && $res['received'])
			{
				console("[OK]");
				console(" - (".round(microtime(true)-$time, 5)." ms)\n");
				return true;
			}
			else
				console("[ERR:{$res}]");
		}
		else
		{
			console("--> [{$this->_IP}] is OFFLINE.\n");
		}
		console(" - (".round(microtime(true)-$time, 5)." ms)\n");
		return false;
	}

	public function updateSoftware(IObservable $oble)
	{
		$time = microtime(true);

		if ($this->isOnline())
		{
			console("--> sending Update to [{$this->_IP}]...".dots(40 - strlen($this->_IP)));
			$data = $oble->getUpdateData()->getAll();
			//print_r($data);
			$data = json_encode($data);
			
			$res = MyCURL($this->_domainData['listener'], ['action' => 'delete_prev', 'update' => 'software', 'data' => $data]);

			if (isset($res['received']) && $res['received'])
			{
				console("[OK]");
				console(" - (".round(microtime(true)-$time, 5)." ms)\n");
				return true;
			}
			else
			{
				console("[ERR:{$res}]");
			}
		}
		else
		{
			console("--> [{$this->_IP}] is OFFLINE.\n");
		}
		console(" - (".round(microtime(true)-$time, 5)." ms)\n");
		return false;
	}

	public function isOnline()
	{
		return ping($this->_IP);
	}
}

?>