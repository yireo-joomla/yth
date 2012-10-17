<?php
/**
 * Example usage of the Yth library
 *
 * @author Yireo (info@yireo.com)
 * @package Yth
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 * @version 0.9.0
 */

// Prevent direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/* 
 * You can use the following examples in your own code:
 */

// Include the library itself
include_once (dirname(__FILE__).DS.'yth.php');

// Add a global title
Yth::addGlobalTitle('My Website Is Uber Cool');

// Remove MooTools
Yth::removeMooTools();

// Construct extra stylesheets
$extra_stylesheets = array();
if(Yth::hasModule('mod_example')) $extra_stylesheets[] = 'mod_example';
if(JRequest::getCmd('option') == 'com_example') $extra_stylesheets[] = 'components/com_example/css/style.css';
if(Yth::isBrowser('ie6')) $extra_stylesheets[] = 'ie6';
if(Yth::isDebug('127.0.0.1')) $extra_stylesheets[] = 'debug';
?>

<!-- Add the CSS-link somewhere in your <head> section -->
<?php echo Yth::addCssPhp($extra_stylesheets); ?>

<?php
// Splitmenu: Get the first level of the menu "mainmenu"
$top_menu_html = Yth::getSplitMenu('mainmenu', 0, 1);
echo $top_menu_html;

// Splitmenu: Get all but the first level of the menu "mainmenu"
$main_menu_html = Yth::getSplitMenu('mainmenu', 1, 9);
echo $main_menu_html;

// Splitmenu: Get the title of the active parent Menu-Item
echo Yth::getActiveParent();

