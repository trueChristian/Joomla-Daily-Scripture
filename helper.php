<?php
/*-------------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
 ____                                                  ____                 __               __               __
/\  _`\                                               /\  _`\   __         /\ \__         __/\ \             /\ \__
\ \,\L\_\     __   _ __    ___ ___     ___     ___    \ \ \/\ \/\_\    ____\ \ ,_\  _ __ /\_\ \ \____  __  __\ \ ,_\   ___   _ __
 \/_\__ \   /'__`\/\`'__\/' __` __`\  / __`\ /' _ `\   \ \ \ \ \/\ \  /',__\\ \ \/ /\`'__\/\ \ \ '__`\/\ \/\ \\ \ \/  / __`\/\`'__\
   /\ \L\ \/\  __/\ \ \/ /\ \/\ \/\ \/\ \L\ \/\ \/\ \   \ \ \_\ \ \ \/\__, `\\ \ \_\ \ \/ \ \ \ \ \L\ \ \ \_\ \\ \ \_/\ \L\ \ \ \/
   \ `\____\ \____\\ \_\ \ \_\ \_\ \_\ \____/\ \_\ \_\   \ \____/\ \_\/\____/ \ \__\\ \_\  \ \_\ \_,__/\ \____/ \ \__\ \____/\ \_\
    \/_____/\/____/ \/_/  \/_/\/_/\/_/\/___/  \/_/\/_/    \/___/  \/_/\/___/   \/__/ \/_/   \/_/\/___/  \/___/   \/__/\/___/  \/_/

/------------------------------------------------------------------------------------------------------------------------------------/

	@version		2.0.x
	@created		22nd October, 2015
	@package		Sermon Distributor
	@subpackage		helper.php
	@author			Llewellyn van der Merwe <https://truechristian.church/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 

	A sermon distributor that links to Dropbox. 

/----------------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

class ModDailyScriptureHelper
{
	/**
	 * Scripture
	 *
	 * @var   array
	 * @since  1.0
	 */
	protected $scripture;

	/**
	 * Constructor.
	 *
	 * @param   Registry  $params the module settings
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $params = null)
	{
		// get the version
		$version = $params->get('version', 'kjv');
		// the link to the scripture for the day
		$path = "https://raw.githubusercontent.com/trueChristian/daily-scripture/master/scripture/$version/README.json";
		// get the scripture object
		$this->scripture = $this->getFileContents($path);
	}

	/**
	 * Waco method to get an scripture value
	 *
	 * @param   mixed  $name  Name of the value to retrieve.
	 *
	 * @return  mixed  The request value
	 *
	 * @since   1.0
	 */
	public function __get($name)
	{
		if ($this->checkScripture($name))
		{
			return $this->scripture->{$name};
		}
		return null;
	}

	/**
	 * get the file content
	 *
	 * @input	string $path   The path to get remotely 
	 *
	 * @returns object on success
	 *
	 * @since  1.0
	 */
	protected function getFileContents($path)
	{
		// use basic file get content for now
		if (($content = @file_get_contents($path)) !== FALSE)
		{
			// return if found
			if ($this->checkJson($content))
			{
				return json_decode($content);
			}
		}
		// use curl if available
		elseif (function_exists('curl_version'))
		{
			// start curl
			$ch = curl_init();
			// set the options
			$options = array();
			$options[CURLOPT_URL] = $path;
			$options[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12';
			$options[CURLOPT_RETURNTRANSFER] = TRUE;
			$options[CURLOPT_SSL_VERIFYPEER] = FALSE;
			// load the options
			curl_setopt_array($ch, $options);
			// get the content
			$content = curl_exec($ch);
			// close the connection
			curl_close($ch);
			// return if found
			if ($this->checkJson($content))
			{
				return json_decode($content);
			}
		}
		return false;
	}

	/**
	 * Check if have an json string
	 *
	 * @input	string   The json string to check
	 *
	 * @returns bool true on success
	 *
	 * @since  1.0
	 */
	protected function checkJson($string)
	{
		if ($this->checkString($string))
		{
			json_decode($string);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}

	/**
	 * Check if have a string with a length
	 *
	 * @input	string   The string to check
	 *
	 * @returns bool true on success
	 *
	 * @since  1.0
	 */
	protected function checkString($string)
	{
		if (isset($string) && is_string($string) && strlen($string) > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if we have an scripture object with value
	 *
	 * @input	key   The key being requested
	 *
	 * @returns bool true on success
	 *
	 * @since  1.0
	 */
	protected function checkScripture($key)
	{
		if (isset($this->scripture) && is_object($this->scripture) && isset($this->scripture->{$key}))
		{
			return true;
		}
		return false;
	}

}
