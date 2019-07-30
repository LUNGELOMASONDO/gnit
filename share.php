<?php
	use PHPMailer\PHPMailer\PHPMailer;
    require '../vendor/autoload.php';
    
    require 'objects/log_error.php';
	require 'objects/db_connect.php';
	require 'objects/valid_filetype.php';
	
	if(isset($_SESSION['content-administrator']))
	{
		header('Location:content-administrator.php');
		exit();
	}
	
	$val_alert = null;
	
	$module = "";
    $year = 0;
    $subject = "";
    $probsol = "";
    $text_att = "";
    $file = null;
    $textcontent = "";
    $meta = "";
	$recipientgroup_arr = null;
	
	if(isset($_POST))
	{	
	    if(isset($_POST['uploadtext_opp']))
        {
            $text_att = $_POST['uploadtext_opp'];
            if($text_att == "Attach file")
            {
                if(isset($_FILES['fileup']))
                {
                    $file = $_FILES['fileup'];
					if($file['size'] > 5000000)
					{
						$val_alert = "Please select a file of max 5mb\nValid file extensions/types:\n.pdf\n.docx\n.png\n.tiff\n.txt";	
					}
					elseif(!in_array($file['type'], valid_file_types()))
					{
						$val_alert = "Please select a file of max 5mb\nValid file extensions/types:\n.pdf\n.docx\n.png\n.tiff\n.txt";
					}
                }
                else
                {
                    $val_alert = "Please select a file of max 5mb\nValid file extensions/types:\n.pdf\n.docx\n.png\n.tiff\n.txt";
                }
            }
            elseif($text_att == "Text")
            {
                if(isset($_POST['editordata']))
                {
                    $textcontent = $_POST['editordata'];
                    if(strlen($textcontent) < 15)
                    {
                        $val_alert = "Please provide a valid solution/problem";
                    }
                }
            }
            else
            {
                $val_alert = "Select Attach file or Text option";
            }
        }
        
        if(isset($_POST['myModule']))
        {
            $module = $_POST['myModule'];
            if(strlen($module) < 3 || strlen($module) > 9)
            {
                $val_alert = "Please provide a valid module code";
            }
        }
        else
        {
            $val_alert = "Please provide a valid module code";
        }
        
        if(isset($_POST['year']))
        {
            $year = $_POST['year'];
            if(is_numeric($year))
            {
                $year = (int)$_POST['year'];
				$upperlimit = (int)date('Y') + 1;
                if($year < 2000 || $year > $upperlimit)
                {
                    $val_alert = "Please provide the year this content was compiled";
                }
            }
			else
			{
				$val_alert = "Please provide the year this content was compiled";
			}
        }
        else
        {
             $val_alert = "Please provide the year this content was compiled";
        }
        
        if(isset($_POST['mySub']))
        {
            $subject = $_POST['mySub'];
            if(strlen($subject) < 4 || strlen($subject) > 50)
            {
                $val_alert = "Please provide a description e.g. Examination Opp 1 2012 or Semester test 2 Question 3 Derivatives etc.";
            }
        }
        else
        {
             $val_alert = "Please provide a description e.g. Examination Opp 1 2012 or Semester test 2 Question 3 Derivatives etc.";
        }
        
        if(isset($_POST['probsol']))
        {
            $probsol = $_POST['probsol'];
            if($probsol != "Problem" && $probsol != "Solution")
            {
                $val_alert = "Is this a Problem or a Solution?";
            }
        }
        else
        {
            $val_alert = "Is this a Problem or a Solution?";
        }
        
        if(isset($_POST['meta']))
        {
            $meta = $_POST['meta'];
            if(strlen($meta) < 8)
            {
                $val_alert = "Please provide more descriptive key words(phrases) about the material you're sharing.";
            }
        }
        else
        {
            $val_alert = "Please provide more descriptive key words(phrases) about the material you're sharing.";
        }  
        
        if(isset($_POST['categorylist']))   
		{
			$recipientgroup_arr_str = $_POST['categorylist'];
			if($recipientgroup_arr_str == "")
			{
				$val_alert = "Please add recipient(s)";
			}
			else
			{
				$recipientgroup_arr = explode('^', $recipientgroup_arr_str);
				if(sizeof($recipientgroup_arr) == 0)
				{
					$val_alert = "Please add recipient(s)";
				}
			}	
		} 
		else
		{
			$val_alert = "Please add recipient(s)";
		}
	}
	else 
	{
		header('Location:index.php');
		exit();
	}
	
	if(isset($val_alert))
	{
		echo $val_alert;
		exit();
	}
	else
	{
		/*
		 * ALL INPUTS ARE VALID. SEND EMAIL TO CONTENT ADMINS
		 */
		try
		{
			$emailbody = "<table style='width:100%'>
  								<tr>
    								<th>Problem/Solution</th>
    								<td>" . $probsol . "</td>
  								</tr>
  								<tr>
    								<th>Module code</th>
    								<td>" . $module . "</td>
  								</tr>
  								<tr>
    								<th>Year of resource</th>
    								<td>" . $year . "</td>
  								</tr>
  								<tr>
  									<th>Description (Category)</th>
  									<td>" . $subject . "</td>
  								</tr>
  								<tr>
  									<th>Search keywords(phrases)</th>
  									<td>" . $meta . "</td>
  								</tr>
  								<tr>
  									<th>Type</th>
  									<td>" . $text_att . "</td>
  								</tr>";
			
			$conn = dbconn();
			$mail = new PHPMailer;
			$mail->isSMTP(); 
			
			$mail->SMTPDebug = 0;  
            $mail->SMTPAuth = true;                             
	        $mail->SMTPSecure = 'ssl';                                 
			$mail->Host = 'mail.gnit.co.za';
			$mail->Port = 465;     
			
			$mail->Username = 'gsupport@gnit.co.za';                 
			$mail->Password = 'fbzaatnt12d$fv';      
			$mail->setFrom('gsupport@gnit.co.za', 'gnit');
			
			$mail->isHTML(true);  
			$mail->Subject = "STUDENT SUBMISSION";
			$mail->AltBody = "View on HTML enabled device";
			/*
			 * CONTENT ADMINS TO SEND MATERIAL TO
			 */
			foreach ($recipientgroup_arr as $recipientgroup) 
			{
				$stmt = $conn->prepare("SELECT administrator.email FROM administrator, content_admin WHERE administrator.capacity=:capacity AND administrator.adminid=content_admin.adminid AND accept_input=TRUE;");
				$stmt->execute(array(":capacity" => $recipientgroup)); 
				$row = $stmt->fetchAll();
				
				if($row != null)
				{
					foreach($row as $data)
					{
						$email = $data['email'];
						$mail->addAddress($email, 'Gnit Administrator');
					}
					/*
					* RECORD SHARE IN DATABASE
					*/
					$stmt = $conn->prepare("INSERT INTO share (capacity, modulecode) VALUES (:capacity, :modulecode);");
    		    	$stmt->bindParam(':capacity', $recipientgroup);
    		    	$stmt->bindParam(':modulecode', $module);
				    $stmt->execute();
				}
			}
			if(isset($file))
			{
				$file_tmp = $file['tmp_name'];	
				$filename = $subject . '_' . $year . '_' . str_replace(" ","_", $file['name']);
				$mail->addAttachment($file_tmp, $filename);
			}
			elseif(strlen($textcontent) != 0)
			{
				$emailbody .= "<tr>
							   		<th>The ". $probsol ."</th>
							   		<td>
							   			<blockquote>
							   				" . $textcontent . "				 
							   			</blockquote>
							   		</td>
							   </tr>";
			}
			
			$emailbody .= "</table>";
			$mail->Body = $emailbody;
			
			$mail->send();
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			exit();
		}
		
		echo "Thank you for sharing :-)";
		exit();
	}
	
?>