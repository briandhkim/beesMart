<?php
//just a flag to check the files going through access.php
$local_access = true;

$return_data =[
	'success' => false,
	'errors' => []
];

function return_and_exit($return_data){
	$output = json_encode($return_data);
	print($output);
	exit();
}

if($_SERVER['REQUEST_METHOD'] === 'GET'){
	if( empty($_GET['action']) ){
		$return_data['errors'][] = 'no specific action for GET';
		return_and_exit($return_data);
	}
	switch($_GET['action']){
		case 'load_liability_data':
			include('request_curl.php');
			break;
		case 'load_credit_report':
			include('load_credit_report.php');
			break;
		default:
			$return_data['errors'][] = 'invalid action';
	}
}else{
	$return_data['errors'][] = 'invalid method';
	return_and_exit($return_data);
}

return_and_exit($return_data);
?>