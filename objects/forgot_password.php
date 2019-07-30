<?php
	require 'log_error.php';
	require 'db_connect.php';
	require 'session_life.php';
	require 'sendmail.php';
	
	session_start();
	session_life();
	
	if(isset($_SESSION))
	{
		if(isset($_POST['email']))
		{
			$email = $_POST['email'];
			
			$conn = dbconn();
			$stmt = $conn->prepare("SELECT admin_name, password FROM administrator WHERE email=:email;");
			$stmt->execute(array("email" => $email)); 
			$row = $stmt->fetch();
			
			if($row == null)
			{
				echo "ERR: Email address [" . $email . "] does not exist on gnit";
				exit();
			}
			else
			{
				$name = $row['admin_name'];
				$password = $row['password'];
				
				$subject = "Password Recovery";
				$message = "Greetings " . $name . " from the gnit student support team. Here's your password: " . "[" . $password . "]";
				if(sendmail($email, $subject, $message))
				{
					echo "Message: Your password has been mailed to [" . $email . "]";
					exit();
				}
				else 
				{
					echo "ERR: Encountered an issue mailing: [" . $email . "]";
				}
			}
		}
		else 
		{
			header('Location:../index.php');
			exit();
		}
	}
?>