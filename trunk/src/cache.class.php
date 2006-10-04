<?php
/*
Copyright (C) 2006 by the WikiPlot project authors (See http://code.google.com/p/WikiPlot).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

/**
* File used to control cache
* 
* This file provides functions to control the content of the cache.
* This file is made to make the software more maintain able, and as an interface to the cache for third party developers.
* 
* @package WikiPlot
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/

/**
* Require local settings
*
* This file is needed to control the cache correctly.
*/
require_once("LocalSettings.php");

/**
* Cache controlling class
* 
* Class used to control the cache.
* 
* @package WikiPlot
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/
class Cache
{
	/**
	*Cleanup the cache
	*
	*Cleans up the cache by removing old and unused files.
	*
	*@access public
	*@uses CleanupMaxAge()
	*@uses CleanupUnused()
	*/
	function CleanupCache()
	{
		$this->CleanupMaxAge();
		$this->CleanupUnused();
	}

	/**
	* Cleanup cache from old files
	*
	* Removes old files from the cache, see LocalSettings.php for settings.
	*
	* @access public
	*/
	function CleanupMaxAge()
	{
		$CachePath = $_SERVER["DOCUMENT_ROOT"] . WikiPlotCachePath;
		if ($cache = opendir($CachePath))
		{
			$MaxFileAge = time() - (WikiPlotCacheAge * 24 * 60 * 60);
			while (false !== ($file = readdir($cache)))
			{
				$FileAge = filemtime($CachePath . "/" . $file);
				if($FileAge>$MaxFileAge)
				{
					if(!(unlink($CachePath . "/" . $file)))
					{
						//TODO: throw some error!
					}
				}
			}
			closedir($cache);
		}else{
			//TODO: throw some error!
		}
		
	}

	/**
	* Cleanup unused files from cache
	*
	* Removes old unused files from the cache, see LocalSettings.php for settings.
	* This functions indentifies files as unused if they havn't been accessed for a long time. 
	*
	* @access public
	*/
	function CleanupUnused()
	{
		$CachePath = $_SERVER["DOCUMENT_ROOT"] . WikiPlotCachePath;
		if ($cache = opendir($CachePath))
		{
			$MaxFileAge = time() - (WikiPlotMaxUnusedAge * 24 * 60 * 60);
			while (false !== ($file = readdir($cache)))
			{
				$FileAge = fileatime($CachePath . "/" . $file);
				if($FileAge>$MaxFileAge)
				{
					if(!(unlink($CachePath . "/" . $file)))
					{
						//TODO: throw some error!
					}
				}
			}
			closedir($cache);
		}else{
			//TODO: throw some error!
		}
	}
	
	/**
	* Does file exist in cache
	*
	* Returns true or false depending on whether or not FileName Exist in cache.
	*
	* @access public
	* @param string $FileName Filename relative to cache.
	* @return boolean Whether or not FileName exist.
	*/
	function FileExist($FileName)
	{
		return file_exists($_SERVER["DOCUMENT_ROOT"] . WikiPlotCachePath . $FileName);
	}

	/**
	* Get file URL
	*
	* Gets the URL og the given FileName, returns false if the files doen't exist.
	* 
	* @access public
	* @uses FileExist()
	* @param string $FileName Filename relative to cache.
	* @return string Returns the URL of the file.
	*/
	function FileURL($FileName)
	{
		if($this->FileExist($FileName))
		{
			return WikiPlotCacheURL . "/" . $FileName;
		}else{
			return false;
		}
	}

	/**
	* Get cache Path
	*
	* Get absolute path to the cache, returns false if FileName exists. 
	*
	*@access public
	*@uses FileExist()
	*@param string $FileName Filename you want the path to, shortcut to detecting if file exists.
	*@return string Path to the cache, false if FileName exists.
	*/
	function CachePath($FileName = null)
	{
		if(!is_null($FileName))
		{
			if($this->FileExist($FileName))
			{
				return false;
			}else{
				return $_SERVER["DOCUMENT_ROOT"] . WikiPlotCachePath . $FileName;
			}
		}else{
			return $_SERVER["DOCUMENT_ROOT"] . WikiPlotCachePath;
		}
	}
}


?>
