<?php

namespace MR4Web_API\Pattern;

use MR4Web_API\Pattern\Interfaces\IObservable;
use MR4Web_API\Pattern\Interfaces\IObserver;
use MR4Web_API\Pattern\CustomerServer;

abstract class AbstractObservable implements IObservable {

	protected $_observers = array();

	public function attach(IObserver $ob)
	{
		$this->_observers[] = $ob;
	}

	public function detach(IObserver $ob)
	{
		foreach ($this->_observers as $key => $observer)
		{
			if ($observer === $ob)
				unset($this->_observers[$key]);
		}
	}

	//abstract public function notify();

}


?>