<?php
	/*
	 * user feedback is for errors
	 * success results in a redirect
	*/
	require 'log_error.php';
	require 'session_life.php';
	require 'admin.php';
	require 'content_admin.php';
	require 'tech_admin.php';
	require 'varsity.php';
	require 'db_connect.php';
	
	if(isset($_POST))
	{
		session_start();
		
		session_life();
		
		if(isset($_SESSION['content-administrator']))
		{
			header('Location:../content-administrator-acc.php');
			exit();
			//echo 'Content Admin Session exists';
		}
		else if(isset($_SESSION['technical-administrator']))
		{
			header('Location:../technical-administrator-acc.php');
			exit();
			//echo 'Technical Admin Session exists'
		}
		else if(isset($_SESSION['content-administrator'], $_SESSION['technical-administrator']))
		{
			header('Location:../technical-administrator-acc');
			exit();
			//session in existance is both a technical admin and content admin
		}
		else 
		{
			//create a session and login
			if(isset($_SESSION))
			{
				$email = $_POST['email'];
				$password = $_POST['password'];
				/*
				 * if and errors occur in either of the 2 methods below
				 * they will exit the script
				 */
				 try
				 {
				 	$conn = dbconn();
					$stmt = $conn->prepare("SELECT * FROM administrator WHERE email=:email AND password=:password");
					$stmt->execute(array("email" => $email, "password" => $password)); 
					$row = $stmt->fetch();
					
					if($row == null)// <-- incorrect admin info provided
					{
						echo 'Alert: Incorrect email and/or password. Please try again.';
						exit();
					}
					else // <-- generic admin found
					{
						$admin_id = $row['ADMINID'];
						$name = $row['ADMIN_NAME'];
						$position = $row['CAPACITY'];
						$year = $row['POSTYEAR'];	
						$instid = $row['INST_ID']; //varsity
						
							/*
							* get university information
							*/
							$stmt = $conn->prepare("SELECT * FROM eduinstitution WHERE inst_id=:inst_id");
							$stmt->execute(array("inst_id" => $instid)); 
							$row = $stmt->fetch();
								
							$varsity = null;
								
							if($row != null)
							{
								$abbrev = $row['ACRONYM'];
								$inst_name = $row['INST_NAME'];
								$varsity = new Varsity($instid, $inst_name, $abbrev);
							}
							else 
							{
								$ERRCODE = "ERR003";
								err_log("No corrosponding university ID for admin: " . $adminid);	
								echo "ERR: Error has occured on your account and has been reported. Please try again later";
								exit();
							}
										
						/*
						 * Find out if admin is a content admin
						 */					
						$stmt = $conn->prepare("SELECT * FROM content_admin WHERE adminid=:id");
						$stmt->execute(array("id" => $admin_id)); 
						$row = $stmt->fetch();
						
						if($row == null)// <-- not a content admin but MIGHT be a tech admin
						{													
							/*
							 * Find out if admin is technical admin
							 */
							$stmt = $conn->prepare("SELECT * FROM technical_admin WHERE adminid=:id");
							$stmt->execute(array("id" => $admin_id)); 
							$row = $stmt->fetch();
							
							if($row == null) // <-- not a tech admin or a content admin
							{
								echo 'ERR: This admin account has not been afforded any access rights. Please try again later';
								err_log("Admin account has not been afforded any access rights: " . $admin_id );
								exit();
							}
							else // <-- admin is a tech admin
							{
								$ta_id = $row['TA_ID'];
								$technical_admin = new Tech_Admin($admin_id, $email, $varsity, $name, $position, $year, $ta_id);
								$_SESSION['technical-administrator'] = $technical_admin;
								header('Location:../techinical-administrator-acc.php');
								exit();
							}
						}
						else // <-- is a content admin could possible be a tech admin also
						{
							$ca_id = $row['CA_ID'];
							$content_admin = new Content_Admin($admin_id, $email, $varsity, $name, $position, $year, $ca_id);
							$_SESSION['content-administrator'] = $content_admin;
							
							/*
							 * check if admin is tech admin in addition to being a content admin
							 */
							$stmt = $conn->prepare("SELECT * FROM technical_admin WHERE adminid=:id");
							$stmt->execute(array("id" => $admin_id)); 
							$row = $stmt->fetch();
							
							if($row == null) // <-- admin is only a content admin
							{
								header('Location:../technical-administrator-acc.php');
								exit();
							}
							else // <-- admin is both a content and technical admin
							{
								$ta_id = $row['TA_ID'];
								$technical_admin = new Tech_Admin($admin_id, $email, $varsity, $name, $position, $year, $ta_id);
								$_SESSION['technical-administrator'] = $technical_admin;
								header('Location:../techinical-administrator-acc.php');
								exit();
							}
						}
					}
				 }
				 catch(PDOException $e)
				 {
				 	$ERRCODE = "ERR002";
					err_log("$ERRCODE: $e");
					echo "ERR: Gnit system downtime. Please check announcements and try again later";
					exit();
				 }
				 catch(Exception $e)
				 {
				 	$ERRCODE = "ERR004";
					err_log("$ERRCODE: $e");
					echo "ERR: Error occured. Please try again later";
					exit();
				 }
			}
			else
			{			
				header('Location:../admin_login.php');
				exit();
				//echo 'Attemp to create session failed';
			}	
		}
	}
	else 
	{
		session_unset();
		session_destroy();
		header('Location:../admin-login.php');
		//echo 'Incorrect means used to access login';
	}
?>