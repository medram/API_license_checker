<?php

namespace MR4Web_API\Pattern;

use MR4Web_API\Pattern\AbstractObservable;
use MR4Web_API\Utils\Update;
use MR4Web_API\Configs\Config;

class UpdateObservable extends AbstractObservable {
	private $_updateData = array();

	public function __construct(Update $update)
	{
		$this->_updateData[] = $update;
	}

	public function getUpdateData()
	{
		return $this->_updateData[0]; // for just ONE update class
	}

	public function notify()
	{
		$serversNum = count($this->_observers);
		console("Start Notifing {$serversNum} CustomerServer with New Update:\n");
		$success = 0;
		$failed = 0;
		$i = 1;
		foreach ($this->_observers as $ob)
		{
			console("[$i/{$serversNum}]");
			if ($ob->updateSoftware($this))
				++$success;
			else
				++$failed;
			++$i;
			usleep(Config::get('sleep')*1000);
		}
		console("\nFinished! (success:{$success}/".$serversNum." | failed:{$failed}/".$serversNum.").");
	}
}

?>