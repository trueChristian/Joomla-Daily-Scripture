<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.net>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

class ModDailyScriptureHelper
{
	/**
	 * Params
	 *
	 * @var   Registry
	 * @since  1.0
	 */
	protected Registry $params;

	/**
	 * The Translation Version
	 *
	 * @var   string
	 * @since  1.1
	 */
	protected string $version;

	/**
	 * Scripture
	 *
	 * @var   mixed
	 * @since  1.0
	 */
	protected $scripture = null;

	/**
	 * Telegram Scripture
	 *
	 * @var   string
	 * @since  1.1
	 */
	protected string $telegram = '';

	/**
	 * Telegram Comments
	 *
	 * @var   string
	 * @since  1.1
	 */
	protected string $comments = '';

	/**
	 * Type
	 *
	 * @var   int
	 * @since  1.0
	 */
	protected int $type;

	/**
	 * The Telegram Post ID
	 *
	 * @var   int
	 * @since  1.1
	 */
	protected int $id = 0;

	/**
	 * Constructor.
	 *
	 * @param   Registry|null  $params  the module settings
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $params = null)
	{
		// we must have the params or we cant continue
		if ($params)
		{
			// set the global params
			$this->params = $params;
			// get the version
			$this->type = $params->get('type', 1);
			// get the version
			$this->version = $params->get('version', 'kjv');

			// implementation type = 1 = gitHub
			if ($this->type == 1)
			{
				// the link to the scripture for the day
				$path = "https://raw.githubusercontent.com/trueChristian/daily-scripture/master/scripture/{$this->version}/README.json";
				// get the scripture object
				$this->scripture = $this->getFileContents($path);
			}
		}
	}

	/**
	 * get the Telegram Post ID
	 *
	 * @return  int
	 * @since   1.1
	 */
	protected function getId(): int
	{
		if ($this->id == 0)
		{
			// the link to the scripture for the day
			$path = "https://raw.githubusercontent.com/trueChristian/daily-scripture/master/scripture/{$this->version}/README.tg.id";
			// get the scripture object
			$id = trim($this->getFileContents($path, false));

			// make sure we have a number here
			if (is_numeric($id))
			{
				$this->id = (int) $id;
			}
		}

		return $this->id;
	}

	/**
	 * Waco method to get an scripture value
	 *
	 * @param   mixed  $name  Name of the value to retrieve.
	 *
	 * @return  mixed  The request value
	 * @since   1.0
	 */
	public function __get($key)
	{
		if ($this->type == 1 && $this->checkScripture($key))
		{
			return $this->scripture->{$key};
		}
		elseif ($this->type == 1 && $key === 'local_link')
		{
			return $this->getLocalLink();
		}
		elseif ($this->type == 2 && $key === 'telegram')
		{
			return $this->getTelegram();
		}
		elseif ($key === 'comments')
		{
			return $this->getComments();
		}

		return null;
	}

	/**
	 * get the local link
	 *
	 * @return  string|null
	 *
	 * @since   1.2
	 */
	protected function getLocalLink(): ?string
	{
		$link = $this->params->get('local_link');

		if (empty($link))
		{
			return null;
		}

		return "$link/{$this->version}/{$this->book}/{$this->chapter}/{$this->verse}";
	}

	/**
	 * get the Telegram script
	 *
	 * @return  string|null
	 *
	 * @since   1.1
	 */
	protected function getTelegram(): ?string
	{
		if (empty($this->telegram))
		{
			$this->setTelegram();
		}

		return $this->checkString($this->telegram) ? $this->telegram : null;
	}

	/**
	 * set the Telegram script
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setTelegram()
	{
		// validate the ID
		if (($id = $this->getId()) > 0)
		{
			// get the color
			$color = $this->getColor();

			// get the dark theme
			$dark = $this->getDarkTheme();

			// get the width
			$width = $this->params->get('width', 100);

			// get the userpic
			$userpic = $this->getUserPic();

			// set the script
			$this->telegram = "<script async src=\"https://telegram.org/js/telegram-widget.js?22\" data-telegram-post=\"daily_scripture/$id\" data-width=\"$width%\"{$color}{$userpic}{$dark}></script>";
		}
	}

	/**
	 * get the Telegram Comment script
	 *
	 * @return  string|null
	 *
	 * @since   1.1
	 */
	protected function getComments(): ?string
	{
		if (empty($this->comments))
		{
			$this->setComments();
		}

		return $this->checkString($this->comments) ? $this->comments : null;
	}

	/**
	 * set the Telegram script
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	protected function setComments()
	{
		// should we add comments
		if (($id = $this->getId()) > 0 && $this->params->get('show_comments', 0) == 1)
		{
			// get the color
			$color = $this->getColor();

			// get the dark theme
			$dark = $this->getDarkTheme();

			// get comment limit
			$limit = $this->params->get('comments_limit', 5);

			// get comment Height
			$height = $this->getCommentHeight();

			// get color ful switch
			$colorful = $this->getCommentColorful();

			// set the script
			$this->comments = "<script async src=\"https://telegram.org/js/telegram-widget.js?22\" data-telegram-discussion=\"daily_scripture/$id\" data-comments-limit=\"$limit\"{$colorful}{$height}{$color}{$dark}></script>";
		}
	}

	/**
	 * get the comment height
	 *
	 * @return  string height value
	 *
	 * @since   1.1
	 */
	protected function getCommentHeight()
	{
		if (($height = $this->params->get('comments_height')) > 300)
		{
			return " data-height=\"$height\"";
		}

		return '';
	}

	/**
	 * get the comment color ful switch
	 *
	 * @return  string height value
	 *
	 * @since   1.1
	 */
	protected function getCommentColorful()
	{
		if (($colorful = $this->params->get('comments_colorful', 0)) == 1)
		{
			return " data-colorful=\"1\"";
		}
		return '';
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
	 * @returns mixed on success
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

		return null;
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
