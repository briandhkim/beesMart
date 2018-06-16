$(document).ready(()=>{
	//this is the initial ajax function I had at the end of on-site interview
	// $.ajax({
	// 	type: 'GET',
	// 	url: 'server/request_curl.php',
	// 	dataType: 'xml',
	// 	success: (resp)=>{
	// 		console.log(resp);
	// 		const responseTable = $('<div>').append($(resp).find('table')).html();
	// 		$('#liabilityTable').append(responseTable);

	// 		//if buttons are present, allow the button to perform function after clicked
	// 		if($('.openReport')){
	// 			$('.openReport').on('click', ()=>{
	// 				$.ajax({
	// 					type: 'GET',
	// 					url: 'server/load_credit_report.php',
	// 					dataType: 'html',
	// 					success: (data)=>{
	// 						console.log(data);
	// 						let parse = new DOMParser();
	// 						let reportResp = parse.parseFromString(data, 'text/html');
	// 						$(reportResp).find('body').appendTo('#creditReport');

	// 						//clear button handler assigned only when credit report is opened
	// 						if($('#creditReport').children()){
	// 							$('.clearReportBtn').on('click',()=>{
	// 								$('#creditReport').children().remove();
	// 								//clear button function after closing
	// 								$('.clearReprotBtn').off('click');
	// 							});
	// 						}
	// 					},
	// 					error: (err)=>{
	// 						console.log(err);
	// 					}
	// 				})
	// 			});
	// 		}
	// 	},
	// 	error: (err)=>{
	// 		console.log('error: ', err);
	// 	}
	// });


	loadTable().then(loadTableSuccess, loadTableError);
});

//made the change below after on-site test to add ajax promise

//initially called at document ready
function loadTable(){
	const promise = {
		then: function(resolve, reject){
			this.resolve = resolve;
			this.reject = reject;
		}
	};
	$.ajax({
		type: 'GET',
		url: 'server/request_curl.php',
		dataType: 'xml',
		success: function(resp){
			promise.resolve(resp);
		},
		error: function(err){
			promise.reject(err);
		}
	});
	return promise;
}
function loadTableSuccess(response){
	const responseTable = $('<div>').append($(response).find('table')).html();
	$('#liabilityTable').append(responseTable);
	if($('.openReport')){
		$('.openReport').on('click',()=>{
			getCreditReport().then(getCreditReportSuccess, getCreditReportError);

		});
	}
}
function loadTableError(err){
	console.log('error loading credit liabilities table: ', err);
}

//for rendering credit report
function getCreditReport(){
	const promise = {
		then: function(resolve, reject){
			this.resolve = resolve,
			this.reject = reject
		}
	};
	$.ajax({
		type: 'GET',
		url: 'server/load_credit_report.php',
		dataType: 'html',
		success: function(data){
			promise.resolve(data);
		},
		error: function(err){
			promise.reject(err);
		}
	});
	return promise;
}
function getCreditReportSuccess(data){
	let parse = new DOMParser();
	let reportResp = parse.parseFromString(data, 'text/html');
	$(reportResp).find('body').appendTo('#creditReport');

	//clear button handler assigned only when credit report is opened
	if( $('#creditReport').children() ){
		$('.clearReportBtn').on('click',()=>{
			$('#creditReport').children().remove();
			//clear button function after closing
			$('.clearReprotBtn').off('click');
		});
	}							
}
function getCreditReportError(err){
	console.log('error getting credit report:', err);
}