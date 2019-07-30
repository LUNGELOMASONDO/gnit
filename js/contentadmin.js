$(document).on('focusin', 'input', function() {
	$(this).select();
});
	
	function input_valid()
	{	
		var module = $('#myModule').val();
		var yr = parseInt($('#year').val());
		var category = $('#mySub').val();
		var attext_dist = $('#uploadtext_opp').val();
		var probsol_dist = $('#probsol').val();
		var meta = $('#meta').val();

		var al_title = "Gnit Upload:";
		
		if(module == "")
		{
			swal({
  				title: al_title,
  				text: "Select a valid module code",
  				icon: "warning",
 		 		button: "Go back",
			});
			return false;
		}else{// 1
			if((probsol_dist != "Problem") && (probsol_dist != "Solution"))
			{
				swal({
  					title: al_title,
  					text: "State the upload type to be either a problem or solution",
  					icon: "warning",
 					button: "Go back",
				});
				return false;
			}else{//2
				if(Number.isInteger(yr) == false)
				{
					swal({
  						title: al_title,
  						text: "Enter a valid year the " + probsol_dist.toLowerCase() + " is from.",
  						icon: "warning",
 		 				button: "Go back",
					});
					return false;
				}else{//3
					if($.isEmptyObject(category))
					{
						swal({
  							title: al_title,
  							text: "Enter a descriptive category for the upload \n\n Examples:\nExamination Opp 1 2018 \nExamination Opp 1 2017 Question 2 \n Chapter 4: Derivatives Exercise 2.3 Solution etc..",
  							icon: "warning",
 		 					button: "Go back",
						});
						return false;
					}else{//4
							if((attext_dist != "Attach file") && (attext_dist != "Text"))
							{
								swal({
  									title: al_title,
  									text: "Is the upload an attachement or is it text?",
  									icon: "warning",
 	 								button: "Go back",
								});
								return false;
						}else{//6
							if(($.isEmptyObject(meta)) || (meta.length < 8))
							{
								swal({
  									title: al_title,
  									text: "Please provide more search keywords. This goes a long way in helping us correctly categorise content",
  									icon: "warning",
 									button: "Go back",
								}); 
								return false;
							}else{//7
								if(yr < 1994)
								{
									swal({
  										title: al_title,
  										text: "Enter a valid year the " + probsol_dist.toLowerCase() + " is from.",
  										icon: "warning",
 		 								button: "Go back",
									});
									return false;
								}
							}//7
						}//6
					}//4
				}//3
			}//2
		}//1
		return true;
	}

$(document).on('change', '#acceptinput', function(){
	var form = $('#input-check-form')[0];
	var formdata = new FormData(form);
		
	$.ajax({
    	type: "POST",
        enctype: 'multipart/form-data',
        url: "objects/accept_user_input.php",
        data: formdata,
        processData: false,
        contentType: false,
        cache: false,
        //timeout: 600000,
        success: function(data) {
        	swal({
  				text: data,
  				icon: "success",
 				button: "OK",
			}); 
        },
        error: function(e) {
        	swal({
  				text: e,
  				icon: "warning",
 				button: "OK",
			}); 
		}
	});		
});

$(document).on('click', '#addmodule-btn', function(){
	window.location.href = "add-module.php";
});

$(document).on('change', '#uploadtext_opp', function(){	
	if($('#uploadtext_opp').val() == 'Attach file')
	{
		$('#file_text_sect').empty();
		var att = "<div class='form-group'>" +
						"<label for='btnfile'>Choose file to upload (max 5MB).</label>" +
   						"<div class='btn btn-primary btn-sm float-left' id='btnfile' name='btnfile'>" +
      						"<span>Choose file:</span>" +
      						"<input type='file' id='fileup' name='fileup'>" +
    					"</div>" +
  					"</div>";
 		$('#file_text_sect').html(att);
	}
	else if($('#uploadtext_opp').val() == "Text")
	{
		$('#file_text_sect').empty();
		var txt = 	"<div class='form-group'>" +
						"<div class='summernote'>" +
							 "<textarea id='my-summernote' name='editordata'></textarea>" +
		 				"</div>" +
		 			"</div>";
		$('#file_text_sect').html(txt);
		$('#my-summernote').summernote({
			minHeight: 200,
	  		placeholder: 'Write your problem/solution here...',
	  		focus: false,
  			airMode: false,
  			fontNames: ['Roboto', 'Calibri', 'Times New Roman', 'Arial'],
  			fontNamesIgnoreCheck: ['Roboto', 'Calibri'],
  			dialogsInBody: true,
  			dialogsFade: true,
  			disableDragAndDrop: false,
  			toolbar: [
    		// [groupName, [list of button]]
				['style', ['bold', 'italic', 'underline', 'clear']],
				['para', ['style', 'ul', 'ol', 'paragraph']],
			    ['fontsize', ['fontsize']],
		    	['font', ['strikethrough', 'superscript', 'subscript']],
	    		['height', ['height']],
		    	['misc', ['undo', 'redo', 'print', 'help', 'fullscreen']]
  			],
			popover: {
			    air: [
      				['color', ['color']],
			   		['font', ['bold', 'underline', 'clear']]
    			]
  			},
  			print: {
    			//'stylesheetUrl': 'url_of_stylesheet_for_printing'
  			}
		});
	}
});

var probsol_dist = "";
	
$(document).on('change', '#probsol', function(){	
	probsol_dist = $('#probsol').val();
});

$(document).on('click', '#btn-upload', function(){		
	if(input_valid())
	{
		var form = $('#uploadform')[0];
		
		var formdata = new FormData(form);

		$.ajax({
            type: "POST",
           	enctype: 'multipart/form-data',
            url: "objects/handle_upload.php",
           	data: formdata,
           	processData: false,
           	contentType: false,
            cache: false,
           	//timeout: 600000,
            success: function(data) {
          		var al_title = "Gnit Upload:";
               	if((data.substring(0,6) == "Alert:") || (data.includes("Warning")) || (data.includes("Notice")))
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
					swal("Gnit Upload:", data, "success", {
  						buttons: {
   							logout: {
   								text: "Logout",
   								value:"logout",
   								},
    						goback: {
      							text: "Make another upload",
      							value: "back",
    						},
  						},
					})
					.then((value) => {
  						switch (value) {
    						case "logout":
      							window.location.replace("objects/logout.php");
     		 					break;
    						case "back":
      							location.reload();
      							break;
    						default:
     							location.reload();
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
	}
});

$(document).on('click', '#btn-cancel', function(){	
	window.location.href = "objects/logout.php";
});

$(document).on('change', '#fileup', function(){	
 
	var form = $('#uploadform')[0];
	var formdata = new FormData(form);

	$.ajax({
    	type: "POST",
        enctype: 'multipart/form-data',
        url: "objects/check_val_file.php",
        data: formdata,
        processData: false,
        contentType: false,
        cache: false,
        success: function(data) {
           		
        	var al_title = "Attached File";
       	 	if((data.substring(0,6) == "Alert:") || (data.includes("Warning")) || (data.includes("Notice")) || (data.includes("Fatal error")))
        	{
        		swal({
  					title: al_title,
					text: data,
  					icon: "warning",
 					button: "Go back",
				}); 
					
				$('#file_text_sect').empty();
				var att = "<div class='form-group'>" +
		    				"<label for='btnfile'>Choose file to upload (max 5MB).</label>" +
   							"<div class='btn btn-primary btn-sm float-left' id='btnfile' name='btnfile'>" +
      							"<span>Choose file:</span>" +
      							"<input type='file' id='fileup' name='fileup'>" +
    						"</div>" +
  						"</div>";
				$('#file_text_sect').html(att);
        	}
		},
    	error: function(e) {
    		swal({
  				title: "Warning",
  				text: e,
  				icon: "warning",
 				button: "Go back",
			}); 
    	}
	});
});

	var ping = 0;
	
$(document).keyup(function(){	
 	if(input_valid_key() && ping == 0)
  	{
  		if(input_valid_key())
  		{
  			ping = 1;
  			swal("Please remember to check for duplicates");
  		}
  	}
});
	
$(document).on('click', '#btn-duplicate', function(){	
	if(input_valid())
	{
		var search_string =  $('#myModule').val() + " " +parseInt($('#year').val()) + " " + $('#mySub').val() + " " + $('#probsol').val() + " " + $('#meta').val();
		openWindowWithPost("duplicate-check.php", {
   			search: search_string
		});
	}
});
	
function openWindowWithPost(url, data) {
	var form = document.createElement("form");
    form.target = "_blank";
	form.method = "POST";
    form.action = url;
	form.style.display = "none";

	for (var key in data) {
   		var input = document.createElement("input");
       	input.type = "hidden";
	    input.name = key;
   	    input.value = data[key];
       	form.appendChild(input);
    }

   	document.body.appendChild(form);
    form.submit();
   	document.body.removeChild(form);
}


	function input_valid_key()
	{	
		var module = $('#myModule').val();
		var yr = parseInt($('#year').val());
		var category = $('#mySub').val();
		var attext_dist = $('#uploadtext_opp').val();
		var probsol_dist = $('#probsol').val();
		var meta = $('#meta').val();
	
		if(module == "")
		{
			return false;
		}else{// 1
			if((probsol_dist != "Problem") && (probsol_dist != "Solution"))
			{
				return false;
			}else{//2
				if(Number.isInteger(yr) == false)
				{
					return false;
				}else{//3
					if($.isEmptyObject(category))
					{
						return false;
					}else{//4
							if((attext_dist != "Attach file") && (attext_dist != "Text"))
							{
								return false;
						}else{//6
							if(($.isEmptyObject(meta)) || (meta.length < 8))
							{
								return false;
							}else{//7
								if(yr < 1994)
								{
									return false;
								}
							}//7
						}//6
					}//4
				}//3
			}//2
		}//1
		return true;
	}