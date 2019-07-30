<?php
    function searchkey_check($key)
    {
        $regexp="/GNIT\d{1,9}[SP]-/";

        if (!preg_match($regexp, $key))
        {
            return FALSE;
        }
        
        return TRUE;
    }

	function search($magnet)
	{
		$result = new ArrayObject(array());
		$conn = dbconn();
		try
		{
			/*
		 	* checks if a search key was entered
		 	*/
			$magnet = strtoupper($magnet);
			$regex_match = array();
			if(searchkey_check($magnet))
			{		
				$searchkey = $magnet;
				$key = substr($searchkey, strpos($searchkey, "T") + 1, -1); // extract the UPLD id and S/P(PROBLEM/SOLUTION) 
				if(strpos($key, "S") !== false)
				{
					/*
					 * SEARCH KEY: solution
					 */
				 
					//remove S from the string
					$upld_id_str = substr($key, 0, -1);
					$upld_id = (int)$upld_id_str;
				
					$stmt = $conn->prepare("SELECT * FROM upload WHERE upld_id=:id");
					$stmt->execute(array(":id" => $upld_id)); 
					$row = $stmt->fetch();
				
					if($row == null)
					{
						return null;
					}
					else 
					{
						$upld_id = $row["UPLD_ID"];
						
						$stmt = $conn->prepare("SELECT sol_id FROM solution WHERE upld_id=:id");
						$stmt->execute(array(":id" => $upld_id)); 
						$row = $stmt->fetch();
						
						if($row == null)
						{
							return null;
						}
						else
						{
							$result[$upld_id] = 100000;
							return $result;
						}
					}
				}
				else if(strpos($key, "P") !== false)
				{
					/*
					 * SEARCH KEY: PROBLEM
					 */
					//remove P from the string
					$upld_id_str = substr($key, 0, -1);
					$upld_id = (int)$upld_id_str;
		
					$stmt = $conn->prepare("SELECT * FROM upload WHERE upld_id=:id");
					$stmt->execute(array(":id" => $upld_id)); 
					$row = $stmt->fetch();
				
					if($row == null)
					{
						return null;
					}
					else 
					{
						$upld_id = $row["UPLD_ID"];

						$stmt = $conn->prepare("SELECT * FROM problem WHERE upld_id=:id");
						$stmt->execute(array(":id" => $upld_id)); 
						$row = $stmt->fetch();
						
						if($row == null)
						{
							return null;
						}
						else
						{
							$result[$upld_id] = 100000;								
							return $result;
						}
					}				 
				}
			}
			else
			{
				/*
				 * NO SEARCH KEY THEREFORE KEY WORDS SEARCH
				 */
				
				$result_score = new ArrayObject(array()); // THE SCORE OF EVERY UPLOAD
				
				$data = $conn->query("SELECT * FROM upload")->fetchAll();	
				
				foreach($data as $row)
				{
					/*
					 * set initial score to 0 for all uploads
					 */
					$result_score[$row["UPLD_ID"]] = 0;
				}
				
				/*
				 * score every upload 
				 */
				$result_score = question_score($result_score, $magnet);
				$result_score = solution_score($result_score, $magnet);
				$result_score = module_score($result_score, $magnet);
				$result_score = year_score($result_score, $magnet);			
				$result_score = description_score($result_score, $magnet);
				$result_score = subject_score($result_score, $magnet);
				$result_score = meta_score($result_score, $magnet);
				$result_score = modulename_score($result_score, $magnet);
				$result_score = part_module_score($result_score, $magnet);
				
				$result_score_arr = (array)$result_score;
				arsort($result_score_arr); // sort from highest to lowest points
				$result_score = new ArrayObject($result_score_arr);
				
				foreach($result_score as $score)
				{
					/*
					 * IF THE 1ST(GREATEST/MAX) SCORE IN THE SET OF RESULTS IS 0 THEN
					 * NO MATCHES WERE FOUND RETURN A VALUE OF NULL
					 */
					if($score <= 0)
					{
						return null;
					}
					break;
				}
				return $result_score;
			}			
		}
		catch(PDOException $e)
		{
			return null;
		}
		catch(Exception $e)
		{
			return null;
		}		
	}

	function question_score($result_score, $magnet)
	{
		/*
		 * PROBLEM POINTS
		 */ 
		 
		$q_key = array("QUESTION", "PROBLEM", "SCENARIO", "CASE", "PAPER", "EXERCISE", "EXAMINATION");
		$points = 0;
		
		$conn = dbconn();
		foreach($q_key as $k)
		{
			if(strpos($magnet, $k))
			{
				$points = $points + 5;
				break;
			}
		}
		//$temp_result = array();
		
		foreach(array_keys((array)$result_score) as $id)
		{
			
			$stmt = $conn->prepare("SELECT * FROM problem WHERE upld_id=:id");
			$stmt->execute(array(":id" => $id)); 
			$row = $stmt->fetch();
			if($row == null)
			{
				// current upload id is not a problem 
				continue;
			}
			else 
			{
				// upload is a problem therefore add 5 points
				$current_score = (double)$result_score[$id];
				$new_score = $current_score + $points;
				$result_score[$id] = $new_score;			
			}
			
		}
		return $result_score;
	}
	
	function solution_score($result_score, $magnet)
	{
		/*
		 * SOLUTION POINTS
		 */ 
		$s_key = array("SOLUTION", "MEMO", "ANSWER", "PROOF", "RESPONSE", "REPLY", "FEEDBACK");
		$s_points = 0;
		
		$conn = dbconn();
		foreach($s_key as $k)
		{
			if(strpos($magnet, $k))
			{// CHECK IF SEARCH STRING(MAGNET) CONTAINS ANY OF THE WORDS IN S_KEY
				$s_points = $s_points + 5;
				break;
			}
		}
		
		foreach(array_keys((array)$result_score) as $id)
		{
			$stmt = $conn->prepare("SELECT * FROM solution WHERE upld_id=:id");
			$stmt->execute(array(":id" => $id)); 
			$row = $stmt->fetch();
			if($row == null)
			{
				// current upload id is not a solution
				continue;
			}
			else 
			{
				// upload is a solution therefore add 5 points
				$current_score = (double)$result_score[$id];
				$new_score = $current_score + $s_points;
				$result_score[$id] = $new_score;			
			}
		}	
		return $result_score;
	}
	
	function module_score($result_score, $magnet)
	{
		/*
		 * MODULE POINTS
		 */ 
		$conn = dbconn();
		
		// get all uploads and their corrosponding module
		$data = $conn->query("SELECT upload.upld_id, moduleinst.pk_mod, moduleinst.mod_code FROM moduleinst, upload WHERE upload.pk_mod = moduleinst.pk_mod;")->fetchAll();

		foreach($data as $row)
		{
			$module_code = $row["mod_code"];
			$module_id = $row["pk_mod"];
			if(strpos($magnet, $module_code) !== false)// check search string(magnet) for the module code
			{
				//add points to all uploads matching the identified module
				//$stmt1 = $conn->prepare("SELECT upload.upld_id FROM moduleinst, upload WHERE upload.pk_mod=:module;");
				$stmt1 = $conn->prepare("SELECT upload.upld_id FROM moduleinst, upload WHERE upload.pk_mod=:module AND moduleinst.pk_mod=:module;");
				$stmt1->execute(array(":module" => $module_id));
				$mod_data = $stmt1->fetchAll(); 
				
				if($mod_data != null)
				{
					foreach($mod_data as $mod_row)
					{
						$upld_key = $mod_row['upld_id'];
						$result_score[$upld_key] = (double)$result_score[$upld_key] + 15;
					}
					break; // got to next upload id
				}
			}
			
		}
		return $result_score; 
	}
	
    function part_module_score($result_score, $magnet) // if the user types accs instead of accs111
	{
		/*
		 * MODULE POINTS
		 */ 
		$conn = dbconn();
		
		// get all uploads and their corrosponding module
		$data = $conn->query("SELECT upload.upld_id, moduleinst.pk_mod, moduleinst.mod_code FROM moduleinst, upload WHERE upload.pk_mod = moduleinst.pk_mod;")->fetchAll();

		foreach($data as $row)
		{
			$module_code = $row["mod_code"];
			$module_id = $row["pk_mod"];
			if(strpos($module_code, $magnet) !== false)// check search string(magnet) for the module code
			{
				//add points to all uploads matching the identified module
				//$stmt1 = $conn->prepare("SELECT upload.upld_id FROM moduleinst, upload WHERE upload.pk_mod=:module;");
				$stmt1 = $conn->prepare("SELECT upload.upld_id FROM moduleinst, upload WHERE upload.pk_mod=:module AND moduleinst.pk_mod=:module;");
				$stmt1->execute(array(":module" => $module_id));
				$mod_data = $stmt1->fetchAll(); 
				
				if($mod_data != null)
				{
					foreach($mod_data as $mod_row)
					{
						$upld_key = $mod_row['upld_id'];
						$result_score[$upld_key] = (double)$result_score[$upld_key] + 7;
					}
					break; // got to next upload id
				}
			}
			
		}
		return $result_score; 
	}

	function year_score($result_score, $magnet)
	{
		/*
		 * YEAR POINTS (ONLY FOR PROBLEMS)
		 */
		 $conn = dbconn();
		 
		 $magnet = preg_replace('/[^A-Za-z0-9\-]/', ' ', $magnet); // removes all special characters and replaces them with " "
		 $magnet_arr = explode(' ', $magnet); // split search string into word array
		 
		 foreach($magnet_arr as $search_word) // loop through every word in search string
		 {
		 	$search_word = trim($search_word); //remove white space
		
		 	if(strval($search_word) == strval(intval($search_word))) // check if integer value
			{
				$year = (int)$search_word; 
		 		$stmt = $conn->prepare("SELECT upld_id, prob_year FROM problem WHERE prob_year=:year;");
				$stmt->execute(array(":year" => $year));
				$data = $stmt->fetchAll(); 
								
				if($data != null)
				{
					foreach($data as $row)
					{
						$upld_key = $row['upld_id'];
						$result_score[$upld_key] = (double)$result_score[$upld_key] + 3;
					} 				
				}
			}
		 }
		 return $result_score;
	}	
	
	function description_score($result_score, $magnet)
	{
		/*
		 * DESCRIPTION POINTS (SOLUTION)
		 */
		 $conn = dbconn();
		 
		 $magnet = preg_replace('/[^A-Za-z0-9\-]/', ' ', $magnet); // removes all special characters and replaces them with " "
		 $magnet_arr = explode(' ', $magnet); // split search string into word array
		 
		 foreach($magnet_arr as $search_word)
		 {
		 	if($search_word != " " && $search_word != "") // checks for empty strings
			{
				$search_word = trim($search_word);
				$data = $conn->query("SELECT description, upld_id FROM solution")->fetchAll();
				
				foreach($data as $row)
				{
					$description = strtoupper($row['description']);
					$upload_id = $row['upld_id'];
					if(strpos($description, $search_word) !== false)
					{
						$result_score[$upload_id] = (double)$result_score[$upload_id] + 2;
					}
				}
			}
		 }
		 return $result_score;
	}
	
	function subject_score($result_score, $magnet)
	{
		/*
		 * SUBJECT POINTS (PROBLEM)
		 */
		 $conn = dbconn();
		 
		 $magnet = preg_replace('/[^A-Za-z0-9\-]/', ' ', $magnet); // removes all special characters and replaces them with " "
		 $magnet_arr = explode(' ', $magnet); // split search string into word array
		 
		 foreach($magnet_arr as $search_word)
		 {
		 	if($search_word != " " && $search_word != "") // checks for empty strings so no points are gained from matching empty space
			{
				$search_word = trim($search_word);
				$data = $conn->query("SELECT prob_subject, upld_id FROM problem")->fetchAll();
				
				foreach($data as $row)
				{
					$description = strtoupper($row['prob_subject']);
					$upload_id = $row['upld_id'];
					if(strpos($description, $search_word) !== false)
					{
						$result_score[$upload_id] = (double)$result_score[$upload_id] + 2;
					}
				}
			}
		 }
		 return $result_score;		
	}
	
	function meta_score($result_score, $magnet)
	{
		/*
		 * META SCORE
		 */
		 $conn = dbconn();
		 
		 $magnet = preg_replace('/[^A-Za-z0-9\-]/', ' ', $magnet); // removes all special characters and replaces them with " "
		 $magnet_arr = explode(' ', $magnet); // split search string into word array
		 
		 foreach($magnet_arr as $search_word)
		 {
		 	if($search_word != " " && $search_word != "")// checks for empty strings so no points are gained from matching empty space
			{
				$search_word = trim($search_word);
				$data = $conn->query("SELECT upld_id, upld_meta FROM upload")->fetchAll();
				
				foreach($data as $row)
				{
					$meta = strtoupper($row['upld_meta']);
					$upld_id = $row['upld_id'];
					if(strpos($meta, $search_word) !== false)
					{
						$result_score[$upld_id] = (double)$result_score[$upld_id] + 1;
					}
				}
			}	
		 }
		 return $result_score;
	}

	function modulename_score($result_score, $magnet)
	{
		/*
		 * MODULE NAME SCORE
		 * Checks for key word matches in the module name associated with an upload
		 */
		 $conn = dbconn();
		 
		 $magnet = preg_replace('/[^A-Za-z0-9\-]/', ' ', $magnet); // removes all special characters and replaces them with " "
		 $magnet_arr = explode(' ', $magnet); // split search string into word array
		 
		 foreach($magnet_arr as $search_word)
		 {
		 	if($search_word != " " && $search_word != "")// checks for empty strings so no points are gained from matching empty space
			{
				$search_word = trim($search_word);
				$data = $conn->query("SELECT upload.upld_id, moduleinst.mod_name FROM upload, moduleinst WHERE moduleinst.pk_mod = upload.pk_mod;")->fetchAll();
				
				foreach($data as $row)
				{
					$modulename = strtoupper($row['mod_name']);
					$upld_id = $row['upld_id'];
					if(strpos($modulename, $search_word) !== false)
					{
						$result_score[$upld_id] = (double)$result_score[$upld_id] + 2;
					}
				}
			}
		}	
		return $result_score;	
	}	
?>