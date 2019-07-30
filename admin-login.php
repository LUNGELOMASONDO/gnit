<?php
	require "objects/session_life.php";
	
	session_start();
	session_life();
	
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');
	header('Expires: 0');
	
	if(isset($_SESSION['content-administrator']))
	{
		//content admin session already exists
		header('Location:content-administrator-acc.php');
		exit();
	}
	else if(isset($_SESSION['technical-administrator']))
	{
		//technical admin session already exists
		header('Location:technical-administrator-acc.php');
		exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title> gnit: Admin Login </title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/sweetalert.min.js"></script>
		<script>
			<?php
				$file = fopen("js/adminlogin.js", "r");
				while(!feof($file))
				{
					echo fgets($file) . "\n";
				}
				fclose($file);
			?>
		</script>
	</head>
	<body>
<!-- Navigation -->
		<nav class="navbar navbar-expand-md navbar-white bg-white sticky-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"> <img src="img/gnit-darkblue.jpg" alt="gnit" style="width:120px;" /> </a>
				<h1 class="page-desc"><b>Admin Login</b></h1>
			</div>
		</nav>
<!-- User content -->
		<div class="container-fluid" id="content" style="padding-bottom:10%;">
			<div class="row">
				<div class="col-md-4">
					
				</div>
				<div class="col-md-4 align-items-center">
					<form>
						<div class="form-group">
							<label for="txtemail">Email Address</label>
							<input type="email" class="form-control" name="txtemail"  id="txtemail" />
						</div>
						<div class="form-group">
							<label for="txtpassword">Password</label>
							<input type="password" class="form-control" name="txtpassword" id="txtpassword" />
						</div>
						<small id="errormessage" class="form-text text-warning"></small>
					</form>
					<button class="btn btn-default" name="btnlogin" id="btnlogin">Login</button>
					<button class="btn btn-link" name="btnforgot" id="btnforgot">Forgot password</button>
				</div>
				<div class="col-md-4">
					
				</div>
			</div>
		</div>
<!-- Footer -->
		<footer class="footer small bg-light" style="position: fixed; left: 0; bottom: 0; width: 100%; height: 11%;">
			<div class="container">
				<div class="row">				
      				<div class="col-sm-7">
        				<span class="text-secondary"><?php echo date('Y'); ?> &copy; <b>gnit</b> by LS Masondo. <br />In association with NWU's B!TS</span>
      				</div>
      			</div>
      		</div>
    	</footer>
	</body>
</html>

<?php
	exit();
?>