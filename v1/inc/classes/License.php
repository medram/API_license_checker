<?php

class License {

	private static $DB;
	private static $envato;
	private $_purchase_code;
	private $_data = array();

	public function __construct ($purchase_code)
	{
		self::$DB = &DB::getInstance();
		self::$envato = new Envato(Config::Get('ENVATO_USERNAME'), Config::Get('ENVATO_API_KEY'));
		$this->_purchase_code = $purchase_code;
		
		if ($this->isOnDatabase())
			$this->_data = $this->isOnDatabase(true);
	}

	/*
	* check if the purchase code is on the database.
	*/
	public function isOnDatabase ($returnData = false)
	{
		//print_r($this->_data);
		if (count($this->_data))
		{
			if ($returnData)
				return $this->_data;
			return true;
		}
		else
		{
			$stm = self::$DB->prepare("SELECT * FROM `license` WHERE `license_code`=:code");
			$stm->bindParam('code', $this->_purchase_code);
			$stm->execute();

			if ($stm->rowCount())
			{
				//print_r($stm->fetch(PDO::FETCH_ASSOC));
				if ($returnData)
					return $stm->fetch(PDO::FETCH_ASSOC);
				return true;
			}
			else
				return false;
		}
	}

	public function checkPurchaseCodeFromMarket()
	{
		if (DEBUG)
			return true; // for test
		return self::$envato->verify_purchase($this->_purchase_code);
	}

	/*
	* check the perchase code from Envato market.
	*/
/*	public function checkPurchaseCode(Domain $domain)
	{
		// check if the license is banned.
		if ($this->isBanned())
			return false;

		// check if the license is used before.
		if (!$this->isOnDatabase())
		{
			logger("code isn't on the database!\n");
			
			if (self::$envato->verify_purchase($this->_purchase_code))
			{
				$this->register();
				return $this->activate($domain);
			}
		}
		else
		{
			logger("code is on the database!\n");
			// check if its license is valid for this domain
			if ($domain->isNewIP($this))
			{
				logger("new IP!\n");
				if ($this->isValidToUsed())
					return $this->activate($domain); // activate license code for the new Domain.
				else
					return false; 
			}
			logger("old IP\n");
			return true; // license code is valid for the some domain.
		}

		return false;
	}*/

	public function get($key)
	{
		//print_r($this->_data);
		if (isset($this->_data[$key]))
			return $this->_data[$key];
		return NULL;
	}

	/*
	* 	check if is the domains are the some.
	*/
	public function isValidToUsed()
	{
		logger('license check: '.$this->get('activation_num') . ' < '. $this->get('activation_max')."\n");
		if ($this->get('activation_num') < $this->get('activation_max'))
			return true;
		else
			return false;
	}



	public function isBanned()
	{
		$codeData = $this->isOnDatabase(true);
		
		if (count($codeData))
		{		
			if ($codeData['banned'] == 0)
				return false;
			else
				return true;
		}
		return false;
	}

/*	public function purchaseCodeStatus ()
	{
		$licenseData = $this->checkFromDatabase(true);

		$stm = self::$DB->prepare("SELECT active FROM `license_product_costumer` WHERE `license_ID`=? AND `customer_ID`=? AND ``");
		$stm->bindParam(array(
				''
			));
	}*/

	/*
	* 	Add +1 to activation_num field. 
	*/
	public function activate (Domain $domain)
	{
		logger("activating code...\n");
		$stm = self::$DB->prepare("UPDATE `license` SET `activation_num`=`activation_num`+1 WHERE `license_code`=?");

		if ($stm->execute([$this->_purchase_code]) && $domain->activate($this))
			return true;
		else
			return false;
	}

	/*
	* 	Add -1 to activation_num field. 
	*/
	public function deactivate (Domain $domain)
	{
		logger("deactivating code...\n");
		
		$stm = self::$DB->prepare("UPDATE `license` SET `activation_num`=`activation_num`-1 WHERE `license_code`=?");

		if ($stm->execute([$this->_purchase_code]) && $domain->deactivate($this))
			return true;
		else
			return false;
	}

	/*
		register the purchase code to database & activate it & register customer information.
	*/
	public function register(Product $product, Customer $customer)
	{
		logger("registering the code...\n");
		try {
			$insert = self::$DB->prepare("INSERT INTO `license` (`product_ID`, `customer_ID`, `license_code`, `activation_num`, `activation_max`, `created`) VALUES (?, ?, ?, ?, ?, ?)");
			$insert->execute([$product->get('product_ID'), $customer->getID(), $this->_purchase_code, 0, 1,time()]);

			$this->_data = $this->isOnDatabase(true);
		} catch (PDOException $e){
			logger('Failed: '.$e->getMessage());
			exit;
		}
	}

}


?>