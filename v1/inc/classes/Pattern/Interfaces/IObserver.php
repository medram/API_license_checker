<?php

namespace MR4Web_API\Pattern\Interfaces;

use MR4Web_API\Pattern\Interfaces\IObservable;
//use MR4Web_API\Pattern\AbstractObservable;

interface IObserver {

	public function updateNews(IObservable $oble);
	public function updateSoftware(IObservable $oble);
}


?>