<?php

/*
 * Envato API PHP Class
 *
 * This PHP Class was created in order to communicate with the new Envato API.
 *
 * Source: https://mr4web.com/
 * API Documentation: https://build.envato.com/api/
 *
 * Date: 02/09/2018 (D/M/Y)
 * Copyright 2018: mr4web.com
 */

namespace MR4Web_API\Markets;

class Envato {

	private $_APIURL = 'http://marketplace.envato.com/api/edge/';
	private $_getResponse = array();

	public function __construct ($envato_username, $envato_api_key)
	{
		$this->_APIURL .= $envato_username.'/'.$envato_api_key.'/verify-purchase:';
	}

	private function curl()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_APIURL);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = json_decode(curl_exec($ch), true);
		curl_close($ch);

		return $response;
	}

	public function verify_purchase($code_to_verify)
	{	
		$this->_APIURL .= urlencode($code_to_verify).'.json';

		$res = $this->curl();
		//print_r($res);
		if (isset($res['verify-purchase']['buyer']))
		{
			$this->_getResponse = $res;
			return true;
		}
		else
			return false;
	}

	public function get_response()
	{
		if(count($this->_getResponse))
			return false;
		else
			return $this->_getResponse;
	}
}


?>