<?php

$return_XML = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><xml></xml>');


//parent element can't have addAtribute after addChild; i.e. needs to do it separately
$header_Test = $return_XML->addChild('h2', 'XML h2 Test');
$header_Test->addAttribute('class', 'text-success');
$header_Small = $header_Test->addChild('small', 'small test');

$content_table = $return_XML->addChild('table');
$content_table->addAttribute('class', 'table');

$header = $content_table->addChild('thead');
$tr = $header->addChild('tr');
$tr->addChild('th', 'Employee Name')->addAttribute('class', 'text-center');
$tr->addChild('th', 'Supervisor')->addAttribute('class', 'text-center text-warning');


file_put_contents('create_file.xml', $return_XML->saveXML());


//*******xml format for whitespace
// $xml_format = new DOMDocument('1.0');
// $xml_format->preserveWhiteSpace = false;
// $xml_format->formatOutput = true;
// $xml_format->loadXML($return_XML->asXML());
// $xml_format->save('formattedXML.xml');



print($return_XML->asXML());

?>