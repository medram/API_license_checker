<?php

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

?>