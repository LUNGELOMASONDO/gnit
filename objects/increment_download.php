<?php
	require 'db_connect.php';
	
	if(isset($_POST['uploadid']))
	{
		$upload_id = $_POST['uploadid'];
		$conn = dbconn();
		$stmt = $conn->prepare("INSERT INTO download (upld_id) VALUES (:id)");
		$stmt->bindParam(':id', $upload_id);
		$stmt->execute();
	}
?>