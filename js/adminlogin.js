$(document).on('focusin', 'input', function() {
	$(this).select();
});

$(document).on('click', '#btnlogin', function(){
	$.post('objects/admin_access.php', {
		email: $('#txtemail').val(),
		password: $('#txtpassword').val()
	}, 
	function(data){
		if(data.substring(0, 3) == "ERR")
		{
			swal(data);
		}
		else if(data.substring(0, 6) == "Alert:")
		{
			$('#errormessage').text(data);
		}
		else
		{
			window.location.replace("content-administrator-acc.php");
		}
	});
});

$(document).on('focusin', '#txtpassword', function() {
	$('#errormessage').text("");
});
	
$(document).on('focusin', '#txtemail', function() {
	$('#errormessage').text("");
});

$(document).on('click', '#btnforgot', function(){
	var varemail = $('#txtemail').val();
	if(varemail != "" && varemail != " ")
	{
		$.post('objects/forgot_password.php', {
			email: varemail
		},
		function(data){
			if(data.substring(0, 3) == "ERR")
			{
				swal({
					title: "Gnit: Password recovery",
 					text: data,
  					icon: "warning",
 	 				button: "OK",
 	 			});
			}
			else{
				if(data.substring(0, 7) == "Message")
				{
					swal({
						title: "Gnit: Password recovery",
 						text: data,
  						icon: "success",
 	 					button: "OK",
 	 				});
				}
			}
		});
	}
	else
	{
		swal({
  			title: "Gnit: Password recovery",
  			text: "Please provide your email address?",
  			icon: "warning",
 	 		button: "OK",
 		});
	}
});