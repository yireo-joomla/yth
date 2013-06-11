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
 *
 * Usage: 
 *      Place this file "css.php" in your template CSS-folder (templates/TEMPLATE/css/css.php)
 *      Optionally modify $defaults and $options
 *      Call this file from within "index.php" instead of calling your stylesheets
 *          <?php echo Yth::addCssPhp(); ?>
 *
 * Array $defaults:
 *      List of all your template stylesheets
 *
 * Array $options:
 *      zlib: Enable zlib compresssion when outputting data
 *      crunch: Remove newlines and comments from the resulting CSS (compression)
 *      remote_directory: Remote directory containing images (for instance: static server or a CDN)
 *  
 */

$stylesheets = array(
    'template.css',
);

// Modify options
$options = array(
    'crunch' => 1,
    'zlib' => 1,
);

/*************** DO NOT EDIT BELOW THIS LINE *********************/

// Initialize the class
$renderer = new CSSRenderer($options);
$renderer->setStylesheets($stylesheets);
$renderer->output();

/*
 * CSS Renderer class
 *
 * Usage:
 *      $renderer = new CSSRenderer(); // Get a new blanc object
 *      $renderer = new CSSRenderer($options); // Get a new object with default options
 *      $renderer->setOptions($options); // Set the options if they were not set yet
 *      $renderer->setStylesheets($stylesheets); // Set the options if they were not set yet
 */
class CSSRenderer
{
    /*
     * Flag to enable the removal of newlines 
     */
    private $crunch = 0;

    /*
     * Flag to enable zlib compression
     */
    private $zlib = 0;

    /*
     * Variable pointing to a remote template-directory serving as static server
     */
    private $remote_directory = null;

    /*
     * Constructor
     *
     * @access public
     * @param array $options
     * @return null
     */
    public function __construct($options)
    {
        if(!empty($options)) {
            foreach($options as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    /*
     * Method to set the default CSS-stylesheets
     *
     * @access public
     * @param array $defaults
     * @return null
     */
    public function setStylesheets($css)
    {
        $this->css = $css;
    }

    /*
     * Method to output all the parsed CSS
     *
     * @access public
     * @param null
     * @return null
     */
    public function output()
    {
        // Start output buffering
        if($this->zlib == 1 && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            @ob_start('ob_gzhandler');
        }

        // Output the CSS header
        header('Content-type: text/css; charset: UTF-8');

        // Get the stylesheets
        $css = $this->getStylesheets();

        // Gather the content
        $content = null;
        foreach($css as $file) {
            if(is_file($file)) {
                $content .= "/* File: $file */\n";
                $content .= file_get_contents($file);
            } else {
                $content .= "/* Error loading file $file */\n";
            }
        }

        // Parse the content
        $remote_directory = preg_replace( '/\/$/', '', trim($this->remote_directory));
        $ssl = (isset($_GET['ssl'])) ? (int)$_GET['ssl'] : 0;
        if(!empty($remote_directory) && $ssl == 0) {
            $content = str_replace('url(../images', 'url('.$remote_directory.'/images', $content);
        }

        // Compress the CSS by removing newlines
        if($this->crunch == 1) {

            $content = preg_replace('/\/\*([^\*]+)\*\//s', '', $content);
            $content = str_replace("\r\n", "", $content);
            $content = str_replace("\n", "", $content);
        }

        // Output the content
        print $content;

        // End output buffering
        if($this->zlib == 1 && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            ob_end_flush();
        }
    }

    /*
     * Method to get a list of the stylesheets that need to be loaded
     *
     * @access private
     * @param null
     * @return array
     */
    private function getStylesheets()
    {
        // Initialize the CSS array
        $css = array();

        // Add output depending on extra parameters
        if(isset($_GET['system']) && !empty($_GET['system'])) {
            $css[] = '../../system/css/system.css';
            $css[] = '../../system/css/general.css';
        }

        // Add output depending on extra GET-parameter
        if(isset($_GET['s']) && !empty($_GET['s'])) {
            $sheets = explode(',', $_GET['s']);
            foreach($sheets as $sheet) {

                // Make sure to append ".css" and remove whitespaces (NULL-bytes)
                if(preg_match('/\.css$/', $sheet) == false) $sheet = $sheet.'.css';
                $sheet = str_replace("\0", '', $sheet);
                $sheet = trim($sheet);

                // Determine the absolute file-path
                if(is_file($sheet) && is_readable($sheet)) {
                    $sheet = realpath($sheet);
                } else {
                    $sheet = realpath(JPATH_BASE.$sheet);
                }

                // If this absolute filepath lies outside of Joomla!, skip it
                if(is_file($sheet) && is_readable($sheet) && strpos($sheet, JPATH_BASE) === 0) {
                    $css[] = $sheet;
                }
            }
        }

        // Add the defaults to the CSS-array
        if(!empty($this->css)) {
            foreach($this->css as $sheet) {
                if(is_file($sheet)) {
                    $css[] = $sheet;
                }
            }
        }

        return $css;
    }
}

