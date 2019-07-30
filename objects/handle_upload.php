<?php
	
	//require 'log_error.php';
	require 'session_life.php';
	require 'admin.php';
	require 'content_admin.php';
	require 'varsity.php';
	require 'db_connect.php';
	require 'module.php';
	require 'get_modules.php';
	
	
	session_start();
	session_life();
	 
	function valid_module($module_name, $inst_id)
	{
		/*
		 * returns Module object or null
		 */
		 
		function before ($ch, $inthat)
    	{
    	    return substr($inthat, 0, strpos($inthat, $ch));
    	};
		 
		if(isset($module_name))
		{
			foreach(arr_module($inst_id) as $mod) 
			{
				if(before('-', $module_name) == $mod->get_code())
				{
					return $mod;
				}
			}
		} 	
		return null;
	}
	
	if(isset($_POST))
	{
		$admin_id = null;
		$module = null;
		$year = null;
		$category = null;
		$probsol_dist = null;
		$file_or_text = null;
		$file = null;
		$text = null;
		$meta = null;
		$content = "pending";
		

		if(isset($_SESSION['content-administrator']))
		{
	
			$admin = $_SESSION['content-administrator'];
			$admin_id = $admin->get_id();
			$varsity = $admin->get_varsity();
			$inst_id = $varsity->get_id();
			// verification start
			if(isset($_POST['myModule']))
			{
				/*
				 * verify module
				 */
				$module = valid_module($_POST['myModule'], $inst_id);
				if(!isset($module))
				{
					echo "Alert: Please provide a valid module code";
					exit();
				}
			}
			else
			{
				echo "Alert: Please provide a valid module code";
				exit();
			}
			
			if(isset($_POST['year']))
			{
				if(is_numeric($_POST['year']))
				{
					$year = (int)$_POST['year'];
					$currentyear = (int)date('Y');
					if(($year < 1994) || ($year > ($currentyear + 2)))
					{
						echo "Alert: Please provide a valid year the upload is from.";
						exit();
					}
				}
				else 
				{
					echo "Alert: Please provide a valid year the upload is from.";
					exit();
				}
			}
			else 
			{
				echo "Alert: Please provide a valid year the upload is from.";
				exit();
			}
			
			if(isset($_POST['mySub']))
			{
				$category = $_POST['mySub'];
				if($category == "")
				{
					echo "Alert: Enter a descriptive category for the upload \n\n Examples:\nExamination Opp 1 2018 \nExamination Opp 1 2017 Question 2 \n Chapter 4: Derivatives Exercise 2.3 Solution etc..";
				}
			}
			else 
			{
				echo "Alert: Enter a descriptive category for the upload \n\n Examples:\nExamination Opp 1 2018 \nExamination Opp 1 2017 Question 2 \n Chapter 4: Derivatives Exercise 2.3 Solution etc..";	
				exit();
			}
			
			if(isset($_POST['probsol']))
			{
				$probsol_dist = $_POST['probsol'];
				if(($probsol_dist != "Problem") && ($probsol_dist != "Solution"))
				{
					echo "Alert: State the upload type to be either a problem or solution";
					exit();
				}
			}
			else 
			{
				echo "Alert: State the upload type to be either a problem or solution";
				exit();
			}
			
			if(isset($_POST['uploadtext_opp']))
			{
				$file_or_text = $_POST['uploadtext_opp'];
				if(($file_or_text != "Attach file") && ($file_or_text != "Text"))
				{
					echo "Alert: [File type] Please provide a file or text as an attachment&";
					exit();
				}
			}
			
			if($file_or_text == "Attach file")
			{
				if(isset($_FILES['fileup']))
				{
					if($_FILES['fileup']['name'] == "")
					{
						echo "Alert: [File type] Please provide a file or text as an attachment*";
						exit();
					}
					else
					{
						$file = $_FILES['fileup'];
					}
				}
				else
				{
					echo "Alert: [File type] Please provide a file or text as an attachment3";
					exit();
				}
			}
			
			if($file_or_text == "Text")
			{
				if(isset($_POST['editordata']))
				{
					$text = $_POST['editordata'];
					if(strlen($text) < 20)
					{
						echo 'Alert: [File type] Please provide a file or text as an attachment4';
						exit();
					}
				}
				else
				{
					echo "Alert: [File type] Please provide a file or text as an attachment^";
					exit();	
				}
			}
			
			if(isset($_POST['meta']))
			{
				$meta = $_POST['meta'];
				if(strlen($meta) < 8)
				{
					echo "Alert: Please provide more search keywords. This goes a long way in helping us correctly categorise content";
					exit();
				}
			}
			else
			{
				echo "Alert: Please provide more search keywords. This goes a long way in helping us correctly categorise content. (Read instruction above input area)";
				exit();
			}
			//validation end
			
			/*
			 * place upload in storage and database
			 */
			$conn = dbconn();

			$filename = $file['name'];
			try
			{
				$stmt = $conn->prepare("INSERT INTO upload (original_fname, upld_content, upld_meta, adminid, pk_mod) VALUES (:original_name, :content, :meta, :adminid, :moduleid)");
    			$stmt->bindParam(':original_name', $filename);
    			$stmt->bindParam(':content', $content);
    			$stmt->bindParam(':meta', $meta);
				$stmt->bindParam(':adminid', $admin_id);
				$mod_id = $module->get_id();
				$stmt->bindParam(':moduleid', $mod_id);
				$stmt->execute();
				/*
				 * Get id of upload
				 */
				$last_upld_id = $conn->lastInsertId();
					
				if($file_or_text == "Attach file")
				{
					if(mkdir('files/' . $last_upld_id, 0777, true) == false)//create folder with id as name
					{
						/*
				 		* if creating the folder fails
				 		*/
						echo "Alert: We encountered a problem saving your file. It has been reported";
						exit();
						//error_log();
					}
					$file_tmp = $file['tmp_name'];
					//check file size
					if($file['size'] < 5097152)
					{
						$ext = explode('.',$filename); //if you try place the next 3 lines in one variable php spazzes -_-
						$get_ext = end($ext);
						$file_ext=strtolower($get_ext);
					
						$content = "files/". $last_upld_id . "/gnit_file." . $file_ext; // file's address
						move_uploaded_file($file_tmp, $content); //move to folder with upload id as the name
					
						//change the value of UPLD_CONTENT to the location of the file
						$conn->prepare("UPDATE upload SET upld_content=:content WHERE upld_id=:id")->execute(array(':content' => $content,
																								   	   ':id' => $last_upld_id));		
					}
					else
					{
						echo "Alert: File is too large";
					}
				}
				else
				{
					$content = $text;
					$conn->prepare("UPDATE upload SET upld_content=:content WHERE upld_id=:id")->execute(array(':content' => $content,
																										   	   ':id' => $last_upld_id));
				}
				/*
			     * insert into problem/solution
				 */
				 if($probsol_dist == "Problem")
			     {
					$stmt = $conn->prepare("INSERT INTO problem (prob_year, prob_subject, upld_id) VALUES (:year, :subject, :uploadid)");
    				$stmt->bindParam(':year', $year);
    				$stmt->bindParam(':subject', $category);
					$stmt->bindParam(':uploadid', $last_upld_id);
					$stmt->execute();	
					
					echo "\tSuccessful :-)\n\nSearch key: " . "GNIT" . $last_upld_id . "P-" . $module->get_code();
					exit();
				 }
				else if($probsol_dist == "Solution")
				{
					$meta .= " " . $year . " " . $module->get_name();
					$stmt = $conn->prepare("INSERT INTO solution (description, upld_id) VALUES (:description, :uploadid)");
    				$stmt->bindParam(':description', $category);
    				$stmt->bindParam(':uploadid', $last_upld_id);
					$stmt->execute();		
						
					$conn->prepare("UPDATE upload SET upld_meta=:meta WHERE upld_id=:id")->execute(array(':meta' => $meta,
																										 ':id' => $last_upld_id));																											 				
					
					echo "\tSuccessful :-)\n\nSearch key: " . "GNIT" . $last_upld_id . "S-" . $module->get_code();
					exit();
				}
			}
			catch(PDOException $e)
			{
				echo "Alert: Encountered a problem with your upload. Please try again ".$e->getMessage();
				exit();
			}	
			catch(Exception $e)
			{
				echo "Alert: Encountered a problem with your upload. Please try again ".$e->getMessage();
				exit();
			}		
		}
		else
		{
			header('Location:logout.php');
			exit();
		}
	}
?>