<?php

include_once "init.php";

/*
1) Register license.
2) check license.
2) activate/deactivate license.

*/

if (!isset($_POST['action']) || is_null($_POST['action']))
	exit;

$action = isset($_POST['action']) ? strip_tags(_addslashes($_POST['action'])) : '' ;

// should be called after the database connection.
require_once INC.'operations.php';

switch ($action)
{
	case 'activate':
		activate_operation();
		break;
	case 'deactivate':
		deactivate_operation();
		break;
}

\MR4Web_API\Utils\Res::emit();

?>