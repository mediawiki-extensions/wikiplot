<?php
/*
Copyright (C) 2006 by the WikiPlot project authors (See http://code.google.com/p/WikiPlot).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

/**
* The file contains XMLParser class
* 
* This file contains the XMLParser class which parses the XML data to
* a multidimensional array.
* 
* @package WikiPlot
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/

/**
 * XMLParser class
 * 
 * This class parses a given XML data to a multidimensional array by using
 * a user-defined tag. The default tag is <graph>. The example below explains
 * how the class works.
 * <code>
 * <?php
 * $xml_data = "<root> 
 *      		<graph color='234,234,233' label='string'>x^2+5</graph>	
 *      		<another_tag name='tag'>This tag</another_tag>
 *      		<graph>x^2+5</graph>
 *      		</root>";
 *
 * $xml = new XMLParser($xml_data);
 * print_r($xml->CreateInputArray());
 * ?>  
 * OUTPUT:
 * Array
 * (
 *   [0] => Array
 *       (
 *           [0] => Array
 *               (
 *                   [color] => 234,234,233
 *                   [label] => string
 *               )
 *           [1] => x^2+5
 *       )
 *   [1] => Array
 *       (
 *           [0] => x^2+5
 *       )
 * )
 * </code>
 * 
 * @package WikiPlot
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
 * @author WikiPlot development team.
 * @copyright Copyright 2006, WikiPlot development team.
 */
class XMLParser {
	 
   /**
    * Created XML Parser
    *
    * Is a resource handle and referenced to be used by athor XML functions
    * @access private
    */
   var $Parser;
   /**
    * XML data given by user
    *
    * Stores the XML data given by user as it is
    * 
    * @var string
    * @access private
    */
   var $Input;
   /**
    * An interested tag in given XML data
    *
    * The variable stores attribute(s) and data of an interested tag not
    * the tag it selv <tag>. For example:
    * <code> 
    * If this is an interested tag
    * <graph color='23,25,200' lable='string'>2x^3+3x</graph> the variable
    * Variable $Tag will look like this:
    * Array
    * (
    *    [0] => Array
    *           (
    *              [color] => 23,25,200
    *              [lable] => string
    *           )
    *    [1] => 2x^3+3x
    * )
    * </code>
    * As you can see the first element in the array is an array and it
    * will always be an array if the interested tag has attribute(s). The second
    * element in the array will be the data of the tag as string. One more thing
    * to be notes is that the array can not contain more then two elements, while one
    * element is possible.
    * 
    * @var array
    * @access private
    */
   var $Tag;
   /**
    * Attributes of interested tag
    * 
    * The variable will always be an array whether the interested tag has any
    * attributes or not. If the interested tag has any attribute the $Attributes
    * variable will be used otherwise it will be ignored.
    *
    * @var array
    * @access private
    */
   var $Attributes;
   /**
    * Data of the tag
    * 
    * The variable will store the data of the tag. For example
    * <tag> tag data </tag>
    * $TagData = "tag data";
    *
    * @var array
    * @access private
    */
   var $TagData;
   /**
    * All interested tags
    * 
    * The variable will store alle the interested tags found in the
    * given XML data.
    *
    * @var array
    * @access private
    */
   var $Tags;
   /**
    * The interested tag
    * 
    * The variable is our iterested tag. It means the tag that we are
    * iterested to finde in the given XML data. 
    * The way you should definde your interested tag is as follows:
    * If your interested tag is <Tag> than you should change the
    * $Separator variable to XMLParser::Separator = "<Tag" not "<Tag>"
    * or something else!
    *
    * @var string
    * @access public
    */
   var $Separator;
   /**
    * Constructor of XMLParser class
    *
    * The function initializes the fallowing variables:
    * $Parser, $Input, $Tags, $Attributes and $Separator.
    * It makes it possible to use XML Parser within an object
    * by using the function xml_set_object. Besides it uses also
    * two more XML Parser Functions xml_set_element_handler(), 
    * xml_set_character_data_handler() and xml_parser_free().
    * 
    * @access private  
    * @param string $Data XML Input Data from user
    * @return XMLParser
    * @uses $Parser
    * @uses $Input
    * @uses $Tags
    * @uses $Attributes
    * @uses ExplodeInputData()
    * @uses Parse()
    * @uses OpenTag()
    * @uses CloseTag()
    * @uses GetCharData()
    */
   function XMLParser($Data)
   {
   	   //Initialize $Parser and creat an XML Parser to use later on
   	   $this->Parser = xml_parser_create();
       //Initialize $Input and set it equal to $Data (XML from user)
   	   $this->Input = $Data;
   	   //Initialize $Tags to be an array
   	   $this->Tags = array();
   	   //Initialize $Attributes to be an array
   	   $this->Attributes = array();
   	   //Initialize $Separator to be an array
   	   $this->Separator = "<graph";
       
   	   //Set XML Parser to use it within object
       xml_set_object($this->Parser, $this);
       //Set up start and end element handlers for the parser
       xml_set_element_handler($this->Parser, "OpenTag", "CloseTag");
       //Set up character data handler for the parser
       xml_set_character_data_handler($this->Parser, "GetCharData");
            
       //Call ExplodeInputData() to get the interested tags 
       $this->ExplodeInputData();
       
       //Call Parse() to parse the $Input
       $this->Parse($this->Input);
 	   
       //Free the XML parser to later use
       xml_parser_free($this->Parser);
   }
	
   /**
    * Parses the given XML data
    *
    * The function uses xml_parse() function from XML Parser Functions in PHP
    * and parses only the first tag in the given XML data and ignores
    * everything else. So you can not use it for multitag XML data.
    * The function also calls CreateTagArray() to generate tag attribute(s)
    * and data to an array.
    * 
    * @access private
    * @param string $Data
    * @uses CreateTagArray()
    */
   function Parse($Data)
   {
       //Parse XML Data using the $Parser
   	   xml_parse($this->Parser, $Data);
       //Put returned values (Attribute(s) and TagData)
       //from XML praser into an array called $Tag
       $this->CreateTagArray();
   }
   
   /**
    * Puts parsed data into an array
    * 
    * The function takes the variables $Attributes and $TagData and
    * puts them into an array called $Tag. The first element in the
    * array will be Attribute(s) of the interested tag and the second
    * element will be the data of the tag. If Attribute does not exist
    * the first element will then be the data of the tag. 
    *
    * @access private
    * @uses $Attributes
    * @uses $TagData
    * @uses $Tag 
    */
   function CreateTagArray()
   {
   	   if (!empty($this->Attributes) && !empty($this->TagData))
   	   {
   	      $this->Tag = array($this->Attributes, $this->TagData);	
   	   }
   	   else 
   	   {
   	   	  $this->Tag = array($this->TagData);
   	   }
   }

   /**
    * Findes the interested tag in XML Data
    * 
    * The function uses explode() function and the $Separator to finde
    * the interested tag in the given XML Data. When the tags are found
    * it puts them into array called $Tags. 
    *
    * @access private
    * @uses $Separator
    * @uses $Input
    * @uses $Tags
    */
   function ExplodeInputData()
   {
   	   //Split the given XML data by using $Separator
   	   $InterestedTags = explode($this->Separator , $this->Input); 
   	   
   	   //Go through the array containing the interesting tags
   	   //NOTICE: $i must be = 1 because the array contains
   	   //nothing on 0 position
   	   //NOTICE: $ must be < lenght of the array and not <= because
   	   //the last element in the array is not interesting.
   	   for ($i=1; $i < count($InterestedTags); $i++)
   	   {
   	   	   //Put the $Separator into the tag
   	   	   //(the separator vanishes when exploding the data)
   	   	   //fx. If the separator is <tag. The following will take place.
   	   	   //<tag>Hello</tag> will be exploded by <tag and
   	   	   //returned as >Hello</tag>. To complete the tag
   	   	   //we put the separator back on place. <tag + >Hello</tag>
   	   	   //this will return the complete tag = <tag>Hello</tag>
   	   	   array_push($this->Tags, $this->Separator . $InterestedTags[$i]);
   	   }
   }

   /**
    * Handles attribute(s) of a tag
    * 
    * The function gets the value of the attribute(s) of a tag using the 
    * $Parser. It is used by xml_set_element_handler() function in the
    * constructor. 
    * 
    * @access private
    * @param mixed $Parser
    * @param string $Tag
    * @param array $Attributes
    * @uses $Parser
    * @uses $Attributes
    */
   function OpenTag($Parser, $Tag, $Attributes)
   {
   	   //Check whether $Attributes is an array and is not an empty array 
   	   if (is_array($Attributes) && count($Attributes) > 0)
   	   {		
   	   	    //Put $his->Attributes equal to $Attributes while changing the 
   	   	    //case of its key(s) to lowercase. The case of the key(s) is 
   	   	    //importan due to avoid error later on.  
   	   		$this->Attributes = array_change_key_case($Attributes, CASE_LOWER);
   	   }
   	   else
   	   { 
   	   		//$this->Attributes will be an empty array() which is ignored
   	   		//when adding it to the general array which is return by the 
   	   		//class!	   	  
   	   }
   }
   
   /**
    * Gets data of the tag
    * 
    * The function gets the data of an interesting tag by using the
    * $Parser. It is used by xml_set_character_data_handler() function
    * in the constructer.
    *
    * @access private
    * @param mixed $Parser
    * @param string $CharData
    * @uses $Parser
    * @uses $TagData
    */
   function GetCharData($Parser, $CharData)
   {
   	    //Set $this->TagData equal to $CharData
   	    //for later use.
   	   	$this->TagData = $CharData;
   }
	
   /**
    * Handles end/closing tag
    * 
    * The function gets the end/closing tag using the $Parser.
    * It is used by xml_set_element_handler() function  in the
    * constructer.
    * 
    * @access private
    * @param mixed $Parser
    * @param string $Tag
    * @uses $Parser
    */
   function CloseTag($Parser, $Tag)
   {
       //Have nothing do to! :(
       //But must be present.
   }

   /**
    * Creates an array containing all parsed XML data
    * 
    * The function runs each and every tag in the $Tags array
    * through the XMLParser object. The parsed data is then
    * stored in the $Graph which is returned at the end of the
    * proces.
    *
    * @access public
    * @return $Graph
    * @uses $Tags
    * @uses XMLParser
    */
   function CreateInputArray()
   {
   	  //Create an array to store the parsed XML data in it
   	  //and then return it at the end of the proces.
	  $Graph = array();
	     	  
	  //Get each interested tag from $Tags 
   	  foreach( $this->Tags as $Tag )
   	  {
   	  	//Create instance of XMLParser and parse the
   	  	//single tag to it
   	  	$XMLParser = new XMLParser($Tag);
   		//Store the data parsed by the XMLParser in the $Graph
   	  	array_push($Graph, $XMLParser->Tag);
   	  }
	  
   	  //Return the $Graph to user
   	  return $Graph;
   }   
}
?>
