<?php
	/**
	 * TO DO:
	 * 	- Write the class in the same way like whole wikiplot
	 * 	- The class must be useable both in PHP 4 and 5
	 *	- Function param must be vars
	 */
	
class xml  {
	 
   var $parser;
   var $serie;
   var $attributes;
   var $exp;
   var $tags;

   function xml($data)
   {
   	   $this->tags = array();
   	
       $this->parser = xml_parser_create();

       xml_set_object($this->parser, $this);
       xml_set_element_handler($this->parser, "tag_open", "tag_close");
       xml_set_character_data_handler($this->parser, "cdata");
            
       $this->explode_data($data);
       $this->parse($data);
 	   xml_parser_free($this->parser);
   }

   function parse($data)
   {
       xml_parse($this->parser, $data);
       $this->creat_serie();
   }
   
   function creat_serie()
   {
   	   if (!empty($this->attributes) && !empty($this->exp)) {
   	      $this->serie = array($this->attributes, $this->exp);	
   	   }
   	   else {
   	   	  $this->serie = array($this->exp);
   	   }
   }

   function explode_data($data, $separator = "<graph")
   {
   	   $splited_data = explode($separator , $data); 
   	   
   	   foreach ($splited_data as $v)
   	   {
   	   	  if( !empty($v) )
   	   	   array_push($this->tags, $separator ."". $v);
   	   }
   }
   function tag_open($parser, $tag, $attributes)
   {
   	   //var_dump($parser, $tag, $attributes);
   	   if (isset($attributes) && is_array($attributes))
   	   		$this->attributes = $attributes;
   	   else 
   	   		$this->attributes = array();	   	   
   }

   function cdata($parser, $cdata)
   {
   	   //var_dump($parser, $cdata);
   	   $this->exp = $cdata;
   }

   function tag_close()
   {
       //var_dump($parser, $tag);
   }
   
   function creat_graph_array()
   {
	  $graph = array();
	     	  
   	  foreach( $this->tags as $tag )
   	  {
   	  	$xml_p = new xml($tag);
   		array_push($graph, $xml_p->serie);
   	  }
	  
   	  return $graph;
   }
} // end of class xml



$st = "<graph color='234,234,233' label='string'>x^2+5</graph>
       <graph color='234,234,233' label='string'>x^2+5</graph>
       <graph>x^2+5</graph>";

$xml = new xml($st);
print_r($xml->creat_graph_array());

?> 