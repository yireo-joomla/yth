yth (Yireo Template Helper)
===========================

Quick Description: A set PHP-scripts to aid in Joomla! template development

When building a Joomla! template, in general, you have the choice of modifying an existing Joomla! template, building a new template based upon a full-blown templating
framework (WARP, Gantry, etcetera), or writing your own template from scratch. For this third alternative - writing your template from the base up - you might need
simple PHP-functions that accomplish complicated tasks: This is where Yth comes to the rescue.

Yth offers a set of methods for common tasks: Building a splitmenu; Compressing CSS-files and JavaScript-files; Removing MooTools; Check for modules being enabled;
Checks for the current page (isArticle, isHome); Image conversion to data-URIs.

Project-page: http://www.yireo.com/software/labs/template-helper
Usage: http://www.yireo.com/software/labs/template-helper/usage

New in Yth 0.10.0:
* Addition to GitHub
* Joomla! 3.0 compatibility
* E\_STRICT compliant
* Class is no abstract; no instance possible
* Merging of JavaScript-files through Yth::addJsPhp method
* New merger-file js/js.php
