<?php

class SMSLib_HTTP_Client
{
	public static function get($url, $options=array())
	{
		$options = array(CURLOPT_HEADER=>FALSE, CURLOPT_URL=>$url,
							 CURLOPT_RETURNTRANSFER=>TRUE) + $options;
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		
		$result = curl_exec($curl);
		
		return json_decode($result, true);
	}

}