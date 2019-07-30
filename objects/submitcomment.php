<?php
	require "db_connect.php";
	
	if(isset($_POST['uploadid']))
	{
		$upload_id = (int)$_POST['uploadid'];
		if(isset($_POST['comment']))
		{
			$comment = $_POST['comment'];
			if(strlen($comment) > 3 && strlen($comment) < 500)
			{
				$conn = dbconn();
				$stmt = $conn->prepare("INSERT INTO comment (upld_id, thecomment) VALUES (:id, :comment)");
				$stmt->bindParam(':id', $upload_id);
				$stmt->bindParam(':comment', $comment);
				$stmt->execute();
			}
			else 
			{
				echo "Alert: Invalid comment length";
				exit();
			}
			echo "Thank you for your contribution ;-)";
		}		
	}
	else 
	{
		echo"error";	
	}
	
?>