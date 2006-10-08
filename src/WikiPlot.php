<?php
/*
Copyright (C) 2006 by the WikiPlot project authors (See http://code.google.com/p/WikiPlot).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

/**
* The MediaWiki extension
*
* This is the MediaWiki extension it self, everything else is just functions and liberaries for this file.
*
* @package WikiPlot
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/


/**
*Include plot.class.php
*
*Requires PlotClass to render plots.
*/
require_once("PlotClass/plot.class.php");

/**
*Include xml.class.php
*
*Requires XMLParser to parse xml to plot.
*/
require_once("xml.class.php");

/**
*Include cache.class.php
*
*Requires Cache to control the cache.
*/
require_once("cache.class.php");

/**
*Register the WikiPlot extension
*
*Makes sure the extension is called when MediaWiki is started.
*/
$wgExtensionFunctions[] = "wfWikiPlotExtension";

/**
*Add hooks
*
*Adds hooks so MediaWiki will perform callback, when it hits the wikiplot tag.
*/
function wfWikiPlotExtension() {
    global $wgParser;
    $wgParser->setHook( "wikiplot", "RenderWikiPlot" );
}

/**
*Deserialize boolean
*
*Deserializes a boolean value from string, this function is used when you want to deserialize parameters given in the WikiML.
*If it is impossible to deserialize the value, the output object is not initialized at all.
*
*@access private
*@param string $value The string you wish to deserialize.
*@param boolean &$SetTo The variable you want the values parsed to.
*/
function WikiPlotDeserializeBoolean($value,&$SetTo)
{
	if($value == "true")
	{
		$SetTo = true;	
	}
	elseif($value == "false")
	{
		$SetTo = false;
	}
}

/**
*Deserialize String
*
*Deserializes a string value from string, this function is used when you want to deserialize parameters given in the WikiML.
*If it is impossible to deserialize the value, the output object is not initialized at all. Usualy this function does nothing.
*
*@access private
*@param string $value The string you wish to deserialize.
*@param string &$SetTo The variable you want the values parsed to.
*/
function WikiPlotDeserializeString($value,&$SetTo)
{
	if(is_string($value))
	{
		$SetTo = $value;	
	}
	
}

/**
*Deserialize Coordiante
*
*Deserializes a 2 integers from string, this function is used when you want to deserialize parameters given in the WikiML.
*If it is impossible to deserialize the value, the output object is not initialized at all.
*
*@access private
*@param string $value The string you wish to deserialize.
*@param integer &$SetTo1 The variable you want the values parsed to.
*@param integer &$SetTo2 The variable you want the values parsed to.
*/
function WikiPlotDeserializeMixed($value,&$SetTo1,&$SetTo2)
{
	if(!is_null($value))
	{
		$values = explode(";",$value,2);
		if(is_numeric($values[0])&&is_numeric($values[1]))
		{
			$SetTo1 = $values[0];
			$SetTo2 = $values[1];
		}
	}
}

/**
*Deserialize Integer
*
*Deserializes a integer value from string, this function is used when you want to deserialize parameters given in the WikiML.
*If it is impossible to deserialize the value, the output object is not initialized at all. Usualy this function does nothing at all, just checks to see if the value can be parsed as an integer.
*
*@access private
*@param string $value The string you wish to deserialize.
*@param Integer &$SetTo The variable you want the values parsed to.
*/
function WikiPlotDeserializeInteger($value,&$SetTo)
{
	if(!is_null($value))
	{
		if(is_numeric($value))
		{
			$SetTo = $value;
		}
	}
}

/**
*Deserialize Color
*
*Deserializes an array representation of a rgb color from string, this function is used when you want to deserialize parameters given in the WikiML.
*This function can deserialize colors written as "255,255,255" (rgb) or "#000000" (hex).
*If it is impossible to deserialize the value, the output object is not initialized at all.
*
*@access private
*@param string $value The string you wish to deserialize.
*@param array &$SetTo The variable you want the values parsed to.
*/
function WikiPlotDeserializeColor($value,&$SetTo)
{
	if(!is_null($value))
	{
		$values = explode(",",$value,3);
		if(is_numeric($values[0])&&is_numeric($values[1])&&is_numeric($values[2]))
		{
			$SetTo = array($values[0],$values[1],$values[2]);
		}
		elseif(strstr($value,"#"))
		{
			$red = hexdec(substr($val, 1 , 2));
			$green = hexdec(substr($val, 3 , 2));
			$blue = hexdec(substr($val, 5 , 2));
			$SetTo = array($red,$green,$blue);
		}
	}
}

/**
*RenderWikiPlot CallBack function
*
*This is the function that handles MediaWiki callbacks, and renders the actual plot.
*
*@access private
*@param string $input The content of the wikiplot tag
*@param array $argv Hash-array of the parameters of the wikiplot tag, with parameter-name as key and parameter-value as value.
*@param Parser $parser The parser of MediaWiki, if null parser is obtained from global variable
*@uses WikiPlotDeserializeBoolean()
*@uses WikiPlotDeserializeString()
*@uses WikiPlotDeserializeMixed()
*@uses WikiPlotDeserializeInteger()
*@uses WikiPlotDeserializeColor()
*@uses XMLParser
*@uses Plot
*@uses Graph
*@uses Cache
*@return string HTML that can be directly inserted into any website.
*/
function RenderWikiPlot($input, $argv, $parser = null)
{
	//Get parser if not given as parameter
	if (!$parser) $parser =& $GLOBALS['wgParser'];
	/*Currently the parser*/

	//Creating instance of plot
	$Plot = new Plot();

	//Getting and deserializing parameters
	WikiPlotDeserializeBoolean($argv["grid"],$Plot->EnableGrid);
	WikiPlotDeserializeBoolean($argv["axis"],$Plot->EnableAxis);

	WikiPlotDeserializeString($argv["caption"],$Plot->Caption);

	WikiPlotDeserializeMixed($argv["xspan"],$Plot->MinX,$Plot->MaxX);
	WikiPlotDeserializeMixed($argv["yspan"],$Plot->MinY,$Plot->MaxY);
	WikiPlotDeserializeMixed($argv["gridspace"],$Plot->XGridSpace,$Plot->YGridSpace);

	WikiPlotDeserializeInteger($argv["height"],$Plot->Height);
	WikiPlotDeserializeInteger($argv["width"],$Plot->Width);
	WikiPlotDeserializeInteger($argv["captionfont"],$Plot->CaptionFont);
	WikiPlotDeserializeInteger($argv["gridfont"],$Plot->GridFont);

	WikiPlotDeserializeColor($argv["gridcolor"],$Plot->GridColor);

	//Parsing Xml
	$XmlParser = new XMLParser($input);
	$Graphs = $XmlParser->CreateInputArray();

	foreach($Graphs as $Graph)
	{
		$G = new Graph;
		if(!is_array($Graph[1]))
		{
			$G->Exp = $Graph[1];
			WikiPlotDeserializeString($Graph[0]["label"],$G->Label);
			WikiPlotDeserializeColor($Graph[0]["color"],$G->Color);
		}else{
			$G->Exp = $Graph[0];
		}
		array_push($Plot->Graphs,$G);
	}

	//Render the plot

	//Get instance of cache
	$cache = new cache();
	
	//Url of the current plot
	$PlotURL = "";

	$PlotFileName = $Plot->GetHash() . ".png";
	if(!$cache->FileExist($PlotFileName))
	{
		$Plot->SaveAs($cache->CachePath($PlotFileName));
	}else{
		$PlotURL = $cache->FileURL($PlotFileName);
	}

	$output = "<a href='$PlotURL' class='image' title='See the plot'><img src='$PlotURL'></a>";

	return $output;
}

?>
