<?php
	require 'log_error.php';
	require 'session_life.php';
	require 'db_connect.php';
	require 'varsity.php';
	require 'admin.php';
	require 'content_admin.php';
	require 'tech_admin.php';
	
	session_start();
	session_life();
	
	function addmodule($code, $name, $id)
	{
		try
		{
			$conn = dbconn();
			$stmt = $conn->prepare("INSERT INTO moduleinst (mod_code, mod_name, inst_id) VALUES (:code, :name, :id)");
			$code = strtoupper($code);
    		$code = ltrim($code);
			$code = str_replace(' ', '', $code);
    		$stmt->bindParam(':code', $code);
    		$stmt->bindParam(':name', $name);
			$stmt->bindParam(':id', $id);
		
			if(isset($code) && ($code != ""))
			{
				$stmt->execute();
				echo "Successful :-)";
				exit();
			}
			else 
			{
				echo "Alert: Could not add the module";
				exit();
			}
		}
		catch(PDOException $e)
		{
			if(strpos($e->getMessage(), "Duplicate"))
			{
				echo $_POST['myModule'] . " is already in our system";
			}
			else
			{
				echo "Alert: Could not add the module ";
				exit();
			}
		}
		catch(Exception $e)
		{
			echo "Alert: Could not add the modue " . $e->getMessage();
			exit();
		}
	}
	
	if(isset($_POST))
	{
		if(isset($_SESSION['content-administrator']) || isset($_SESSION['technical-administrator']))
		{
			$admin = null;
			if(isset($_SESSION['technical-administrator']))
			{
				$admin = $_SESSION['technical-administrator'];
			}
			else
			{
				$admin = $_SESSION['content-administrator'];
			}
			if(isset($_POST['myModule']))
			{
				$code = $_POST['myModule'];
				if(isset($_POST['module-name']))
				{
					$name = $_POST['module-name'];
					$varsity_id = $admin->get_varsity()->get_id();
					addmodule($code, $name, $varsity_id);
				}
			}
		}
		else 
		{
			echo "Session timed out. Please login";	
		}
	}
	else 
	{
		header('Location:../admin-login.php');
		exit();	
	}
?>