<?php
	function get_accept_user_input($admin_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT accept_input FROM content_admin WHERE adminid=:id");
		$stmt->execute(array("id" => $admin_id)); 
		$row = $stmt->fetch();
		
		if($row == null)
		{
			return false;
		}
		else
		{
			return (bool)$row['accept_input'];
		}
	}
?>