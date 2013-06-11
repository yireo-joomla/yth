<?php
/**
 * Yireo Template Helper for Joomla!
 *
 * @author Yireo (info@yireo.com)
 * @package Yth
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 * @version 0.10.2
 */

// Prevent direct access
defined('_JEXEC') or die();

// Wipe out the current generator tag
$document = JFactory::getDocument();
$document->setGenerator(JFactory::getConfig()->get('config.sitename'));

// Define the base-path of this template
define('TEMPLATE_BASE', dirname(__FILE__));

/*
 * Yireo template class
 *
 * @static
 * @package Yth
 */
abstract class Yth 
{
    /*
     * Method to manually override the META-generator
     *
     * @static
     * @access public
     * @param string $generator
     * @return null
     */
    static public function setGenerator($generator)
    {
        $document = JFactory::getDocument();
        $document->setGenerator($generator);
    }
    
    /*
     * Method to remove MooTools from the page
     *
     * @static
     * @access public
     * @param null
     * @return null
     */
    static public function removeMooTools()
    {
        $document = JFactory::getDocument();
        $head = $document->getHeadData();
        if(!empty($head['scripts'])) {
            foreach($head['scripts'] as $script => $scriptdata) {
                if(stristr($script, 'mootools') || stristr($script, 'caption.js') || stristr($script, 'media/system/js/core.js')) {
                    unset($head['scripts'][$script]);
                }
            }
        }

        if(!empty($head['script'])) {
            foreach($head['script'] as $index => $script) {
                if(stristr($script, 'window.addEvent')) {
                    unset($head['script'][$index]);
                }
            }
        }

        if(!empty($head['custom'])) {
            foreach($head['custom'] as $index => $script) {
                if(stristr($script, 'mootools')) {
                    unset($head['custom'][$index]);
                }
            }
        }

        $document->setHeadData($head);
    }
    
    /*
     * Method to load the custom language file:
     * /templates/TEMPLATE/language/en-GB/en-GB.custom.ini
     *
     * @static
     * @access public
     * @param null
     * @return null
     * @todo Untested under Joomla! 2.5 and 3.0
     */
    static public function loadCustomLanguageFile()
    {
        $language = JFactory::getLanguage();
        $language->load('custom' , dirname(__FILE__), $language->getTag(), true);
    }
    
    /*
     * Method to get the HTML of a splitmenu
     *
     * @static
     * @access public
     * @param string $menu 
     * @param int $startLevel
     * @param int $endLevel
     * @param bool $showChildren
     * @return string
     */
    static public function getSplitMenu( $menu = 'mainmenu', $startLevel = 0, $endLevel = 1, $showChildren = false ) 
    {
        // Import the module helper
        jimport('joomla.application.module.helper');

        // Get a new instance of the mod_mainmenu module
        if(self::isJoomla15()) {
            $module = JModuleHelper::getModule('mod_mainmenu', $menu);
        } else {
            $module = JModuleHelper::getModule('mod_menu', $menu);
        }

        if(!empty($module) && is_object($module)) {

            // Construct the module parameters
            $params = array();
            $params[] = (self::isJoomla15()) ? 'menu-type='.$menu : 'menutype='.$menu;
            $params[] = 'cache=0';
            $params[] = 'startLevel='.$startLevel;
            $params[] = 'endLevel='.$endLevel;
            $params[] = 'showAllChildren='.(int)$showChildren;
            $module->params = implode("\n", $params);

            // Construct the module options
            $options = array('style' => 'raw');

            // Render this module
            $document = JFactory::getDocument();
            $renderer = $document->loadRenderer('module');
            $output = $renderer->render($module, $options);
            return $output;
        }

        return null;
    }

    /*
     * Method to determine whether a certain module is loaded or not
     *
     * @static
     * @access public
     * @param string $name
     * @return bool
     */
    static public function hasModule($name = '') 
    {
        // Import the module helper
        jimport('joomla.application.module.helper');

        $instance = JModuleHelper::getModule($name);
        if(is_object($instance)) {
            return true;
        }

        return false;
    }

    /*
     * Copy of the original JDocumentHTML::countModules() method, but this copy skips empty modules as well
     * 
     * @static
     * @access public
     * @param string $condition
     * @return integer
     */
    static public function countModules($condition)
    {
        $result = '';
        $document = JFactory::getDocument();

        $words = explode(' ', $condition);
        for($i = 0; $i < count($words); $i+=2)
        {
            // odd parts (modules)
            $name = strtolower($words[$i]);
            $buffer = $document->getBuffer('modules', $name);
            if(!isset($buffer) || $buffer === false || empty($buffer)) {
                $words[$i] = 0;
            } else {
                $words[$i] = count(JModuleHelper::getModules($name));
            }
        }

        $str = 'return '.implode(' ', $words).';';

        return eval($str);
    }

    /*
     * Method to get the parent Menu-Item of the current page
     *
     * @static
     * @access public
     * @param int $level
     * @return string
     */
    static public function getActiveParent($level = 0) 
    {
        // Fetch the active menu-item
        $menu = JFactory::getApplication()->getMenu();
        $active = $menu->getActive();

        // Get the parent (at a certain level)
        $parent = $active->tree[$level];
        $parentItem = $menu->getItem($parent);

        // Return the title of this Menu-Item
        if(isset($parentItem->title)) return $parentItem->title;
        if(isset($parentItem->name)) return $parentItem->title;
    }

    /*
     * Method to determine whether the current page is the Joomla! homepage
     *
     * @static
     * @access public
     * @param null
     * @return bool
     */
    static public function isHome() 
    {
        // Fetch the active menu-item
        $menu = JFactory::getApplication()->getMenu();
        $active = $menu->getActive();

        // Return whether this active menu-item is home or not
        return (boolean)$active->home;
    }

    /*
     * Method to get the current sitename
     *
     * @static
     * @access public
     * @param null
     * @return string
     */
    static public function getSitename() 
    {
        return JFactory::getConfig()->get('config.sitename');
    }

    /*
     * Method to determine whether the current page is a Joomla! article
     *
     * @static
     * @access public
     * @param null
     * @return bool
     */
    static public function isArticle() 
    {
        return (JRequest::getCmd('option') == 'com_content' && JRequest::getCmd('view') == 'article'); 
    }

    /*
     * Method to add a global title to every page title
     *
     * @static
     * @access public
     * @param string $global_title
     * @return boolean
     */
    static public function addGlobalTitle( $global_title = null, $before = false) 
    {
        // Set a default global title
        if(empty($global_title)) {
            $global_title = self::getSitename();
        }

        // Get the current title
        $document = JFactory::getDocument();
        $title = $document->getTitle();

        // Determine if the title is already included
        if(stristr($title, $global_title)) {
            return false;
        }

        // Add the global title to the current title
        if($before == true) {
            $document->setTitle($global_title.' - '.$title);
        } else {
            $document->setTitle($title . ' - ' . $global_title );
        }

        return true;
    }

    /*
     * Method to detect a certain browser type
     *
     * @static
     * @access public
     * @param string $shortname
     * @return string
     */
    static public function isBrowser($shortname = 'ie6')
    {
        jimport('joomla.environment.browser'); 
        $browser = JBrowser::getInstance(); 

        $rt = false;
        switch($shortname) {
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
     * Method to construct the URL for the Yireo CSS/PHP-script
     *
     * @static
     * @access public
     * @param array $stylesheets
     * @param bool $system_css
     * @return string
     */
    static public function addCssPhp($stylesheets, $system_css = false) 
    {
        $template = JFactory::getApplication()->getTemplate();
        $path = 'templates/'.$template.'/'.self::loadCssPhp($stylesheets, $system_css);
        echo '<link rel="stylesheet" href="'.$path.'" type="text/css" />';
    }

    /*
     * Method to construct the URL for the Yireo CSS/PHP-script
     *
     * @static
     * @access public
     * @param array $extra
     * @return string
     */
    static public function loadCssPhp($stylesheets = array(), $system_css = false) 
    {
        // The actual file
        $css_php = 'css/css.php';

        // Detect component CSS automatically
        $option = JRequest::getCmd('option');
        if(is_file(dirname(__FILE__).'/css/'.$option.'.css')) {
            $stylesheets[] = $option;
        }

        // Load the sheet options
        $options = array();
        if(!empty($stylesheets) && is_array($stylesheets)) {
            $options[] = 's='.implode(',', $stylesheets);
        }

        // Add a SSL-flag
        if(JURI::getInstance()->isSsl()) {
            $options[] = 'ssl=1';
        }

        // Add the system CSS flag
        if($system_css == true) {
            $options[] = 'system=1';
        }

        if(!empty($options)) {
            $css_php .= '?'.implode('&amp;', $options);
        }
        return $css_php;
    }

    /*
     * Method to construct the URL for the Yireo JS/PHP-script
     *
     * @static
     * @access public
     * @param array $scripts
     * @return string
     */
    static public function addJsPhp($scripts)
    {
        $template = JFactory::getApplication()->getTemplate();
        $path = 'templates/'.$template.'/'.self::loadJsPhp($scripts);
        echo '<script type="text/javascript" src="'.$path.'"></script>';
    }

    /*
     * Method to construct the URL for the Yireo JS/PHP-script
     *
     * @static
     * @access public
     * @param array $extra
     * @return string
     */
    static public function loadJsPhp($scripts = array())
    {
        // The actual file
        $js_php = 'js/js.php';

        // Detect component CSS automatically
        $option = JRequest::getCmd('option');
        if(is_file(dirname(__FILE__).'/js/'.$option.'.js')) {
            $scripts[] = $option;
        }

        // Load the sheet options
        $options = array();
        if(!empty($scripts) && is_array($scripts)) {
            $options[] = 's='.implode(',', $scripts);
        }

        // Add a SSL-flag
        if(JURI::getInstance()->isSsl()) {
            $options[] = 'ssl=1';
        }

        if(!empty($options)) {
            $js_php .= '?'.implode('&amp;', $options);
        }
        return $js_php;
    }

    /*
     * Method to include an image
     *
     * @static
     * @access public
     * @param string $image
     * @return string
     */
    static public function image($image = null)
    {
        $template = JFactory::getApplication()->getTemplate();
        if(!is_file($image) && is_file(TEMPLATE_BASE.'/images/'.$image)) {
            return 'templates/'.$template.'/images/'.$image;
        } elseif(!is_file($image) && is_file(TEMPLATE_BASE.'/'.$image)) {
            return 'templates/'.$template.'/'.$image;
        }
        return $image;
    }

    /*
     * Method to include an image as data-URI
     *
     * @static
     * @access public
     * @param string $image
     * @return string
     */
    static public function datauri($image = null)
    {
        if(!is_file($image) && is_file(TEMPLATE_BASE.'/images/'.$image)) {
            $image = TEMPLATE_BASE.'/images/'.$image;
        } elseif(!is_file($image) && is_file(TEMPLATE_BASE.'/'.$image)) {
            $image = TEMPLATE_BASE.'/'.$image;
        } elseif(!is_file($image)) {
            $image = JPATH_BASE.'/'.$image;
        }

        // Don't know what to do with this
        if(!is_file($image)) {
            return $image;
        }

        // Fetch the content
        $image = realpath($image);
        $content = @file_get_contents($image);
        if(empty($content)) {
            return null;
        }

        $mimetype = null; 
        if(preg_match('/\.gif$/i', $image)) {
            $mimetype = 'image/gif';
        } elseif(preg_match('/\.png$/i', $image)) {
            $mimetype = 'image/png';
        } elseif(preg_match('/\.webp$/i', $image)) {
            $mimetype = 'image/webp';
        } elseif(preg_match('/\.(jpg|jpeg)$/i', $image)) {
            $mimetype = 'image/jpg';
        }

        if(!empty($content) && !empty($mimetype)) {
            return 'data:'.$mimetype.';base64,'.base64_encode($content);
        }
        return $image;
    }

    /*
     * Method to debug certain settings
     *
     * @static
     * @access public
     * @param mixed $ips String or array with IP-addresses
     * @return null
     */
    static public function isDebug($ips = null)
    {
        if(empty($ips)) {
            return;
        } elseif(is_string($ips)) {
            $ips = array($ips);
        }

        foreach($ips as $ip) {
            if($ip == $_SERVER['REMOTE_ADDR']) {
                return true;
            }
        }

        return false;
    }

    /*
     * Helper-method to get the current Joomla! core version
     * 
     * @param null
     * @return string
     */
    static public function getJoomlaVersion()
    {
        JLoader::import( 'joomla.version' );
        $version = new JVersion();
        return $version->getShortVersion();
    }

    /*
     * Helper-method to get the current Joomla! core version
     * 
     * @param null
     * @return bool
     */
    static public function isJoomlaVersion($version = null)
    {
        JLoader::import( 'joomla.version' );
        $jversion = new JVersion();
        if(version_compare( $jversion->RELEASE, $version, 'eq')) {
            return true;
        }
        return false;
    }

    /*
     * Helper-method to get the current Joomla! core version
     * 
     * @param null
     * @return bool
     */
    static public function isJoomla15()
    {
        return self::isJoomlaVersion('1.5');
    }
}
