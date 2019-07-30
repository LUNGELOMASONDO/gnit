<?php
	require 'objects/log_error.php';
	require 'objects/session_life.php';
	require 'objects/admin.php';
	require 'objects/tech_admin.php';
	require 'objects/varsity.php';
	require 'objects/db_connect.php';
	
	session_start();
	session_life();
	
	if(!(isset($_SESSION['technical-administrator'])))
	{
		header('Location:admin-login.php');
		exit();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title>Gnit: Technical Administrator</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/func.js"></script>
	</head>
	<body>
<!-- Nav -->
		<nav class="navbar navbar-expand-md navbar-white bg-white sticky-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"> <img src="img/gnit-darkblue.jpg" alt="gnit" style="width:120px;" /> </a>
				<h1 class="page-desc"><b>Technical Administrator</b></h1>
			</div>
		</nav>	
		
<!-- footer -->
		<footer class="footer small bg-light" style="position: fixed; left: 0; bottom: 0; width: 100%; height: 11%;">
			<div class="container">
				<div class="row">				
      				<div class="col-sm-7">
        				<span class="text-secondary">2019 &copy; <b>gnit</b> by Lungelo S. Masondo. <br />In association with NWU-BITS</span>
      				</div>
      				<?php
      					if(isset($_SESSION['content-administrator']))
      					{
      						echo '<div class="col-lg-5 text-right">
      								<a href="content-administrator-acc.php">Upload Portal</a>
      							  </div>';
      					}
      				?>
      			</div>
      		</div>
    	</footer>	
	</body>
</html>