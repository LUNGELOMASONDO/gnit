$(document).on('click', '#btn-backtoadmin', function(){	
	window.location.replace("content-administrator-acc.php");
});
	
$(document).on('click', '#btn-addmodule', function(){
		
	var form = $('#addmodule-form')[0];
	var formdata = new FormData(form);

	$.ajax({
    	type: "POST",
        enctype: 'multipart/form-data',
        url: "objects/add_module.php",
        data: formdata,
        processData: false,
        contentType: false,
        cache: false,
        //timeout: 600000,
        success: function(data) {
           		
        	var al_title = "Module Addition:";
        		
            if((data.substring(0,6) == "Alert:") || (data.includes("Warning")) || (data.includes("Notice")) || (data.includes("Fatal error")))
            {
                swal({
  					title: al_title,
					text: data,
  					icon: "warning",
 					button: "Go back",
				}); 
             }
             else
             {
				swal("Module Addition:", data, "success", {
  					buttons: {
   						logout: {
   							text: "Go to upload page",
   							value:"logout",
   						},
    					goback: {
      						text: "Add another module",
      						value: "back",
    					},
  					},
				})
				.then((value) => {
  					switch (value) {
    					case "logout":
      						window.location.replace("content-administrator-acc.php");
     		 				break;
    					case "back":
      						location.reload();
      						break;
    					default:
      						window.location.replace("content-administrator-acc.php");
					}
				});
			}
		},
        error: function(e) {
        	swal({
  				title: al_title,
  				text: e,
  				icon: "warning",
 				button: "Go back",
			}); 
        }
	});		
});