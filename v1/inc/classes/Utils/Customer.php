<?php

namespace MR4Web_API\Utils;

use MR4Web_API\Connections\DB;
use MR4Web_API\Configs\Config;
use MR4Web_API\Utils\License;

class Customer {
	private $_license;
	private $_name;
	private $_email;
	private $_ID;
	private $_data = array();
	private $_isNew = false;
	private static $DB;

	public function __construct(License $license, $name, $email)
	{
		$this->_license = $license;
		$this->_name = $name;
		$this->_email = $email;
		self::$DB = &DB::getInstance();
		$this->init();
	}

	private function init()
	{
		try {
			$stm = self::$DB->prepare("SELECT * FROM `customer` WHERE `customer_ID`=? OR `email`=?");
			$stm->execute(array($this->_license->get('customer_ID'), $this->getEmail()));

			if ($stm->rowCount())
			{
				$this->_data = $stm->fetch(\PDO::FETCH_ASSOC);
				$this->_ID = $this->_data['customer_ID'];
			}
			else
				$this->_isNew = true;

		} catch (\PDOException $e){
			logger($e->getMessage());
			exit;
		}		
	}

	public function shouldToUpdate()
	{
		if (!$this->isNew() && ($this->get('email') != $this->getEmail() || $this->get('name') != $this->getName()))
		{
			return true;
		}
		return false;
	}

	public function update()
	{
		if ($this->shouldToUpdate())
		{
			logger("Update Customer info...\n");
			$sql = "UPDATE `customer` SET `name`='%s', `email`='%s', `updated`=%d WHERE `customer_ID`=%d";
			return self::$DB->exec(sprintf($sql, $this->getName(), $this->getEmail(), time(), $this->getID()));
		}
		return false;
	}

	public function register()
	{
		logger("registring customer...\n");
		$time = time();
		try {
			// register new customer.
			$stm = self::$DB->prepare("INSERT INTO `customer` (name, email, joined, updated) VALUES (?, ?, ?, ?)");
			$stm->execute(array($this->getName(), $this->getEmail(), $time, $time));
			$this->_ID = self::$DB->lastInsertId();

		} catch (PDOException $e){
			logger($e->getMessage());
			exit;
		}
	}

	public function isNew()
	{
		return $this->_isNew;
	}

	public function getID()
	{
		return $this->_ID;
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

	public function getEmail()
	{
		return $this->_email;
	}
}

?>