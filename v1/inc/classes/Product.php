<?php

class Product {
	private $_name;
	private $_version = 0;
	private $_data = array();
	private static $DB;

	public function __construct($name, $version)
	{
		$this->_name = $name;
		$this->_version = $version;
		self::$DB = &DB::getInstance();
		$this->init();
	}

	private function init()
	{
		$stm = self::$DB->query("SELECT * FROM `product` WHERE `name`='{$this->getName()}'");
		if ($stm->rowCount())
		{
			$this->_data = $stm->fetch(PDO::FETCH_ASSOC);
		}
	}

	public function get($key)
	{
		if (isset($this->_data[$key]))
			return $this->_data[$key];
		return NULL;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getVersion()
	{
		return $this->_version;
	}
}

?>