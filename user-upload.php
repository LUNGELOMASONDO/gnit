<?php
	require 'objects/log_error.php';
	require 'objects/db_connect.php';
	require 'objects/session_life.php';
	require 'objects/admin.php';
	require 'objects/content_admin.php';
	require 'objects/varsity.php';
	require 'objects/module.php';
	require 'objects/get_modules.php';
	require 'objects/modulearr_to_str.php';
	require 'objects/get_accept_user_input.php';
	require 'objects/admin_category_arr.php';
	
	session_start();
	session_life();
	
	if(isset($_SESSION['content-administrator']))
	{
		header('Location:content-administrator-acc.php');
		exit();
	}
	
	$module = arr_module(1); // [1] as a parameter assumes NWU
	$str_module = mod_arr_to_str($module, 1);
	$admin_category_arr = admin_category_arr();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title>Gnit: Share Resource</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-select.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-select.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.min.js"></script>
		<script src="js/summernotestyle.js"></script>
		<link rel="stylesheet" href="css/summernotesyle.css">
		<link rel="stylesheet" href="css/styleform.css" />
		<link rel="stylesheet" href="css/auto_mod.css" />
		<link rel="stylesheet" href="css/auto_sub.css" />
		<script src="js/auto_mod.js"></script>
		<script src="js/auto_sub.js"></script>
		<script src="js/sweetalert.min.js"></script>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<script>
			<?php
				$file = fopen("js/userupload.js", "r");
				while(!feof($file))
				{
					echo fgets($file) . "\n";
				}
				fclose($file);
			?>
		</script>
	</head>
	<body>
<!-- nav -->
		<nav class="navbar navbar-expand-md navbar-white bg-white sticky-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"> <img src="img/gnit-darkblue.jpg" alt="gnit" style="width:120px;" /> </a>
			</div>
		</nav>
<!-- content -->
		<div class="container" style="padding-bottom:8%;">
			<div class="row">
				<div class="col-lg-3">
					<button id='viewfile' class='btn btn-outline-dark' data-toggle='modal' data-target='#myModal'>
  						<i class="material-icons">mail_outline</i><br/>
  						Click to add/remove recipient(s)
					</button>
					<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  						<div class='modal-dialog'>
    						<div class='modal-content'>
     							<div class='modal-header'>
     								<h4 class='modal-title' id='myModalLabel'>Recipient list</h4> 
        							<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
   								</div>
      							<div class='modal-body'>
        							<p><b>Below is a list of student categories entrusted by <span style="color:grey;">gnit</span> to ensure that your upload makes it on to our system. Please select the most relevant recipient group(s) to the material you want to share:</b></p>
      								<form id="categorycheck">
      								<?php
      									foreach($admin_category_arr as $category)
      									{
      										echo '<input type="checkbox" value="' . $category . '"/> ' . $category . ' <br />';
      									}
      								?>
      								</form>
      							</div>
      							<div class='modal-footer'>
       								<button class='btn btn-success' id='btn-save' data-toggle='modal' data-target='#myFlagModal'>Save</button>
      							</div>
    						</div>
  						</div>
					</div>
					
				</div>
				<div class="col-lg-6" style="padding-top:30px;">
					<fieldset class="the-fieldset">
						<legend class="the-legend"><b>Have a problem/solution you would like to share?</b></legend>
						<form id="userinput">
							<div class="form-group">
								<div class="autocomplete" style="width:300px;">
									<input id="myModule" name="myModule" type="text" placeholder="Select module code e.g WVNS221">
								</div>
							</div>
							<div class="form-group">
  								<div style="width:40%;">
  									<label for="year">Resource Year:</label>
  									<input class="form-control" type="number" id="year" name="year" />
  								</div>
  							</div>	
 							<div class="form-group">
  								<label for="mySub">Category:</label>
  							 	<div class="autocomplete" style="width:300px;">
    								<input type="text" id="mySub" name="mySub" placeholder="Problem/Question Category or description" />
  								</div>	
  							</div>	
  							<div class="form-group">
								<label for="probsol">Problem/Solution</label>
  								<select class="selectpicker" id="probsol" name="probsol">
  									<option> </option>
  									<option>Problem</option>
  									<option>Solution</option>
								</select>			
							</div>	
							<div class="form-group">
								<label for="uploadtext_opp">Attachment/Text</label>
 								<select class="selectpicker" id="uploadtext_opp" name="uploadtext_opp">
 									<option> </option>
  									<option>Attach file</option>
  									<option>Text</option>
								</select>					
							</div> 		
 							<div class="container container-fluid" id="file_text_sect" style="width:100%;">		
  								<!--
  									func.js will fill this when an option has been chosen above
  								-->				
  							</div>
  							<br/>
  							<br/>
  							<div class="form-group">
  								<label for="meta"><small>
  									Provide key words that will aid in finding this upload in searches or make it unique and are descriptive of the upload.
  									Leave a space between each word and don't skip over to the next line.
  								</small></label>					
  								<textarea class="form-control" id="meta" name="meta" rows="3"></textarea>
  							</div>  
  							<!--List of categories-->	
  							<div id="invisiblecategories" style="height:0px;width:0px;visibility:hidden;">
								<!-- 
									input field with content admin categories here 
								-->
  							</div>						
						</form>
						<button class="btn btn-success" id="btn-share">Share</button>
					</fieldset>
				</div>
				<div class="col-lg-3">
					
				</div>
			</div>
		</div>
		<!-- string of modules -->
		<div id="mod-str" style="visibility:hidden;"><?php echo $str_module; ?></div>
<!-- Footer -->
		<footer class="footer small bg-light" style="position: fixed; left: 0; bottom: 0; width: 100%; height: 7%;">
			<div class="container">
				<div class="row">				
      				<div class="col-lg-7">
      					<br />
        				<span class="text-secondary"><?php echo date('Y'); ?> &copy; <b>gnit</b> by LS Masondo. <br />In association with NWU's B!TS</span>
      				</div>
      			</div>
      		</div>
    	</footer>
	</body>
</html>