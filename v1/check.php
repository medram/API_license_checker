<?php
include_once "init.php";

use MR4Web_API\Pattern\NewsObservable;
use MR4Web_API\Pattern\UpdateObservable;
use MR4Web_API\Pattern\CustomerServerFactory;
use MR4Web_API\Utils\Update;
use MR4Web_API\Pattern\NewsFactory;


$action = isset($_POST['check'])? $_POST['check'] : '';

switch ($action)
{
	case 'Im_alive':
		checkForNewStuff();
		break;
	default:
	{
		header("location: https://www.mr4web.com");
		exit;
	}
}

function checkForNewStuff()
{
	$data = isset($_POST['data']) ? json_decode($_POST['data'], true) : '' ;
	if (count($data))
	{
		//file_put_contents('data.txt', json_encode($data));

		if (filter_var($data['IP'], FILTER_VALIDATE_IP))
		{
			notifyCustomerWithNews($data['IP']);
			notifyCustomerWithUpdate($data['IP'] ,$data['product_name'], $data['product_version']);
		}
	}
}

function notifyCustomerWithNews($IP = NULL)
{
	/*
		get CustomersServers
		push it to the NewsObservable
		and notify
	*/
	$newsList = NewsFactory::CreateList();
	$newsObservable = new NewsObservable($newsList);
	$customerServersList = CustomerServerFactory::CreateList($IP);
	//print_r($customerServersList);
	// attach customerServers to newsObservable
	foreach ($customerServersList as $customerServer)
		$newsObservable->attach($customerServer);
	
	// notify all customer servers with new News
	$newsObservable->notify();
}

function notifyCustomerWithUpdate($IP, $productName, $productVersion)
{
	/*
		get CustomersServers
		push it to the UpdateObservable
		and notify
	*/
	$update = new Update($productName);
	if (version_compare($productVersion, $update->version(), '<'))
	{
		$updateObservable = new UpdateObservable($update);
		$customerServersList = CustomerServerFactory::CreateList($IP);

		foreach ($customerServersList as $customerServer)
			$updateObservable->attach($customerServer);

		$updateObservable->notify();
	}
}



?>