$(document).ready(()=>{


	$.ajax({
		type: 'GET',
		url: 'create_xml.php',
		dataType: 'xml',
		success: (resp)=>{
			console.log(resp);
			const h2Test = $('<div>').append($(resp).find('h2.text-success')).html();
			// $('#h2Test').html(h2Test.html());
			$('#h2Test').append(h2Test);
		},
		error: (err)=>{
			console.log('error: ', err);
		}
	});

});