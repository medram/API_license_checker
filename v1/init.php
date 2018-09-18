<?php

define('START_TIME', microtime(true));

// true, to fake Envato request & show operations messages.
define('DEBUG', true);
define('DEBUG_SHOW_OPERATIONS', false);
define('DEBUG_SHOW_MSGS_CONSOLE', true);

define('INC', 'inc/');
define('CLASS_DIR', INC.'classes/');

require_once INC.'common.php';

/*
* autoload classes.
*/

spl_autoload_register(function ($filename){
	//echo $filename.'<br>';
	$path = str_ireplace('MR4Web_API\\', CLASS_DIR, $filename);
	$path = $path.'.php';
	$path = str_ireplace('\\', '/', $path);

	if (file_exists($path))
	{
		//echo '<pre><b>Loading ...</b> '.$path.'</pre>';
		require $path;
	}	
	else
	{
		//echo "<pre>Fatal Error: The Class <b>\"".$filename."\"</b> Not Found on this path <b>".$path."</b></pre>";
	}
});

?>