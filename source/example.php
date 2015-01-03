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


// Add extra stylesheets
if ($yth->hasModule('mod_example')) $yth->addCss('mod_example');
if ($yth->getInput()->getCmd('option') == 'com_example') $yth->addCss('com_example');
if ($yth->isBrowser('ie6')) $this->addCss('ie6');
if ($yth->isDebug('127.0.0.1')) $this->addCss('debug.css');

?>

<?php
// Print body class
?>

<body class="<?php echo $yth->getBodySuffix(); ?>">


<img src="<?php echo $yth->datauri('logo.png'); ?>" />


<?php
// Splitmenu: Get the first level of the menu "mainmenu"
$top_menu_html = $yth->getSplitMenu('mainmenu', 0, 1);
echo $top_menu_html;

// Splitmenu: Get all but the first level of the menu "mainmenu"
$main_menu_html = $yth->getSplitMenu('mainmenu', 1, 9);
echo $main_menu_html;


// Splitmenu: Get the title of the active parent Menu-Item
echo $yth->getActiveParent();

?>
</body>
