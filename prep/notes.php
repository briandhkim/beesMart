<?php 
/*******

1. Using SimpleXML to create a request XML 
	https://stackoverflow.com/questions/143122/using-simplexml-to-create-an-xml-object-from-scratch?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa

2. Using SimpleXML's xpath function to parse a response XML 
	https://www.w3schools.com/php/php_ref_simplexml.asp
	https://www.w3schools.com/xml/xpath_syntax.asp

3. Using cURL to submit request XML as POST to url endpoint. 
	http://php.net/manual/en/function.curl-setopt.php
	
4. Using Bootstrap to print contents of response XML to table 
	https://www.bootstrapdash.com/use-bootstrap-with-php/
	https://stackoverflow.com/questions/18697422/send-xml-data-to-webservice-using-php-curl?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa
	https://stackoverflow.com/questions/15679090/send-an-xml-post-request-to-a-web-server-with-curl/15679384#15679384

5. Using Ajax to dynamically load specific HTML embedded inside 
   response XML to webpage. 

6. Use any php function to save response XML to hard drive.

NOTES
PHP by defualt is:
	synchronous:
		all operations in PHP happen in order by default
		there is no DOM to act as a state machine, so once
		a block of text is written out to a web page, it 
		cannot normally be changed
	linear
		everything written at the top happens before what comes after
		on the same page
	translated
		like JS, changing a text file with PHP in it will change how
		it runs the next time the page is processed
	procedural
		things that are defined first happen first. Must define a 
		function/variable before it can be used
	server-side
		runs exclusively on the server before a web-page is sent to a browser.
		therefore, it has access to underlying technology on the server, such 
		as files. 
	stateless
		each request to a page is separate and unique call of that page, and will
		not remember what the previous call did


ADDINg and CONCATENATION:
	JS uses + symbol for adding numbers and concatenating strings
	PHP uses . symbol for concatenation and + for add

OBJECT access:
	js accesses members of an object with . symbol (e.g. x.name or x['name'])
	PHP accesses members of an object with -> symbol (x->)
********/



// ####1. Using SimpleXML to create a request XML
	//when loading local file:
	// $local_xml = simplexml_load_file('sample_file.xml');
$simpleXML_request = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?>');
	//to add on to this object
	$request_data1 = $simpleXML_request->addChild('REQUEST'); //REQUEST gets added to xml doc like an html element
	$request_data1->addAttribute('_Identifier', 'sampleIdentifier'); //this gets added on like html attribute(e.g. class, value, etc)
	$request_dataTwo = $simpleXML_request->addChild('REQ_DATA');
	$request_dataTwo->addAttribute('_login_id', 'admin');
	$request_dataTwo->addAttribute('__login_pw', 'adminPassword');
	//adding an element as child elmt of another
			//REQ_DATA_GROUP would be child of REQ_DATA and REQ_DATA_CHILD would be child of REQ_DATA_GROUP
	$data_child = $request_dataTwo->addChild('REQ_DATA_GROUP')->addChild('REQ_DATA_CHILD');
	$data_child->addAttribute('FirstName', 'John');
	$data_child->addAttribute('LastName', 'Smith');

	/**
		if using a function and passing in the object as argument (e.g. $data_child) function parameter needs to be 
		defined with &before the variable  e.g.
		function createGroup($prim_variable, &$ref_variable){
			$return_child = $ref_variable->addChild($prim_variable);
			$return_child->addAttribute('asdfas', 'asdfasdf');
			return $return_child;
		};
		outside of this function, this can be used to addAttribute or addChild like other variable e.g.
		$func_child = createGroup($a, $b);
		$func_chlld->addAttribute('...','...');

	**/
	//after building
//####3. Using cURL to submit request XML as POST to url endpoint
	//create curl object
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'url goes here');

	//set TRUE to return the transfer as a string of the return value of curl_exec() instead of
	//outputting it directly
	//curl_exec() returns TRUE on success or FALSe on failure. however, if _RETURNTRANSFER option is set,
	//it will return the result on success, false on failure 	probably should be set to true
curl_Setopt($ch, CURLOPT_RETURNTRANSFER, true);
	/**
	other opts to consider:
		CURLOPT_HTTPHEADER : array('Content-Type: text/xml');
	**/
//to set to post request
curl_setopt($ch, CURLOPT_POST, true);
//add post data (simpleXML request created above)
curl_setopt($ch, CURLOPT_POSTFIELDS, $simpleXML_request->asXML());

//execute curl request
$resp = curl_exec($ch);
curl_close($ch);

//####6. Use any php function to save response XML to hard drive.
file_put_contents('response_file.xml', $resp);

//####2. Using SimpleXML's xpath function to parse a response XML 
	//grabbing xml tag TABLe_DATA content using xpath
	//  selector syntax:
	//	'TABLE_DATA'  selects all nodes with name table_data
	//	'/TABLE_DATA' selects the root element TABLE_DATA
	// 	'//TABLE_DATA'  selects all 'TABLE_DATA' no matter where they are in the doc

//####4. Using Bootstrap to print contents of response XML to table 
$returnXML = new SimpleXMLElement('<?xml version="1.0" encoding"utf-8"?> <xml></xml> ')

//## 2. continued
$table_data = $simpleXML_response->xpath('//TABLE_DATA');
//	$table_data holds all nodes with name TABLE_DATA

//## 4. continued: create table element to hold all table data
$data_table = $returnXML->addChild('table')->addAttribute('class','table'); //creates node/element with table tag
$header = $data_table->addChild('thead');
$tr = $header->addChild('tr');
$tr->addChild('th', 'Employee Name'); //the text inside the first header column would be Employee Name
$tr->addChild('th', 'Phone Number')->addAttribute('class','text-warning text-center'); //adding more columns

$tBody = $data_table->addChild('tbody');

//to traverse through the data array:
foreach($table_data as $tdata){
	//searching for content inside the tdata variable
	//e.g. getting the password attribute value of USER node inside $tdata  i.e. USER is child of TABLE_DATA
	// https://www.w3schools.com/xml/xpath_syntax.asp
	$password = $tdata->xpath('USER[1]')['password']; //first USER node found in the tdata
	$employee_name = $tdata->xpath('EMPLOYEE_NAME[1]')['_name']; //get _name attribute value of the first employee_name node
	//adding table row and content inside it
	$row = $tBody->addChild('tr');
	$row->addChild('td', $password)->addAttribute('class','text-danger');
	$row->addChild('td')->addChild('a', $employee_name)->addAttribute('class','btn btn-success');
}

//return final data as xml
print($returnXML->asXML());


?>