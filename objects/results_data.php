<?php
   	function is_problem_solution($upload_id)
	{
		/*
		 * CHECKS FOR EITHER PROBLEM OR SOLUTION
		 */
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT sol_id FROM solution WHERE upld_id=:id");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row == null)
		{
			$stmt = $conn->prepare("SELECT problem_id FROM problem WHERE upld_id=:id");
			$stmt->execute(array(":id" => $upload_id)); 
			$row = $stmt->fetch();
			
			if($row == null)
			{
				return null; // UPLOAD ID IS NOT A PROBLEM OR A SOLUTION
			}
			else
			{
				return "Problem"; 
			}
		}
		else 
		{
			return "Solution";			
		}
	}

    function filetype_check($key)
    {
        $regexp="%files/\d{1,9}/gnit_file%";

        if (!preg_match($regexp, $key))
        {
            return FALSE;
        }
        
        return TRUE;
    }

	function get_content_type($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT upld_content FROM upload WHERE upld_id=:id");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row == null)
		{
			return null;
		}
		else 
		{
			$content = $row['upld_content'];
			if(filetype_check($content))
			{
				/*
				 * DOWNLOADABLE FILE
				 */
				$exp = explode('.',$content);
				$end = end($exp);
				$file_ext=strtolower($end);
				
				$image_extentions_arr = array("jpeg", "png", "tiff", "jpg");
				$pdf_extension = "pdf";
				$word_doc_extension = "docx";
				$text_extension = "txt";
				
				if(in_array($file_ext, $image_extentions_arr))
				{
					return "image";
				}
				else if($file_ext == "pdf")
				{
					return "pdf";
				}
				else if($file_ext == "docx")
				{
					return "doc";
				}
				else if($file_ext == "txt")
				{
					return "text file";
				}
				else
				{
					return "unknown";
				}
			}
			else 
			{
				return "text";
			}
		}
	}

	function set_badge_color($content_type)
	{
		$badge = "badge badge-secondary";
		//set badge color
		if($content_type == "image")
		{
			$badge = "badge badge-success";
		}
		elseif($content_type == "pdf")
		{
			$badge = "badge badge-danger";
		}
		elseif($content_type == "doc")
		{
			$badge = "badge badge-primary";
		}
		elseif($content_type == "text file")
		{
			$badge = "badge badge-info";
		}
		elseif($content_type == "unknown")
		{
			$badge = "badge badge-warning";
		}
		elseif($content_type == "text")
		{
			$badge = "badge badge-dark";
		}
		
		return $badge;
	}

	function get_contents_heading($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT description FROM solution WHERE upld_id=:id");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row == null)
		{
			$stmt = $conn->prepare("SELECT prob_subject FROM problem WHERE upld_id=:id");
			$stmt->execute(array(":id" => $upload_id)); 
			$row = $stmt->fetch();
			
			if($row != null)
			{
				return $row['prob_subject'];
			}
		}
		else 
		{
			return $row['description'];
		}
	}

	function get_upload_key($upload_id, $prob_sol)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT mod_code FROM moduleinst, upload WHERE upload.upld_id=:id AND upload.pk_mod = moduleinst.pk_mod");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		if($row != null)
		{
			if($prob_sol == "Problem")
			{
				return "GNIT" . $upload_id . "P-" . $row['mod_code'];
			}
			elseif($prob_sol == "Solution")
			{
				return "GNIT" . $upload_id . "S-" . $row['mod_code'];
			}
		}
		else
		{
			return null;
		}
	}

	function num_downloads($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT COUNT(*) FROM download WHERE download.upld_id=:id;");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		return (int)$row['COUNT(*)'];
	}

	function num_flags($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT COUNT(*) FROM flags WHERE flags.upld_id=:id;");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		return (int)$row['COUNT(*)'];
	}

	function problem_year($upload_id, $prob_sol)
	{
		
		if($prob_sol == "Problem")
		{
			$conn = dbconn();
			$stmt = $conn->prepare("SELECT prob_year FROM problem WHERE problem.upld_id=:id;");
			$stmt->execute(array(":id" => $upload_id)); 
			$row = $stmt->fetch();
			
			if($row != null)
			{
				return $row['prob_year'];
			}
		}
		else 
		{
			return "";	
		}
	}

	function get_timestamp($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT upld_time FROM upload WHERE upload.upld_id=:id;");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row != null)
		{
			$sqltimestamp = $row['upld_time'];
			$unix_sql_timestamp = strtotime($sqltimestamp);
			$form_date = date("d/m/Y", $unix_sql_timestamp);
			$form_time = date("g:i a", $unix_sql_timestamp);
			
			$date_today = strtotime("today");
			$date_tomorrow = strtotime("yesterday");
			
			if($form_date == date("d/m/Y", $date_today))
			{
				$form_date = "Today";
			}
			else if($form_date == date("d/m/Y", $date_tomorrow))
			{
				$form_date = "Yesterday";
			}
			
			return $form_date . ", " . $form_time;
		}
		return "00/00/0000 00:00 am";
	}

	function get_admin_name($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT administrator.admin_name FROM administrator, upload WHERE upload.adminid=administrator.adminid;");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row != null)
		{
			return $row['admin_name'];
		}
		else
		{
			return "";	
		}
	}

	function get_position($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT administrator.capacity FROM administrator, upload WHERE upload.adminid=administrator.adminid;");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row != null)
		{
			return $row['capacity'];
		}
		else
		{
			return "";	
		}
	}
	
	function get_upload_module($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT mod_code FROM moduleinst, upload WHERE upload.upld_id=:id AND upload.pk_mod = moduleinst.pk_mod");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		if($row != null)
		{
			return $row['mod_code'];
		}
		else
		{
			return null;
		}
	}

	function get_upload_year($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT prob_year FROM problem, upload WHERE upload.upld_id=:id AND upload.upld_id = problem.upld_id;");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		if($row != null)
		{
			return $row['prob_year'];
		}
		else
		{
			return "";
		}
	}

	function get_content($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT upld_content FROM upload WHERE upload.upld_id=:id;");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row != null)
		{
			return $row['upld_content'];
		}
		else
		{
			return "";	
		}
	}

	function get_file($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("SELECT upld_content FROM upload WHERE upld_id=:id");
		$stmt->execute(array(":id" => $upload_id)); 
		$row = $stmt->fetch();
		
		if($row == null)
		{
			return null;
		}
		else 
		{
			$content = $row['upld_content'];
			if(filetype_check($content))
			{
				return $content;
			}
		}	
	}

	function get_file_ext($content)
	{
		$exp = explode('.',$content);
		$end = end($exp);
		$file_ext=strtolower($end);
		return $file_ext;
	}
?>