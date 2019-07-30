/**
 * @author Masondo
 */
$(document).on('click', '#btn-download', function (e) {
   	$.post('objects/increment_download.php', {
    	uploadid: $('#uploadid').text()
   	},
   	function(data){
   		
   	});
});

$(document).on('click', '#btn-submitflag', function(e){
	var form = $('#flagform')[0];
		
	var formdata = new FormData(form);
	formdata.append("uploadid", $('#uploadid').text());
			$.ajax({
            	type: "POST",
            	enctype: 'multipart/form-data',
            	url: "objects/increment_flag.php",
            	data: formdata,
           		processData: false,
            	contentType: false,
            	cache: false,
            	//timeout: 600000,
            	success: function(data) {
  					swal({
  						title: "FLAG SUCCESSFUL",
  						text: data,
 						icon: "success",
  						button: "OK",
					});
            	},
            	error: function(e) {
 
            	}
        	});
  
});

var maxLength = 500;
$(document).on('keyup', '#writecomment', function(e){
	var textlen = maxLength - $(this).val().length;
  	if(textlen >= 0)
  	{
  		$('#rchars').text(textlen);
  	}
  	else
  	{
  		$('#rchars').text("0");
  	}
});

$(document).on('click', '#submit-comment', function(e){
	$.post('objects/submitcomment.php', {
		uploadid: $('#uploadid').text(),
		comment: $('#writecomment').val()
	}, 
	function(data){
		if(data.substring(0, 6) == "Alert:")
		{
			swal({
  				title: "Gnit Comment",
 	 			text: data,
 				icon: "warning",
  				button: "OK",
			});
		}
		else
		{
			swal({
  				title: "Gnit Comment",
  				text: data,
 				icon: "success",
	  			button: "OK",
			});
			$('#writecomment').val("");
		}
	});
});
