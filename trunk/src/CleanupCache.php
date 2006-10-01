<?php
/*
Copyright (C) 2006 by the WikiPlot project authors (See http://code.google.com/p/WikiPlot).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

/**
* File used to clear the cache
* 
* This file is supposed to be called as a cron script, to clear the cache on a regular basis.
* 
* @package WikiPlot
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/

/**
* Require cache class
*
* This class is needed to control the cache.
*/
require_once("cache.class.php");

//Create instance of cache
$Cache = new Cache;

//Cleanup cache
$Cache->CleanupCache();
?>
