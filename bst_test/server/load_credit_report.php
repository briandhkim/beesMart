<?php
$reportXML = simplexml_load_file('xml_data/save_response.xml') or die('Error loading xml');
$reportResponse = $reportXML->xpath('//DOCUMENT')[0];
print($reportResponse);
?>