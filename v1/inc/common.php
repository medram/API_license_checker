<?php

use MR4Web_API\Configs\Config;

function _addslashes ($str)
{
	if (get_magic_quotes_gpc())
	{
		return $str;
	}
	else
	{
		return addslashes($str);
	}
}

function checkParams($keys)
{
	foreach ($keys as $key)
	{
		if (!isset($_POST[$key]) || empty($_POST[$key]))
			return false;
	}

	return true;
}

function logger($string)
{
	if (DEBUG_SHOW_OPERATIONS)
		echo $string;
}

function MyCURL($URL, array $fields = array())
{
	$userAgent = isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT'] : Config::get('user_agent');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($ch, CURLOPT_NOBODY, true);
	
	if ($userAgent != '')
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

	if (count($fields))
	{
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	}

    $res = json_decode(curl_exec($ch), true);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($res == false)
        $res = $httpCode;
    curl_close($ch);

    return $res;
}

function ping($host, $port=80, $timeout=10)
{
    $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($fsock)
        return true;
    return false;
}

function dots($num, $sym = '.')
{
	$dots = "";
	for ($i = 0; $i < $num; ++$i)
		$dots .= $sym;
	return $dots;
}

function console($msg)
{
	if (DEBUG_SHOW_MSGS_CONSOLE)
		echo $msg;
}

?>