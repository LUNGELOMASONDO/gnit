<?php
	require "log_error.php";
	require "db_connect.php";
	require "session_life.php";
	require "admin.php";
	require "content_admin.php";
	
	session_start();
	session_life();
	/* 
	 * will only run on post request
	 */
	
	if(isset($_SESSION['content-administrator']))
	{
		$content_admin = $_SESSION['content-administrator'];
		$admin_id = $content_admin->get_id();
		if(isset($_POST))
		{
			if(isset($_POST['acceptinput']))
			{
				$conn = dbconn();
				$stmt = $conn->prepare("UPDATE content_admin SET accept_input=:acc_input WHERE adminid= :id");
				$stmt->execute(array(':acc_input' => TRUE, ':id' => $admin_id));
				$stmt = null;
				echo "Students will now be able to send you material";
				exit();
			}
			else 
			{
				$conn = dbconn();
				$stmt = $conn->prepare("UPDATE content_admin SET accept_input=:acc_input WHERE adminid= :id");
				$stmt->execute(array(':acc_input' => FALSE, ':id' => $admin_id));
				$stmt = null;
				echo "Students will now not be able to send you material";
				exit();
			}
		}
	}
	else 
	{
		echo "Could not change user input status";	
		exit();
	}
?>