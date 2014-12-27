<?php
/**
 * Example usage of the Yth library
 *
 * @author Yireo (info@yireo.com)
 * @package Yth
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 * @version 0.11.0
 */

// Prevent direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/* 
 * You can use the following examples in your own code:
 */

// Include the library itself
include_once (dirname(__FILE__).'/yth.php');

// Add a global title
$yth->addGlobalTitle('My Website Is Uber Cool');

// Remove MooTools
$yth->removeMooTools();

// Construct extra stylesheets
$extra_stylesheets = array();
if($yth->hasModule('mod_example')) $extra_stylesheets[] = 'mod_example';
if($yth->getInput()->getCmd('option') == 'com_example') $extra_stylesheets[] = 'components/com_example/css/style.css';
if($yth->isBrowser('ie6')) $extra_stylesheets[] = 'ie6';
if ($yth->isDebug('127.0.0.1')) $extra_stylesheets[] = 'debug';
?>

<?php
// Splitmenu: Get the first level of the menu "mainmenu"
$top_menu_html = $yth->getSplitMenu('mainmenu', 0, 1);
echo $top_menu_html;

// Splitmenu: Get all but the first level of the menu "mainmenu"
$main_menu_html = $yth->getSplitMenu('mainmenu', 1, 9);
echo $main_menu_html;

// Splitmenu: Get the title of the active parent Menu-Item
echo $yth->getActiveParent();

