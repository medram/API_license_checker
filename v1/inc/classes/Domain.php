<?php

class Domain {
	private $_license;
	private $_domain;
	private $_IP;
	private $_data = array();
	private static $DB;

	public function __construct(License $license, $ip, $domain='')
	{
		$this->_license = $license;
		if (filter_var($domain, FILTER_VALIDATE_URL))
			$this->_domain = preg_replace("/((https|http):\/\/)?/i", "", $domain);
		
		if (filter_var($ip, FILTER_VALIDATE_IP))
			$this->_IP = $ip;
		
		//echo $this->_domain."\n";
		self::$DB = &DB::getInstance();
	}

	public function getIP()
	{
		return $this->_IP;
	}

	public function getDomainName()
	{
		return $this->_domain;
	}

	public function activate()
	{
		$time = time();
		$sql = "UPDATE `domains` SET `active`=1, `last_modification`=%d, last_check=%d, checks_num=checks_num+1 WHERE `IP`='%s' AND `license_ID`=%d";

		return self::$DB->exec(sprintf($sql, $time, $time, $this->getIP(), $this->_license->get('license_ID')));
	}

	public function deactivate()
	{
		$time = time();
		$sql = "UPDATE `domains` SET `active`=0, `last_modification`=%d, last_check=%d, checks_num=checks_num+1 WHERE `IP`='%s' AND `license_ID`=%d";
		return self::$DB->exec(sprintf($sql, $time, $time, $this->getIP(), $this->_license->get('license_ID')));
	}

	public function updateInfo(Product $product)
	{
		logger("Domain info updating...\n");
		$sql = "UPDATE `domains` SET product_version='%s', last_check=%d, checks_num=checks_num+1 WHERE `license_ID`=%d AND `IP`='%s'";

		return self::$DB->exec(sprintf($sql, $product->getVersion(), time(), $this->_license->get('license_ID'), $this->getIP()));
	}

	public function register(Product $product)
	{
		try {
			logger("registering domain...\n");
			$time = time();

			$stm = self::$DB->prepare("INSERT INTO `domains` (license_ID, IP, domain_name, product_version, active, created, last_modification, last_check, checks_num) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");	
			$stm->execute(array(
					$this->_license->get('license_ID'),
					$this->getIP(),
					$this->getDomainName(),
					$product->getVersion(),
					0, // 1 to activate the domain.
					$time,
					$time,
					$time,
					0
				));
		} catch (PDOException $e) {
			logger("ERROR: ".$e->getMessage());
			exit;
		}
		
/*		if ($stm->errorCode() != 0)
			print_r($stm->errorInfo());*/
	}

	private function getData()
	{
		if (count($this->_data))
			return $this->_data;

		try {
			$stm = self::$DB->prepare("SELECT * FROM `domains` WHERE `license_ID`=? AND `IP`=?");
			$stm->execute(array($this->_license->get('license_ID'), $this->getIP()));

			if ($stm->rowCount())
			{
				$this->_data = $stm->fetch(PDO::FETCH_ASSOC);
				return $this->_data;
			}
		} catch (PDOException $e) {
			logger("ERROR: ".$e->getMessage());
			exit;
		}
		return NULL;
	}

	public function isActive()
	{
		$this->getData($this->_license);
		
		if ($this->get('active'))
			return true;
		return false;
	}

	public function get($key)
	{
		if (isset($this->_data[$key]))
			return $this->_data[$key];
		return NULL;
	}

	public function isNewIP()
	{
		$this->getData($this->_license);

		if (count($this->_data))
			return false;
		return true;
	}
}

?>