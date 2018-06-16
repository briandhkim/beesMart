<?php
$credit_request = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?> <REQUEST_GROUP mismovERSIONid="2.3.1"></REQUEST_GROUP>');

// 	REQUESTING/RECEIVING/SUBMITTING
//	common attributes: _name, _streetaddress, _city, _state, _postalcode

function createCommonParty($xmlAttr, $name, $stAddress, $city, $state, $postalCode){
	$party = $GLOBALS['credit_request']->addChild($xmlAttr);
	$party->addAttribute('_Name', $name);
	$party->addAttribute('_StreetAddress', $stAddress);
	$party->addAttribute('_City', $city);
	$party->addAttribute('_State', $state);
	$party->addAttribute('_PostalCode', $postalCode);

	return $party;
}

$req_party = createCommonParty('REQUESTING_PARTY', 'ACG Funding', '1661 Hanover Road Suite 216', 'City of Industry', 'CA', '91748');

//needs _identifier attr
$rec_party = createCommonParty('RECEIVING_PARTY', 'Credit Plus', '31550 Winterplace Parkway', 'Salisbury', 'MD', '21804');
$rec_party->addAttribute('_Identifier', 'AV');

//needs loginaccountidentifier, loginaccountpassword, identifier
$submit_party = createCommonParty('SUBMITTING_PARTY', 'BeSmartee', '16892 Bolsa Chica Street 201', 'Huntington Beach', 'CA', '92649');
$submit_party->addAttribute('LoginAccountIdentifier', 'besmartee');
$submit_party->addAttribute('LoginAccountPassword', '263nx848');
$submit_party->addAttribute('_Identifier', 'BeSmartee07272015');
//******** end of req/rec/sub party elements *********

//this gets reused later on
date_default_timezone_set('America/Los_Angeles');
$requestDate = date('Y-m-d\TH:i:s');

$requestNode = $credit_request->addChild('REQUEST');
$requestNode->addAttribute('RequestDatetime', $requestDate);
$requestNode->addAttribute('InternalAccountIdentifier', '');
$requestNode->addAttribute('LoginAccountIdentifier', 'TNGUYEN3');
$requestNode->addAttribute('LoginAccountPassword', 'CHECKm@te1');

$credit_req = $requestNode->addChild('REQUEST_DATA')->addChild('CREDIT_REQUEST');

//children of $credit_request
$mismoVer = $credit_req->addChild('MISMOVersionID', '2.3.1');
$lenderCaseId = $credit_req->addChild('LenderCaseIdentifier', 'LME8BW68');
$reqPartyReqedName = $credit_req->addChild('RequestingPartyRequestedByName', 'Benson Pang');

$credit_req_data = $credit_req->addChild('CREDIT_REQUEST_DATA');
$credit_req_data->addAttribute('CreditRequestID', 'CreditReequest1');
$credit_req_data->addAttribute('BorrowerID', 'Borrower');
$credit_req_data->addAttribute('CreditReportRequestActionType', 'Submit');
$credit_req_data->addAttribute('CreditReportType', 'Merge');
$credit_req_data->addAttribute('CreditRequestType', 'Individual');
$credit_req_data->addAttribute('CreditRequestDateTime', $requestDate);

$credit_rep_included = $credit_req_data->addChild('CREDIT_REPOSITORY_INCLUDED');
$credit_rep_included->addAttribute('_EquifaxIndicator', 'Y');
$credit_rep_included->addAttribute('_ExperianIndicator', 'Y');
$credit_rep_included->addAttribute('_TransUnionIndicator', 'Y');

$borrower = $credit_req->addChild('LOAN_APPLICATION')->addChild('BORROWER');
$borrower->addAttribute('BorrowerID', 'Borrower');
$borrower->addAttribute('_FirstName', 'Tim');
$borrower->addAttribute('_LastName', 'Testcase');
$borrower->addAttribute('_BirthDate', '1999-01-01');
$borrower->addAttribute('_HomeTelephoneNumber', '714-235-7114');
$borrower->addAttribute('_SSN', '123456789');
$borrower->addAttribute('_PrintPositiontype', 'Borrower');

$residence = $borrower->addChild('_RESIDENCE');
$residence->addAttribute('_StreetAddress', '4053 Aladdin Dr');
$residence->addAttribute('_City', 'Huntington Beach');
$residence->addAttribute('_state', 'CA');
$residence->addAttribute('_PostalCode', '92649');
$residence->addAttribute('BorrowerResidencyType', 'Current');
//end of $credit_request children
//end of #1 create a XML request using SimpleXML

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://credit.meridianlink.com/inetapi/AU/get_credit_report.aspx" );
curl_setopt($ch, CURLOPT_POST, true);
//set to true to receive data after post request
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml') );
curl_setopt($ch, CURLOPT_POSTFIELDS, $credit_request->asXML() );

$resp = curl_exec($ch);
curl_close($ch);

//this was to compensate for receiving null from curl request
if(!$resp){
	$resp = simplexml_load_file('xml_data/response.xml') or die('Error loading xml');
}
// print_r($resp);
//end of #2 submit request to APi using curl

$xml_format = new DOMDocument('1.0');
$xml_format->preserveWhiteSpace = false;
$xml_format->formatOutput = true;
$xml_format->loadXML($resp->asXML());
$xml_format->save('xml_data/save_response.xml');
//end of #3; used this instead of file_put_contents for formatting


//from credit liabilities; print following: name of creditor, date, outstanding balance, monthly payment, account type
$liability_response = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><xml></xml>');
$lib_table = $liability_response->addChild('table');
$lib_table->addAttribute('class', 'table table-striped table-hover table-condensed table-bordered');

//table header
$header = $lib_table->addChild('thead');
$header->addAttribute('class', 'font-weight-bold');
$headerRow = $header->addChild('tr');
$headerRow->addAttribute('class', 'warning');
$headerRow->addChild('th', 'Name of Creditor')->addAttribute('class', 'text-center');
$headerRow->addChild('th', 'Date')->addAttribute('class', 'text-center');
$headerRow->addChild('th', 'Outstanding Balance')->addAttribute('class', 'text-center text-danger');
$headerRow->addChild('th', 'Monthly Payment')->addAttribute('class', 'text-center');
$headerRow->addChild('th', 'Account Type')->addAttribute('class', 'text-center');

//talbe body
$body = $lib_table->addChild('tbody');
$body->addAttribute('class', 'text-center');

	// 	'//TABLE_DATA'  selects all 'TABLE_DATA' no matter where they are in the doc
$cred_liabilties = $resp->xpath('//CREDIT_LIABILITY'); 
// print following: name of creditor, date, outstanding balance, monthly payment, account type
foreach($cred_liabilties as $lib){
	//grabbing relevant info
	$creditorName = $lib->xpath('_CREDITOR')[0]['_Name'];
	$acctDate = $lib['_LastActivityDate'];
	$balance = $lib['_UnpaidBalanceAmount'];
	$monthlyPayment = $lib['_MonthlyPaymentAmount'];
	$accountType = $lib['_AccountType'];
	
	//building table element
	$bodyRow = $body->addChild('tr');
	$creditLink = $bodyRow->addChild('td')->addChild('a', $creditorName);
	$creditLink->addAttribute('class', 'btn btn-primary openReport');
	$bodyRow->addChild('td', $acctDate);
	$bodyRow->addChild('td', "$" .  $balance);
	$bodyRow->addChild('td', "$" . $monthlyPayment);
	$bodyRow->addChild('td', $accountType);
}

print($liability_response->asXML());

?>