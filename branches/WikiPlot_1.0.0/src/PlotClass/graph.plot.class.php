<?php
/*
Copyright (C) 2006 by the WikiPlot project authors (See http://code.google.com/p/WikiPlot).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


/**
* File containing Graph representation
* 
* This file contains a class used as representation of a Graph in plot's. It cannot be used independently, it is a requirement of plot.class.php
* 
* @package WikiPlot
* @subpackage PlotClass
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/

/**
* Representation of a graph
* 
* Class used to represente graphs on a plot.
* 
* @package WikiPlot
* @subpackage PlotClass
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/
class Graph
{
	/**
	* Label of graph
	*
	* This is the label or legend of the graph and will be shown in the corner of the plot, i the graphs color.
	*
	*@access public
	*@var string
	*/
	var $Label;

	/**
	* Font of the label
	*
	* This is the font of the label, defaults to 2, 1-5 are built-in and works as different fontsizes.
	*
	*@access public
	*@var integer
	*/
	var $LabelFont = 2;

	/**
	* Enable label
	*
	* Enable label, defaults to true, draws label if true.
	*
	*@access public
	*@var boolean
	*/
	var $EnableLabel = true;

	/**
	*Expression
	*
	*The mathematical expression representing the graph.
	*
	*@see EvalMath::evaluate()
	*@access public
	*@var string
	*/
	var $Exp;

	/**
	* Color of the graph
	*
	* Color of the graph and label, array of the RGB representation of the color.
	* Example: array($Red,$Green,$Blue);
	*
	*@access public
	*@var array
	*/
	var $Color = array(0,0,0);

	/**
	*Get hash
	*
	*Gets a hash of the graphs parameters. Actually is not a hashsum but just all parameter parsed as one string, this is done to reduce collision risk in Plot::GetHash().
	*
	*@access private
	*@return string Hash of all parameters.
	*/
	function GetHash()
	{
		return $this->Label ."_". $this->LabelFont ."_". $this->Exp ."_". $this->Color[0] . "_" . $this->Color[1] . "_" . $this->Color[2] . "_" . $this->EnableLabel;
	}

}
?>
