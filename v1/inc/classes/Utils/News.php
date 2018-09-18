<?php

namespace MR4Web_API\Utils;
use MR4Web_API\Connections\DB;

class News {

	private static $DB;
	private static $_tableName;
	private $_newsData;

	public function __construct($news_ID)
	{
		self::$DB = &DB::getInstance();
		self::$_tableName = 'news';
		$this->_newsData = [];
		$this->init($news_ID);
	}

	private function init($news_ID)
	{
		// get news data using ID
		$stm = self::$DB->prepare("SELECT * FROM `".self::$_tableName."` WHERE `news_ID`=?");
		$stm->execute([$news_ID]);
		if ($stm->rowCount())
			$this->_newsData = $stm->fetch(\PDO::FETCH_ASSOC);
	}

	public function get($key)
	{
		if (isset($this->_newsData[$key]))
			return $this->_newsData[$key];
		return [];
	}

	public function getAll()
	{
		return $this->_newsData;
	}
}

?>