<?php

namespace MR4Web_API\Pattern\Interfaces;

use MR4Web_API\Pattern\Interfaces\IObserver;

interface IObservable {

	public function attach(IObserver $ob);
	public function detach(IObserver $ob);
	public function notify();
}

?>