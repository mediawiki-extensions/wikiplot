<?php
/**
* @package WikiPlot
*/

require_once("./PlotClass/plot.class.php");

$wgExtensionFunctions[] = "wfWikiPlotExtension";

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
	$values = split(";",$value,2);
	if(is_numeric($values[0])&&is_numeric($values[1]))
	{
		$SetTo1 = $values[0];
		$SetTo2 = $values[1];
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
	if(is_numeric($value))
	{
		$SetTo = $value;
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
	$values = split("",$value,3);
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

# The callback function for rendering plot
function RenderWikiPlot( $input, $argv, $parser = null  ) {
	if (!$parser) $parser =& $GLOBALS['wgParser'];
	# $argv is an array containing any arguments passed to the
	# extension like <example argument="foo" bar>..
	# Put this on the sandbox page:  (works in MediaWiki 1.5.5)
	#   <example argument="foo" argument2="bar">Testing text **example** in between the new tags</example>

	//Getting arguments from wikitext
	$output = "Text passed into example extension: <br/>$input";
	$output .= " <br/> and the value for the arg 'argument' is " . $argv["argument"];
	$output .= " <br/> and the value for the arg 'argument2' is: " . $argv["argument2"];

$Plot = new Plot();

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

//TODO: remeber to use width and height as x- and yspan if x-/yspan isn't provided.
/*
WikiML specification
<plot grid="true" caption="Caption text" axis="true" xspan="-10;10" yspan="-10;10" height="20" width="20" gridspace="x;y" captionfont="5" gridfont="1" gridcolor="200,200,200">
<graph label="Graph 1" color="0,0,255" font="3">5x^3</graph>
<graph label="Graph 2" color="0,255,0" font="3">2x^2</graph>
</plot>
*/

	//Render as wikitext:
	$localParser = new Parser();
	$output = $localParser->parse(";Test:This is rendered wikitext", $parser->mTitle, $parser->mOptions); //Once we test this, remember to check if adding parameters true, false OR false, true OR false OR true... see http://meta.wikimedia.org/wiki/MediaWiki_extensions_FAQ for more information on wikitext rendering in extensions
	$text = $output->getText();

	return $output;
}

?>
