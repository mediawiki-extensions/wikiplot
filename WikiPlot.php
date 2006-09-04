<?php

$wgExtensionFunctions[] = "wfWikiPlotExtension";

function wfExampleExtension() {
    global $wgParser;
    $wgParser->setHook( "plot", "RenderWikiPlot" );
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


	//Render as wikitext:
	$localParser = new Parser();
	$output = $localParser->parse(";Test:This is rendered wikitext", $parser->mTitle, $parser->mOptions); //Once we test this, remember to check if adding parameters true, false OR false, true OR false OR true... see http://meta.wikimedia.org/wiki/MediaWiki_extensions_FAQ for more information on wikitext rendering in extensions
	$text = $output->getText()

	return $output;
}

?>
