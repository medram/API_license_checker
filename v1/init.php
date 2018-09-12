<?php

// true, to fake Envato request & show operations messages.
define('DEBUG', true);
define('DEBUG_SHOW_OPERATIONS', false);

define('INC', 'inc/');
define('CLASS_DIR', INC.'classes/');

require_once INC.'common.php';

/*
* autoload classes.
*/
spl_autoload_register(function ($filename){
	$path = CLASS_DIR.ucfirst($filename).'.php';
	if (file_exists($path))
		include $path;
});

?>