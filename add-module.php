<?php
	require 'objects/session_life.php';
	require 'objects/log_error.php';
	require 'objects/varsity.php';
	require 'objects/admin.php';
	require 'objects/content_admin.php';
	require 'objects/tech_admin.php';
	require 'objects/db_connect.php';
	require 'objects/module.php';
	require 'objects/get_modules.php';
	require 'objects/modulearr_to_str.php';

	
	session_start();
	session_life();
	
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');
	header('Expires: 0');
	
	$admin = null;
	$name = null;
	$capacity = null;
	$email = null;	
	$str_module = null;
	
	if(isset($_SESSION['content-administrator']) && isset($_SESSION['technical-administrator']))
	{
		$admin = $_SESSION['technical-administrator'];
		$name = $admin->get_name();
		$capacity = $admin->get_pos();
		$email = $admin->get_email();	
		$inst_id = $admin->get_varsity()->get_id();
		$module = arr_module($inst_id);
		$str_module = mod_arr_to_str($module, 1);
	}else{
		if(isset($_SESSION['content-administrator']))
		{
			$admin = $_SESSION['content-administrator'];
			$name = $admin->get_name();
			$capacity = $admin->get_email();
			$email = $admin->get_pos();	
			$inst_id = $admin->get_varsity()->get_id();
			$module = arr_module($inst_id);
			$str_module = mod_arr_to_str($module, 1);
		}else{
			if(isset($_SESSION['technical-administrator']))
			{
				$admin = $_SESSION['technical-administrator'];
				$name = $admin->get_name();
				$capacity = $admin->get_pos();
				$email = $admin->get_pos();	
				$inst_id = $admin->get_varsity()->get_id();
				$module = arr_module($inst_id);
				$str_module = mod_arr_to_str($module, 1);
			}else{
				header('Location:objects/logout.php');
				exit();
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title>Gnit: Add a module</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-select.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-select.min.js"></script>
		<link rel="stylesheet" href="css/styleform.css">
		<link rel="stylesheet" href="css/auto_mod.css" />
		<script src="js/auto_mod.js"></script>
		<script src="js/sweetalert.min.js"></script>
		<script>
			<?php
				$file = fopen("js/addmodule.js", "r");
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
				<h1 class="page-desc"><b>Add a module</b></h1>
			</div>
		</nav>
<!-- content -->
		<div class="container-fluid" id="content" style="padding-bottom:20%;>
			<div class="row">
				<div class="col-lg-3">
					<small>
						<b>Administrator:</b> <?php echo $name; ?>
						<br/>
						<b>Capacity:</b> <?php echo $capacity ?>
						<br/>
						<b>Email:</b> <?php echo $email; ?>
					</small>					
				</div>
				<div class="col-lg-6 align-items-center" style="margin: 0 auto; padding-top: 25px;">
					<div class="container container-fluid">
						<form id="addmodule-form">
							<fieldset class="the-fieldset">
								<legend class="the-legend"><b>Add a module</b></legend>
								<div class="row">
									<div class="col-sm-5">
										<div class="form-group">
											<label for="myModule"><small>Enter the module code</small></label>
											<input id="myModule" name="myModule" type="text" placeholder="E.g. ALDE111">
										</div>
									</div>
									<div class="col-sm-7">
										<div class="form-group">
											<label for="module-name"><small>Enter the name</small></label>
											<input id="module-name" class="form-control" name="module-name" type="text" placeholder="E.g. Introduction to Academic Literacy">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="container">
										<small class="bg-light">1. The codes that popup as you type are already in our system so can not be added again</small>
										<br/>
										<small class="bg-light">2. For the full name that accompanies a module code see relevant module study guide or faculty handbook</small>
									</div>
								</div>
							</fieldset>
						</form>
						<br/>
						<button class="btn btn-default" id="btn-backtoadmin">Back</button>
						<button class="btn btn-success" id="btn-addmodule">Add module</button>
					</div>		
				</div>
				<div class="col-lg-3">
					
				</div>
			</div>
		</div>
		<!--string of modules -->
		<div id="mod-str"><?php echo $str_module; ?></div>
<!-- footer -->		
		<footer class="footer small bg-light" style="position: fixed; left: 0; bottom: 0; width: 100%; height: 11%;">
			<div class="container">
				<div class="row">				
      				<div class="col-lg-7">
        				<span class="text-secondary">2019 &copy; <b>gnit</b> by LS Masondo. <br />In association with NWU's B!TS</span>
      				</div>
      			</div>
      		</div>
    	</footer>
	</body>
</html>