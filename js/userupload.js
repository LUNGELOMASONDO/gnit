$(document).on('focusin', 'input', function() {
	$(this).select();
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


$(document).on('click', '#btn-share', function(){		
		var form = $('#userinput')[0];
		
		var formdata = new FormData(form);

		$.ajax({
            type: "POST",
           	enctype: 'multipart/form-data',
            url: "share.php",
           	data: formdata,
           	processData: false,
           	contentType: false,
            cache: false,
           	//timeout: 600000,
            success: function(data) {
				if(data.substring(0, 9) == "Thank you")
				{
					swal("gnit", data, "success", {
  						buttons: {
	   						backtohome: {
   								text: "Go back to homepage",
   								value:"home",
   							},
    						share: {
      							text: "Share another resource",
      							value: "back",
    						},
  						},
					})
					.then((value) => {
  						switch (value) {
	    					case "home":
    	  						window.location.replace("index.php");
	     		 				break;
    						case "back":
      							location.reload();
      							break;
    						default:
	      						window.location.replace("index.php");
							}
					});
				}
				else
				{
					swal({
  						title: "gnit",
  						text: data,
  						icon: "warning",
	 					button: "OK",
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


$(document).on('click', '#btn-save', function(){
	
	var elements = document.querySelectorAll("#categorycheck input[type=checkbox]");
	$('#invisiblecategories').empty();
	var category_str = "";
	for (var i = 0, element; element = elements[i++];) 
	{
		if($(element).prop('checked'))
		{
			category_str += element.value;
			category_str += "^";
		}
	}	
	var category_text = '<input type="text" id="categorylist" name="categorylist" value="' + category_str + '" />';
	$('#invisiblecategories').html(category_text);
});

$(document).on('change', '#fileup', function(){	
 
	var form = $('#userinput')[0];
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
  				title: al_title,
  				text: e,
  				icon: "warning",
 				button: "Go back",
			}); 
    	}
	});
});