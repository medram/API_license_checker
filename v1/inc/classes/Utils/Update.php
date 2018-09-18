<?php

namespace MR4Web_API\Utils;
use MR4Web_API\Connections\DB;

class Update {

	private static $DB;
	private static $_tableName;
	private $_productData;
	private $_updatesData;
	private $_featuresData;

	public function __construct($productName)
	{
		self::$DB = &DB::getInstance();
		self::$_tableName = 'product';
		$this->_productData = [];
		$this->_featuresData = [];
		$this->init($productName);
	}

	private function init($productName)
	{
		// get news data using ID
		$stm = self::$DB->prepare("SELECT * FROM `".self::$_tableName."` WHERE `name`=?");
		$stm->execute([$productName]);
		if ($stm->rowCount())
		{
			$this->_productData = $stm->fetch(\PDO::FETCH_ASSOC);
			$stm2 = self::$DB->prepare("SELECT * FROM `updates` WHERE `product_ID`=? ORDER BY `update_ID` DESC LIMIT 0, 1");
			$stm2->execute([$this->_productData['product_ID']]);
			$this->_updatesData = $stm2->fetch(\PDO::FETCH_ASSOC);

			$stm3 = self::$DB->prepare("SELECT * FROM `features` WHERE `update_ID`=?");
			$stm3->execute([$this->_updatesData['update_ID']]);
			$this->_featuresData = $stm3->fetchAll(\PDO::FETCH_ASSOC);
		}
	}

	public function version()
	{
		return $this->_productData['version'];
	}

	public function getFromProductData($key)
	{
		if (isset($this->_productData[$key]))
			return $this->_productData[$key];
		return NULL;
	}

	public function getFeatures($key)
	{
		if (isset($this->_featuresData[$key]))
			return $this->_featuresData[$key];
		return NULL;
	}

	public function getAll()
	{
		$features = [];
		foreach ($this->_featuresData as $f)
		{
			$features[]['desc'] = $f['feature_desc'];
		}
		return ['product' => $this->_productData, 'updates' => $this->_updatesData, 'features' => $features];
	}
}


?>