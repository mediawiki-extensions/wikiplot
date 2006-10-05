<?php
/**
* File used to store settings
* 
* This file, is supposed to be manipulated by the user, it contains settings for WikiPlot. Primarily for the caching functionallity.
* 
* @package WikiPlot
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/

/**
* Path to the cache
*
* Path to the cache, relative to the DOCUMENT_ROOT.
*
*@see $CacheURL
*@var string Path relative to DOCUMENT_ROOT
*/
define("WikiPlotCachePath","./cache/");

/**
* URL to cache
*
* URL to cache directory define in $CachePath.
*
*@see $CachePath
*@var string absolute url
*/
define("WikiPlotCacheURL","http://example.com/cache/");

/**
* Max Cache Age
*
* Maximum cache age in days. Delete a file older than...
* if 0 Cache never expires.
*
*@var Integer Cache age in days.
*/
define("WikiPlotCacheAge",0);

/**
* Max Unused Age
*
* Maximun unused age before deletion.
*
*@var Integer Age in days.
*/
define("WikiPlotMaxUnusedAge",14);
?>
