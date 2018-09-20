<?php

include_once "init.php";

use MR4Web_API\Pattern\NewsObservable;
use MR4Web_API\Pattern\UpdateObservable;
use MR4Web_API\Pattern\CustomerServerFactory;
use MR4Web_API\Utils\Update;
use MR4Web_API\Pattern\NewsFactory;


/*$action = isset($_POST['action'])? $_POST['action'] : '';

switch ($action)
{
	case 'news':
		notifyCustomerWithNews();
		break;
	case 'update':
		notifyCustomerWithUpdate();
		break;
}*/

function notifyCustomerWithNews()
{
	/*
		get CustomersServers
		push it to the NewsObservable
		and notify
	*/
	$newsList = NewsFactory::CreateList();
	$newsObservable = new NewsObservable($newsList);
	$customerServersList = CustomerServerFactory::CreateList();
	//print_r($newsList);
	//print_r($customerServersList);
	// attach customerServers to newsObservable
	foreach ($customerServersList as $customerServer)
		$newsObservable->attach($customerServer);
	
	// notify all customer servers with new News
	$newsObservable->notify();
}

function notifyCustomerWithUpdate($productName)
{
	/*
		get CustomersServers
		push it to the UpdateObservable
		and notify
	*/
	$update = new Update($productName);
	$updateObservable = new UpdateObservable($update);
	$customerServersList = CustomerServerFactory::CreateList();

	foreach ($customerServersList as $customerServer)
		$updateObservable->attach($customerServer);

	$updateObservable->notify();
}

notifyCustomerWithUpdate('ADLinker');
//notifyCustomerWithNews();
?>