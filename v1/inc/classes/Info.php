<?php

class Info {

	private $_customer;
	private $_product;
	private static $DB;

	public function __construct (Customer $customer, Product $product)
	{
		$this->_customer = $customer;
		$this->_product = $product;
		self::$DB = &DB::getInstance();
		$this->isNew();
	}

	public function save()
	{
		if ($this->isNew())
			// insert
			return $this->insert();
/*		else
			// update
			return $this->update();*/
	}

/*	private function update()
	{
		logger("updating info...\n");
		$stm = self::$DB->prepare("UPDATE `product_customer` SET version=?, last_check=?, checks_num=checks_num+1 WHERE `product_ID`=? AND `customer_ID`=?");
		return (bool)$stm->execute([$this->_product->getVersion(), time(), $this->_product->get('product_ID'), $this->_customer->getID()]);
	}*/

	private function insert()
	{
		logger("inserting info...\n");
		$stm = self::$DB->prepare("INSERT INTO `product_customer` (product_ID, customer_ID) VALUES (?, ?)");
		return (bool)$stm->execute([$this->_product->get('product_ID'), $this->_customer->getID()]);
	}

	private function isNew()
	{
		$stm = self::$DB->prepare("SELECT * FROM `product_customer` WHERE `product_ID`=? AND `customer_ID`=?");

		$stm->execute([$this->_product->get('product_ID'), $this->_customer->getID()]);

		if ($stm->rowCount())		
			return false;
		else
			return true;
	}
}


?>