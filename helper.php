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
	@author			Llewellyn van der Merwe <https://www.vdm.io/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 

	A sermon distributor that links to Dropbox. 

/----------------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;

class ModDailyScriptureHelper
{
	/**
	 * Params
	 *
	 * @var   Registry
	 * @since  1.0
	 */
	protected $params;

	/**
	 * Scripture
	 *
	 * @var   mix
	 * @since  1.0
	 */
	protected $scripture = null;

	/**
	 * Type
	 *
	 * @var   int
	 * @since  1.0
	 */
	protected $type;

	/**
	 * Telegram ID = Date
	 *
	 * @var   int
	 * @since  1.0
	 */
	protected $telegramID = 440;

	/**
	 * Telegram Date of the telegramID
	 *
	 * @var   string
	 * @since  1.0
	 */
	protected $telegramDate = 'Saturday 01 January, 2022';

	/**
	 * Constructor.
	 *
	 * @param   Registry  $params the module settings
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $params = null)
	{
		// set the global params
		$this->params = $params;
		// get the version
		$this->type = $params->get('type', 1);
		// implementation type = 1 = gitHub
		if ($this->type == 1)
		{
			// get the version
			$version = $params->get('version', 'kjv');
			// the link to the scripture for the day
			$path = "https://raw.githubusercontent.com/trueChristian/daily-scripture/master/scripture/$version/README.json";
			// get the scripture object
			$this->scripture = $this->getFileContents($path);
		}
		// implementation type = 2 = Telegram
		elseif ($this->type == 2)
		{
			$this->setTelegram();
		}
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
		if ($this->type == 1 && $this->checkScripture($name))
		{
			return $this->scripture->{$name};
		}
		elseif ($this->type == 2 && $name === 'telegram' && isset($this->telegram))
		{
			return $this->telegram;
		}
		return null;
	}

	/**
	 * get the Telegram script
	 *
	 * @return  string  data-color values
	 *
	 * @since   1.0
	 */
	protected function setTelegram()
	{
		// get today
		$today = $this->getTimeStamp();
		// get the telegram date
		$telegram_date = $this->getTimeStamp($this->telegramDate);
		// get the difference
		$difference = $today - $telegram_date;
		// get the number of days (plus one of the current date)
		$days = round($difference / 86400) + 1;
		// add the days
		$id = $this->telegramID + $days;
		// validate the ID
		if ($id > 0)
		{
			// get the width
			$width = $this->params->get('width', 100);
			// get the color
			$color = $this->getColor();
			// get the userpic
			$userpic = $this->getUserPic();
			// get the dark theme
			$dark = $this->getDarkTheme();
			// set the script
			$this->telegram = "<script async src=\"https://telegram.org/js/telegram-widget.js?15\" data-telegram-post=\"daily_scripture/$id\" data-width=\"$width%\"${color}${userpic}${dark}></script>";
		}
	}

	/**
	 * get today's time stamp based on user
	 *
	 * @return  int the timestamp
	 *
	 * @since   1.0
	 */
	protected function getTimeStamp($getDate = 'now')
	{
		// get today's date
		$date = new Date($getDate);
		// get the user time zone
		$timezone = Factory::getUser()->getTimezone();
		// update the date to the users time zone
		$date->setTimezone($timezone);
		// return the time stamp
		return $date->toUnix();
	}

	/**
	 * get the color
	 *
	 * @return  string  The telegram script
	 *
	 * @since   1.0
	 */
	protected function getColor()
	{
		// get the color
		$color = $this->params->get('color', 1);
		// convert to color
		switch($color)
		{
			case 2:
				// Cyan
				$color = '13B4C6';
				$dark_color = '39C4E8';
			break;
			case 3:
				// Green
				$color = '29B127';
				$dark_color = '72E350';
			break;
			case 4:
				// Yellow
				$color = 'CA9C0E';
				$dark_color = 'F0B138';
			break;
			case 5:
				// Red
				$color = 'E22F38';
				$dark_color = 'F95C54';
			break;
			case 6:
				// White
				$color = '343638';
				$dark_color = 'FFFFFF';
			break;
			case 7:
				// custom color
				$color = strtoupper(trim($this->params->get('custom_color', 'F646A4'), '#'));
				$dark_color = null;
			break;
			default:
				// default
				$color = null;
				$dark_color = null;
			break;
		}
		// load colors if set
		if ($color)
		{
			$color = " data-color=\"$color\"";
			// load dark color if set
			if ($dark_color)
			{
				$color = "$color data-dark-color=\"$dark_color\"";
			}
			return $color;
		}
		return '';
	}

	/**
	 * get the user pic state
	 *
	 * @return  string data-userpic value
	 *
	 * @since   1.0
	 */
	protected function getUserPic()
	{
		// get the author_photo
		$author_photo = $this->params->get('author_photo', 1);
		// convert to userpic
		switch($author_photo)
		{
			case 2:
				// Always show
				$userpic = 'true';
			break;
			case 3:
				// Always hide
				$userpic = 'false';
			break;
			default:
				// Auto
				$userpic = null;
			break;
		}
		// load userpic if set
		if ($userpic)
		{
			$userpic = " data-userpic=\"$userpic\"";
			return $userpic;
		}
		return '';
	}

	/**
	 * get the dark theme state
	 *
	 * @return  string data-dark value
	 *
	 * @since   1.0
	 */
	protected function getDarkTheme()
	{
		// get the theme
		$theme = $this->params->get('theme', 1);
		// only load if dark theme is set
		if ($theme == 2)
		{
			return  " data-dark=\"1\"";
		}
		return '';
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
	protected function getFileContents($path, $json = true)
	{
		// use basic file get content for now
		if (($content = @file_get_contents($path)) !== FALSE)
		{
			// return if found
			if ($json)
			{
				if ($this->checkJson($content))
				{
					return json_decode($content);
				}
			}
			elseif ($this->checkString($content))
			{
				return $content;
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
			if ($json)
			{
				if ($this->checkJson($content))
				{
					return json_decode($content);
				}
			}
			elseif ($this->checkString($content))
			{
				return $content;
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
