<?php

namespace MR4Web_API\Model;

use MR4Web_API\Model\Model;

class MLicense extends Model {

	public function __construct()
	{
		parent::__construct();
		$this->setTable("license");
	}

	
}

?>