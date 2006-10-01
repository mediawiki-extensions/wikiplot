<?php

class XMLParser {
	 
   var $Parser;
   var $Input;
   var $Tag;
   var $Attributes;
   var $Expression;
   var $Tags;
   var $Separator;

   function XMLParser($Data)
   {
   	   $this->Parser = xml_parser_create();
       
   	   $this->Input = $Data;
   	   
   	   $this->Tags = array();
   	   
   	   $this->Separator = "<graph";

       xml_set_object($this->Parser, $this);
       
       xml_set_element_handler($this->Parser, "OpenTag", "CloseTag");
       
       xml_set_character_data_handler($this->Parser, "GetCharData");
            
       $this->ExplodeInputData();
       
       $this->Parse($this->Input);
 	   
       xml_parser_free($this->Parser);
   }

   function Parse($Data)
   {
       xml_parse($this->Parser, $Data);
       
       $this->CreatTagArray();
   }
   
   function CreatTagArray()
   {
   	   if (!empty($this->Attributes) && !empty($this->Expression))
   	   {
   	      $this->Tag = array($this->Attributes, $this->Expression);	
   	   }
   	   else 
   	   {
   	   	  $this->Tag = array($this->Expression);
   	   }
   }

   function ExplodeInputData()
   {
   	   $InputTags = explode($this->Separator , $this->Input); 
   	   
   	   for ($i=1; $i < count($InputTags); $i++)
   	   {
   	   	   array_push($this->Tags, $this->Separator ."". $InputTags[$i]);
   	   }
   }
      
   function OpenTag($Parser, $Tag, $Attributes)
   {
   	   if (isset($Attributes) && is_array($Attributes))
   	   		$this->Attributes = $Attributes;
   	   else 
   	   		$this->Attributes = array();	   	   
   }

   function GetCharData($Parser, $CharData)
   {
   	   $this->Expression = $CharData;
   }

   function CloseTag($Parser, $Tag)
   {
       //Have nothing do to! :(
   }

   function CreatInputArray()
   {
	  $Graph = array();
	     	  
   	  foreach( $this->Tags as $Tag )
   	  {
   	  	$XMLParser = new XMLParser($Tag);
   		array_push($Graph, $XMLParser->Tag);
   	  }
	  
   	  return $Graph;
   }   
}


$st1 = "<graph color='234,234,233' label='string'>x^2+5</graph>
       <graph color='234,234,233' label='string'>x^2+5</graph>
       <graph>x^2+5</graph>";

$st2 = "<root>
       <graph color='234,234,233' label='string'>x^2+5</graph>
       <tag1 name='serie1'>This is serie one</tag1>
       <graph color='234,234,233' label='string'>x^2+5</graph>
       <tag2 name='serie without attr'>this is serie three</tag2>
       <graph>x^2+5</graph>
       </root>";

$xml = new XMLParser($st2);
print_r($xml->CreatInputArray());

?>