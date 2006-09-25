<?php
/**
* @package WikiPlot
*/

require_once("./PlotClass/plot.class.php");

$wgExtensionFunctions[] = "wfWikiPlotExtension";

function wfWikiPlotExtension() {
    global $wgParser;
    $wgParser->setHook( "plot", "RenderWikiPlot" );
}

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

function WikiPlotDeserializeString($value,&$SetTo)
{
	if(is_string($value))
	{
		$SetTo = $value;	
	}
	
}

function WikiPlotDeserializeMixed($value,&$SetTo1,&$SetTo2)
{
	$values = split(";",$value,2);
	if(is_numeric($values[0])&&is_numeric($values[1]))
	{
		$SetTo1 = $values[0];
		$SetTo2 = $values[1];
	}
}

function WikiPlotDeserializeInteger($value,&$SetTo)
{
	if(is_numeric($value))
	{
		$SetTo = $value;
	}
}

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
/*
WikiML specification
<plot grid="true" caption="Caption text" axis="true" xspan="-10;10" yspan="-10;10" height="20" width="20" gridspace="x;y" captionfont="5" gridfont="1" gridcolor="200,200,200">
<graph color="0,0,255" font="3">5x^3</graph>
<graph color="0,255,0" font="3">2x^2</graph>
</plot>
*/

	//Render as wikitext:
	$localParser = new Parser();
	$output = $localParser->parse(";Test:This is rendered wikitext", $parser->mTitle, $parser->mOptions); //Once we test this, remember to check if adding parameters true, false OR false, true OR false OR true... see http://meta.wikimedia.org/wiki/MediaWiki_extensions_FAQ for more information on wikitext rendering in extensions
	$text = $output->getText()

	return $output;
}

?>
