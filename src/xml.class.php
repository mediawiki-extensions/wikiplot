<?php
/**
* @package WikiPlot
*/
class XMLparser {

   var $attr;
   var $data;
   var $graph;
   var $input;
   
   function openElement($parser, $element, $attributes) {
       $data['name'] = $element;
       if ($attributes) { $data['attr'] = $attributes; }
       $this->data[] = $data;
   }

   function closeElement($parser, $element) {
     if (count($this->data) > 1) {
           $data = array_pop($this->data);
           $index = count($this->data) - 1;
           $this->data[$index][$element][] = $data;
       }
   }

   function characterData($parser, $data) 
   {
       if ($data = trim($data)) {
           $index = count($this->data) - 1;
           $this->data[$index]['math'] .= $data;
       }
   }
   

   function XMLParser($xml_file)
   {
       $this->input = '<root>';
	   $this->input .= $xml_file;
	   $this->input .= '<root>';
       $this->xml = xml_parser_create();
       xml_set_object($this->xml, $this);
       xml_set_element_handler($this->xml, 'openElement', 'closeElement');
       xml_set_character_data_handler($this->xml, 'characterData');
       $this->parse($xml_file);
   }
   
   function parse($xml_file) 
   {
   	   xml_parser_set_option($this->xml, XML_OPTION_CASE_FOLDING, false);
       xml_parse($this->xml, $xml_file);
   }
   
   function prep_graph() {
		$temp = array();
		$temp_i = count($this->data[0]['graph']);
		
		for ($i=0; $i<$temp_i; $i++) {
			$this->graph[] = $this->data[0]['graph'][$i];
		}
   }
}
?>
