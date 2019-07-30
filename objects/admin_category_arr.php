<?php
/*
 * Retrieve all distinct capacity(positions) content administrator
 */
	function admin_category_arr()
	{
		$category_arr = array();
		
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT DISTINCT administrator.capacity FROM administrator, content_admin WHERE administrator.adminid=content_admin.adminid AND content_admin.accept_input=TRUE ORDER BY administrator.capacity;");
		$stmt->execute(array()); 
		$row = $stmt->fetchAll();
		
		if($row == null)
		{
			return false;
		}
		else 
		{
			foreach($row as $data) 
			{
				$category_arr[] = $data['capacity'];
			}	
		}
		
		return $category_arr;
	}
?>