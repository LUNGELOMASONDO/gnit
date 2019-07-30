<?php
	function arr_module($inst_id)
	{
		/*
		 * must return array of type Module
		 */
		try
		{
			$modules = array();
	
			$conn = dbconn();
	
			$stmt = $conn->prepare("SELECT * FROM moduleinst WHERE inst_id=:id");
			$stmt->execute(array("id" => $inst_id));
			$row = $stmt->fetchAll();
	
			foreach($row as $data)
			{
				$mod_id = $data['PK_MOD'];
				$mod_code = $data['MOD_CODE'];
				$mod_name = $data['MOD_NAME'];
				$modules[] = new Module($mod_id, $mod_code, $mod_name, $inst_id);
			}
			return $modules;
		}
		catch(PDOException $e)
		{
			$ERRCODE = "ERR022";
			err_log("$ERRCODE: $e");
			echo "Alert: Gnit system downtime. Please check announcements and try again later";
			exit();
		}
		catch(Exception $e)
		{
			$ERRCODE = "ERR054";
			err_log("$ERRCODE: $e");
			echo "Alert: Error occured. Please try again later";
			exit();
		}
	}
?>