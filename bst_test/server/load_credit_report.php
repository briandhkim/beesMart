<?php
if(empty($local_access)){
	die('no direct access');
}

$reportXML = simplexml_load_file('xml_data/save_response.xml') or die('Error loading xml');
$reportResponse = $reportXML->xpath('//DOCUMENT')[0];

$return_data['success'] = true;
$return_data['data'] = $reportResponse->asXML();

?>