<?php

namespace MR4Web_API\Model;

use MR4Web_API\Model\Model;

class MDomain extends Model {

	public function __construct()
	{
		parent::__construct();
		$this->setTable("domains");
	}

	
}

?>