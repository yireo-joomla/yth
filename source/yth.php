<?php
/**
 * Yireo Template Helper for Joomla!
 *
 * @author Yireo (info@yireo.com)
 * @package Yth
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 * @version 0.11.0
 */

// Prevent direct access
defined('_JEXEC') or die();

// Define the base-path of this template
define('TEMPLATE_BASE', dirname(__FILE__));

/*
 * Yireo template class
 *
 * @package Yth
 */
class Yth 
{
	/*
	 * Document instance
	 */
	protected $doc = null;

	/*
	 * Application instance
	 */
	protected $app = null;

	/*
	 * JInput instance
	 */
	protected $input = null;

	/*
	 * Menu instance
	 */
	protected $menu = null;

	/*
	 * Constructor called when instantiating this class
	 */
	public function __construct()
	{
		// Fetch system variables
		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->menu = $this->app->getMenu();

		// Automatically reset the generator
		$this->doc->setGenerator(JFactory::getConfig()->get('config.sitename'));
	}

	/*
	 * Method to manually override the META-generator
	 *
	 * @access public
	 * @param string $generator
	 * @return null
	 */
	public function setGenerator($generator)
	{
		$this->doc->setGenerator($generator);
	}
	
	/*
	 * Method to remove MooTools from the page
	 *
	 * @access public
	 * @param null
	 * @return null
	 */
	public function removeMooTools()
	{
		$head = $this->doc->getHeadData();
		if (!empty($head['scripts']))
		{
			foreach ($head['scripts'] as $script => $scriptdata)
		{
				if (stristr($script, 'mootools') || stristr($script, 'caption.js') || stristr($script, 'media/system/js/core.js'))
				{
					unset($head['scripts'][$script]);
				}
			}
		}

		if (!empty($head['script']))
		{
			foreach ($head['script'] as $index => $script)
			{
				if (stristr($script, 'window.addEvent'))
				{
					unset($head['script'][$index]);
				}
			}
		}

		if (!empty($head['custom']))
		{
			foreach ($head['custom'] as $index => $script)
			{
				if (stristr($script, 'mootools'))
				{
					unset($head['custom'][$index]);
				}
			}
		}

		$this->doc->setHeadData($head);
	}
	
	/*
	 * Method to load the custom language file:
	 * /templates/TEMPLATE/language/en-GB/en-GB.custom.ini
	 *
	 * @access public
	 * @param null
	 * @return null
	 */
	public function loadCustomLanguageFile()
	{
		$language = JFactory::getLanguage();
		$language->load('custom' , dirname(__FILE__), $language->getTag(), true);
	}
	
	/*
	 * Method to get the HTML of a splitmenu
	 *
	 * @access public
	 * @param string $menu 
	 * @param int $startLevel
	 * @param int $endLevel
	 * @param bool $showChildren
	 * @return string
	 */
	public function getSplitMenu($menu = 'mainmenu', $startLevel = 0, $endLevel = 1, $showChildren = false) 
	{
		// Import the module helper
		jimport('joomla.application.module.helper');

		// Get a new instance of the mod_mainmenu module
		$module = JModuleHelper::getModule('mod_menu', $menu);

		if (!empty($module) && is_object($module))
		{
			// Construct the module parameters
			$params = array();
			$params[] = 'menutype=' . $menu;
			$params[] = 'cache=0';
			$params[] = 'startLevel=' . $startLevel;
			$params[] = 'endLevel=' . $endLevel;
			$params[] = 'showAllChildren=' . (int) $showChildren;
			$module->params = implode("\n", $params);

			// Construct the module options
			$options = array('style' => 'raw');

			// Render this module
			$renderer = $this->doc->loadRenderer('module');
			$output = $renderer->render($module, $options);

			return $output;
		}

		return null;
	}

	/*
	 * Method to determine whether a certain module is loaded or not
	 *
	 * @access public
	 * @param string $name
	 * @return bool
	 */
	public function hasModule($name = '') 
	{
		// Import the module helper
		jimport('joomla.application.module.helper');

		$instance = JModuleHelper::getModule($name);

		if (is_object($instance))
		{
			return true;
		}

		return false;
	}

	/*
	 * Copy of the original JDocumentHTML::countModules() method, but this copy skips empty modules as well
	 * 
	 * @access public
	 * @param string $condition
	 * @return integer
	 */
	public function countModules($condition)
	{
		$result = '';
		$words = explode(' ', $condition);

		for($i = 0; $i < count($words); $i+=2)
		{
			// odd parts (modules)
			$name = strtolower($words[$i]);
			$buffer = $this->doc->getBuffer('modules', $name);

			if(!isset($buffer) || $buffer === false || empty($buffer))	
			{
				$words[$i] = 0;
			}
			else
			{
				$words[$i] = count(JModuleHelper::getModules($name));
			}
		}

		$str = 'return ' . implode(' ', $words) . ';';

		return eval($str);
	}

	/*
	 * Method to get the parent Menu-Item of the current page
	 *
	 * @access public
	 * @param int $level
	 * @return string
	 */
	public function getActiveParent($level = 0) 
	{
		// Fetch the active menu-item
		$active = $this->menu->getActive();

		// Get the parent (at a certain level)
		$parent = $active->tree[$level];
		$parentItem = $this->menu->getItem($parent);

		// Return the title of this Menu-Item
		if (isset($parentItem->title))
		{
			return $parentItem->title;
		}

		if (isset($parentItem->name))
		{
			return $parentItem->title;
		}
	}

	/*
	 * Method to return the current Menu Item ID
	 *
	 * @access public
	 * @param null
	 * @return int
	 */
	public function getItemId() 
	{
		return $this->input->getInt('Itemid');
	}

	/*
	 * Method to check up a specific Menu Item
	 *
	 * @access public
	 * @param int
	 * @return bool
	 */
	public function isMenuItem($itemId) 
	{
		if ($this->input->getInt() == $itemId)
		{
			return true;
		}

		return false;
	}

	/*
	 * Method to fetch the current path
	 *
	 * @access public
	 * @param string $output Output type
	 * @return mixed
	 */
	public function getPath($output = 'array') 
	{
        $uri = JURI::getInstance();
        $path = $uri->getPath();
        $path = preg_replace('/^\//', '', $path);

        if ($output == 'array')
        {
            $path = explode('/', $path);
            return $path;
        }
		
        return $path;
	}

	/*
	 * Method to get the current language
	 *
	 * @access public
	 * @param null
	 * @return string
	 */
	public function getLanguage() 
	{
	    $language = JFactory::getLanguage();	

        return $language->getTag();
	}

	/*
	 * Method to determine whether the current page is the Joomla! homepage
	 *
	 * @access public
	 * @param null
	 * @return bool
	 */
	public function isHome($language = null) 
	{
		// Fetch the active menu-item
		$active = $this->menu->getActive();

		// Return whether this active menu-item is home or not
		return (boolean)$active->home;
	}

	/*
	 * Method to get the current sitename
	 *
	 * @access public
	 * @param null
	 * @return string
	 */
	public function getSitename() 
	{
		return JFactory::getConfig()->get('config.sitename');
	}

	/*
	 * Method to determine whether the current page is a article
	 *
	 * @access public
	 * @param null
	 * @return bool
	 */
	public function isArticle() 
	{
		return ($this->input->getCmd('option') == 'com_content' && $this->input->getCmd('view') == 'article'); 
	}

	/*
	 * Method to determine whether the current page is a blog
	 *
	 * @access public
	 * @param null
	 * @return bool
	 */
	public function isBlog() 
	{
		return ($this->input->getCmd('option') == 'com_content' 
			&& $this->input->getCmd('view') == 'category' 
			&& $this->input->getCmd('layout') == 'blog'); 
	}

	/*
	 * Method to check whether the current user is logged in
	 *
	 * @access public
	 * @param null
	 * @return bool
	 */
	public function isLoggedIn() 
	{
		$user = JFactory::getUser();

		return ($user->guest == 0) ? true : false;
	}

	/*
	 * Method to check whether the current user is a guest
	 *
	 * @access public
	 * @param null
	 * @return bool
	 */
	public function isGuest() 
	{
		$user = JFactory::getUser();

		return ($user->guest == 1) ? true : false;
	}

	/*
	 * Method to add a global title to every page title
	 *
	 * @access public
	 * @param string $global_title
	 * @return boolean
	 */
	public function addGlobalTitle( $global_title = null, $before = false) 
	{
		// Set a default global title
		if (empty($global_title))
		{
			$global_title = $this->getSitename();
		}

		// Get the current title
		$title = $this->doc->getTitle();

		// Determine if the title is already included
		if (stristr($title, $global_title))
		{
			return false;
		}

		// Add the global title to the current title
		if ($before == true)
		{
			$this->doc->setTitle($global_title . ' - ' . $title);
		}
		else
		{
			$this->doc->setTitle($title . ' - ' . $global_title );
		}

		return true;
	}

	/*
	 * Method to detect a certain browser type
	 *
	 * @access public
	 * @param string $shortname
	 * @return string
	 */
	public function isBrowser($shortname = 'ie6')
	{
		jimport('joomla.environment.browser'); 
		$browser = JBrowser::getInstance(); 

		$rt = false;
		switch($shortname)
		{
			case 'firefox':
			case 'ff':
				$rt = (stristr($browser->getAgentString(), 'firefox')) ? true : false;
				break;

			case 'ie':
				$rt = ($browser->getBrowser() == 'msie') ? true : false;
				break;

			case 'ie6':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getVersion() == '6.0') ? true : false;
				break;

			case 'ie7':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getVersion() == '7.0') ? true : false;
				break;

			case 'ie8':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getVersion() == '8.0') ? true : false;
				break;

			default:
				$rt = (stristr($browser->getAgentString(), $shortname)) ? true : false;
				break;

		}

		return $rt;
	}

	/*
	 * Method to include an image
	 *
	 * @access public
	 * @param string $image
	 * @return string
	 */
	public function image($image = null)
	{
		$template = $this->app->getTemplate();

		if (!is_file($image) && is_file(TEMPLATE_BASE . '/images/' . $image))
		{
			return 'templates/' . $template . '/images/' . $image;
		}
		elseif(!is_file($image) && is_file(TEMPLATE_BASE . '/' . $image))
		{
			return 'templates/' . $template . '/' . $image;
		}

		return $image;
	}

	/*
	 * Method to include an image as data-URI
	 *
	 * @access public
	 * @param string $image
	 * @return string
	 */
	public function datauri($image = null)
	{
		if (!is_file($image) && is_file(TEMPLATE_BASE . '/images/' . $image))
		{
			$image = TEMPLATE_BASE . '/images/' . $image;
		} 
		elseif(!is_file($image) && is_file(TEMPLATE_BASE . '/' . $image))
		{
			$image = TEMPLATE_BASE . '/' . $image;
		} 
		elseif(!is_file($image))
		{
			$image = JPATH_BASE . '/' . $image;
		}

		// Don't know what to do with this
		if (!is_file($image))
		{
			return $image;
		}

		// Fetch the content
		$image = realpath($image);
		$content = @file_get_contents($image);

		if (empty($content))
		{
			return null;
		}

		$mimetype = null; 
		if (preg_match('/\.gif$/i', $image))
		{
			$mimetype = 'image/gif';
		} 
		elseif (preg_match('/\.png$/i', $image)) 
		{
			$mimetype = 'image/png';
		}	
		elseif (preg_match('/\.webp$/i', $image))
		{
			$mimetype = 'image/webp';
		} 
		elseif (preg_match('/\.(jpg|jpeg)$/i', $image)) 
		{
			$mimetype = 'image/jpg';
		}

		if (!empty($content) && !empty($mimetype)) 
		{
			return 'data:' . $mimetype . ';base64,' . base64_encode($content);
		}
		return $image;
	}

	/*
	 * Method to debug certain settings
	 *
	 * @access public
	 * @param mixed $ips String or array with IP-addresses
	 * @return null
	 */
	public function isDebug($ips = null)
	{
		if (empty($ips))
		{
			return;
		} 
		elseif (is_string($ips))
		{
			$ips = array($ips);
		}

		foreach ($ips as $ip) 
		{
			if ($ip == $_SERVER['REMOTE_ADDR'])
			{
				return true;
			}
		}

		return false;
	}

	/*
	 * Helper-method to get the current Joomla! core version
	 * 
	 * @param null
	 * @return bool
	 */
	public function isJoomlaVersion($version = null)
	{
		JLoader::import('joomla.version');
		$jversion = new JVersion();

		if (version_compare( $jversion->RELEASE, $version, 'eq'))
		{
			return true;
		}

		return false;
	}

	/*
	 * Generate a list of useful CSS classes for the body
	 * 
	 * @param null
	 * @return bool
	 */
	public function getBodySuffix()
	{
		$classes = array();
		$classes[] = 'option-' . str_replace('_', '-', $this->input->getCmd('option'));
		$classes[] = 'view-' . $this->input->getCmd('view');
		$classes[] = 'layout-' . $this->input->getCmd('layout');
		$classes[] = 'item-' . $this->getItemId();
		$classes[] = 'path-' . implode('-', $this->getPath('array'));
		$classes[] = 'home-' . (int) $this->isHome();

		return implode(' ', $classes);
	}

	/*
	 * Return the JInput object
	 * 
	 * @param null
	 * @return bool
	 */
	public function getInput()
	{
		return $this->input;
	}

	/*
	 * Add a script
	 * 
	 * @param string
	 * @return null
	 */
	public function addJs($js)
	{
		$template = $this->app->getTemplate();

		return $this->doc->addScript('templates/' . $template . '/js/'.$js);
	}

	/*
	 * Add a stylesheet
	 * 
	 * @param string
     * @param bool
	 * @return null
	 */
	public function addCss($css, $add_suffix = true)
	{
        if (preg_match('/\.css/', $css) == false && $add_suffix)
        {
            $css .= '.css';
        }

		$template = $this->app->getTemplate();

		return $this->doc->addStylesheet('templates/yth/' . $template . '/'.$css);
	}
}

// Automatically instantiate the class
$yth = new Yth();
