<?php

/**
 * Panacea Mobile HTTP API.
 */
class SMSLib_API_Panacea extends SMSLib_HTTP_Client
{
	/**
	 * API url.
	 */
	public $url = 'http://api.panaceamobile.com/json';
	/**
	 * API account details and options.
	 *
	 * @var  string
	 */
	public $api_data;

	/**
	 * Constructor.
	 *
	 * @param  string  $username  api username
	 * @param  string  $password  password
	 * @param  string  $url       API address
	 */
	public function __construct($username=NULL, $password=NULL, $url=NULL)
	{
		if ($url !== NULL)
		{
			$this->url = $url;
		}
		$this->api_data = array('username'=>$username, 'password'=>$password, 'url'=>$url);
	}
	
	/**
	 * API actions control.
	 *
	 * @param   string  $action_name  API action name
	 * @param   array   $arguments    action argumenets, last is an option if it's an array
	 * @return  array                 Panacea json decoded response.
	 */
	public function __call($action_name, $arguments)
	{
		// Last value is passed to the HTTP library as options if it's an array
		if (is_array($arguments[count($arguments)-1]))
		{
			$curl_options = $arguments[count($arguments)-1];
			$arguments = array_slice($arguments, 0, count($arguments)-1, TRUE);
		}
		else
		{
			$curl_options = array();
		}

		$actions_list = SMSLib_HTTP_Client::get($this->url.'?action=list_actions');
		$actions = $actions_list['details'];
		if (array_key_exists($action_name, $actions))
		{
			$url = $this->url.'?action='.$action_name;
			if ( ! empty($actions[$action_name]))
			{
				// Username and password should take index 0 and 1.
				if ($actions[$action_name][1]['name'] == 'password')
				{
					array_unshift($arguments, $this->api_data['password']);
				}
				if ($actions[$action_name][0]['name'] == 'username')
				{
					array_unshift($arguments, $this->api_data['username']);
				}
				$params = ''; $required_args = 0;
				foreach ($actions[$action_name] as $arg_num => $arg)
				{
					if ( ! $arg['optional'])
					{
						$required_args++;
					}
					// Formated string will only contain placeholders for available arguments
					if (($arg_num < count($arguments)))
					{
						$params .= '&'.$arg['name'].'=%s';
					}
				}
				if ($required_args > count($arguments))
				{
					trigger_error($action_name.' action requires '.$args_count
						.' arguments ('.count($arguments).' given).', E_USER_ERROR);
				}
				$url = vsprintf($url.$params, $arguments);
			}
			return SMSLib_HTTP_Client::get($url, $curl_options);
		}
		else
		{
			trigger_error($action_name.' is not a valid API action', E_USER_ERROR);
		}

	}
	
}