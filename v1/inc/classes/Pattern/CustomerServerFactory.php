<?php

namespace MR4Web_API\Pattern;

use MR4Web_API\Pattern\CustomerServer;
use MR4Web_API\Connections\DB;

class CustomerServerFactory {

	private static $_validDomains = [];
	private static $DB;

	public static function CreateList($IP = NULL)
	{
		if (!count(self::$_validDomains))
			self::init($IP);
		
		$customerServerList = [];

		foreach (self::$_validDomains as $data)
		{
			$customerServerList[] = new CustomerServer($data['IP']);
		}
		return $customerServerList;
	}

	private static function init($IP = NULL)
	{
		self::$DB = &DB::getInstance();
		//get valid licenses to get valid domains, and we use valid domains to create CustomerServer.

		if (filter_var($IP, FILTER_VALIDATE_IP))
		{
			$stm = self::$DB->prepare("SELECT * FROM `domains` WHERE `IP`=? AND `active`=?");
			$stm->execute([$IP, 1]);
			$rows = $stm->fetchAll(\PDO::FETCH_ASSOC);

			if (count($rows))
				self::$_validDomains = array_merge(self::$_validDomains, $rows);
		}
		else
		{
			$stm = self::$DB->prepare("SELECT * FROM `license` WHERE `banned`=0 AND `activation_num` > 0");
			$stm->execute();

			if ($stm->rowCount())
				$validLicenses = $stm->fetchAll(\PDO::FETCH_ASSOC);
			else
				$validLicenses = [];

			foreach ($validLicenses as $key => $license)
			{
				$stm = self::$DB->prepare("SELECT * FROM `domains` WHERE `license_ID`=? AND `active`=?");
				$stm->execute([$license['license_ID'], 1]);
				$rows = $stm->fetchAll(\PDO::FETCH_ASSOC);

				if (count($rows))
					self::$_validDomains = array_merge(self::$_validDomains, $rows);
			}
		}
	}
}

?>