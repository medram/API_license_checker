<?php

namespace MR4Web_API\Pattern;
use MR4Web_API\Utils\News;
use MR4Web_API\Connections\DB;

class NewsFactory {

	private static $DB;
	private static $_tableName;
	private static $_newsIDList;
	private static $_newsList;

	private static function init()
	{
		self::$DB = &DB::getInstance();
		self::$_tableName = 'news';

		$stm = self::$DB->prepare("SELECT `news_ID` FROM `".self::$_tableName."`");
		$stm->execute();
		if ($stm->rowCount())
			self::$_newsIDList = $stm->fetchAll(\PDO::FETCH_ASSOC);
		else
			self::$_newsIDList = [];
	}

	public static function CreateList()
	{
		if (self::$_newsIDList == '' || self::$_newsList == '')
		{
			self::init();
			
			//print_r(self::$_newsIDList);

			foreach (self::$_newsIDList as $ID)
			{
				$ID = $ID['news_ID'];
				self::$_newsList[] = new News($ID);
			}
		}
		return self::$_newsList;
	}
}

?>