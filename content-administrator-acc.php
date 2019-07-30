<?php
	require 'objects/log_error.php';
	require 'objects/session_life.php';
	require 'objects/admin.php';
	require 'objects/content_admin.php';
	require 'objects/varsity.php';
	require 'objects/db_connect.php';
	require 'objects/module.php';
	require 'objects/get_modules.php';
	require 'objects/modulearr_to_str.php';
	require 'objects/get_accept_user_input.php';
	
	session_start();
	session_life();
	
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');
	header('Expires: 0');
	
	$content_admin = null;
	$module = null;
	$str_module = null;
	$input_check = null;
	if(!isset($_SESSION['content-administrator']))
	{
		header('Location:admin-login.php');
		exit();
	}
	else 
	{
		$content_admin = $_SESSION['content-administrator'];
		$inst_id = $content_admin->get_varsity()->get_id();
		$module = arr_module($inst_id);
		$str_module = mod_arr_to_str($module, 2);
		
		$admin_id = $content_admin->get_id();
		if(get_accept_user_input($admin_id))
		{
			$input_check = '<input type="checkbox" id="acceptinput" name="acceptinput" checked />';
		}
		else
		{
			$input_check = '<input type="checkbox" id="acceptinput" name="acceptinput" />';
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title>Gnit: Content Administrator</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-select.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-select.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.min.js"></script>
		<link rel="stylesheet" href="css/summernotesyle.css">
		<script src="js/summernotestyle.js"></script>
		<link rel="stylesheet" href="css/styleform.css">
		<link rel="stylesheet" href="css/auto_mod.css" />
		<link rel="stylesheet" href="css/auto_sub.css" />
		<script src="js/auto_mod.js"></script>
		<script src="js/auto_sub.js"></script>
		<script src="js/sweetalert.min.js"></script>
		<script>
			<?php
				$file = fopen("js/contentadmin.js", "r");
				while(!feof($file))
				{
					echo fgets($file) . "\n";
				}
				fclose($file);
			?>
		</script>
	</head>
	
	<body>
<!-- Nav -->
		<nav class="navbar navbar-expand-md navbar-white bg-white sticky-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"> <img src="img/gnit-darkblue.jpg" alt="gnit" style="width:120px;" /> </a>
				<h1 class="page-desc"><b>Upload Portal</b></h1>
			</div>
		</nav>
<!-- user content -->
		<div class="container-fluid" id="content" style="padding-bottom:7%;">
			<div class="row">
				<div class="col-md-10">

				</div>
				<div class="col-md-2">
					<button class="btn btn-default"><a href="#">Help</a></button>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3">
					<small>
						<p>
							<b>Administrator:</b> <?php echo $content_admin->get_name(); ?>
							<br/>
							<b>Capacity:</b> <?php echo $content_admin->get_pos(); ?>
							<br/>
							<b>Email:</b> <?php echo $content_admin->get_email(); ?>
						</p>
					</small>
					<form id="input-check-form">
						<p>
							<?php echo $input_check; ?> Recieve material on your email account from students
						</p>	
					</form>				
				</div>
				<div class="col-lg-6 align-items-center" style="margin: 0 auto; padding-top: 25px;">
					<div class="container container-fluid">
						<form id="uploadform">
							<fieldset class="the-fieldset">
								<legend class="the-legend"><b>Catalogue and Upload File</b></legend>
								<!-- form components -->
 		 						<div class="form-group">
  									<div class="autocomplete" style="width:300px;">
  										<div class="row">
  											<div class="col-sm-9">
    											<input id="myModule" name="myModule" type="text" placeholder="Enter the module code">
    										</div>
    										<div class="col-sm-3">
    											<span class="btn btn-secondary" id="addmodule-btn">Add a module</span>
    										</div>
    									</div>
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
									<label for="probsol">Upload type:</label>
  									<select class="selectpicker" id="probsol" name="probsol">
  										<option> </option>
  										<option>Problem</option>
  										<option>Solution</option>
									</select>			
								</div>	
								<div class="form-group">
									<label for="uploadtext_opp">File type:</label>
 									<select class="selectpicker" id="uploadtext_opp" name="uploadtext_opp">
 										<option> </option>
  										<option>Attach file</option>
  										<option>Text</option>
									</select>					
								</div> 																						
  								<div class="container container-fluid" id="file_text_sect">		
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
							</fieldset>
						</form>
						<button class="btn btn-success" id="btn-upload">Upload</button>
						<button class="btn btn-danger" id="btn-cancel">Cancel & Logout</button>
						<button class="btn btn-outline-warning" id="btn-duplicate">Check for Duplicates</button>
					</div>
				</div> 
				<div class="col-lg-3">

				</div>
		</div>
		<!-- string of modules -->
		<div id="mod-str"><?php echo $str_module; ?></div>
<!-- footer -->		
		<footer class="footer small bg-light" style="position: fixed; left: 0; bottom: 0; width: 100%; height: 7%;">
			<div class="container">
				<div class="row">				
      				<div class="col-sm-7">
        				<span class="text-secondary"><?php echo date('Y'); ?> &copy; <b>gnit</b> by LS Masondo. <br />In association with NWU's B!TS</span>
      				</div>
      				<?php
      					if(isset($_SESSION['technical-administrator']))
      					{
      						echo '<div class="col-lg-5 text-right">
      								<a href="technical-administrator-acc.php">Technical Admin Portal</a>
      							  </div>';
      					}
      				?>
      			</div>
      		</div>
    	</footer>
	</body>
</html>