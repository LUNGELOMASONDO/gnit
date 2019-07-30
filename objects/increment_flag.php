<?php
	require 'db_connect.php';
	
	if(isset($_POST['uploadid']))
	{		
		$upload_id = (int)$_POST['uploadid'];
		$conn = dbconn();
		
		if(isset($_POST['copyright']))
		{
			$description = strtoupper($_POST['copyright']);
			$stmt = $conn->prepare("INSERT INTO flags (upld_id, flag_description) VALUES (:id, :desc)");
			$stmt->bindParam(':id', $upload_id);
			$stmt->bindParam(':desc', $description);	
			$stmt->execute();
		}
		
		if(isset($_POST['incomplete']))
		{
			$description = strtoupper($_POST['incomplete']);
			$stmt = $conn->prepare("INSERT INTO flags (upld_id, flag_description) VALUES (:id, :desc)");
			$stmt->bindParam(':id', $upload_id);
			$stmt->bindParam(':desc', $description);	
			$stmt->execute();
		}
		
		if(isset($_POST['incorrect']))
		{
			$description = strtoupper($_POST['incorrect']);
			$stmt = $conn->prepare("INSERT INTO flags (upld_id, flag_description) VALUES (:id, :desc)");
			$stmt->bindParam(':id', $upload_id);
			$stmt->bindParam(':desc', $description);	
			$stmt->execute();
		}
		
		if(isset($_POST['visibility']))
		{
			$description = strtoupper($_POST['visibility']);
			$stmt = $conn->prepare("INSERT INTO flags (upld_id, flag_description) VALUES (:id, :desc)");
			$stmt->bindParam(':id', $upload_id);
			$stmt->bindParam(':desc', $description);	
			$stmt->execute();
		}
		
		if(isset($_POST['information']))
		{
			$description = strtoupper($_POST['information']);
			$stmt = $conn->prepare("INSERT INTO flags (upld_id, flag_description) VALUES (:id, :desc)");
			$stmt->bindParam(':id', $upload_id);
			$stmt->bindParam(':desc', $description);	
			$stmt->execute();
		}
		
		if(isset($_POST['duplicate']))
		{
			$description = strtoupper($_POST['duplicate']);
			$stmt = $conn->prepare("INSERT INTO flags (upld_id, flag_description) VALUES (:id, :desc)");
			$stmt->bindParam(':id', $upload_id);
			$stmt->bindParam(':desc', $description);	
			$stmt->execute();
		}
		
		echo "Thank you for helping Gnit be better for all users ;-). For more assistance write to us at support@gnit.co.za";
	}
?>