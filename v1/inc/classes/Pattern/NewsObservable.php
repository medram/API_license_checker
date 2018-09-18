<?php

namespace MR4Web_API\Pattern;

use MR4Web_API\Pattern\AbstractObservable;
use MR4Web_API\Utils\News;
use MR4Web_API\Configs\Config;

class NewsObservable extends AbstractObservable {

	private $_newsList = array();

	public function __construct(array $newsList)
	{
		foreach ($newsList as $news)
		{
			if ($news instanceof News)
				$this->_newsList[] = $news;
		}
	}

	public function getNewsList()
	{
		return $this->_newsList;
	}

	public function notify()
	{
		$serversNum = count($this->_observers);
		console("Start Notifing {$serversNum} CustomerServer with News:\n");
		$success = 0;
		$failed = 0;
		$i = 1;
		foreach ($this->_observers as $ob)
		{
			console("[$i/{$serversNum}]");
			if ($ob->updateNews($this))
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