<?php		
	require 'valid_filetype.php';
	require 'session_life.php';
	
	session_start();
	session_life();
	
	if(!isset($_SESSION))
	{
		header('Location:../admin-login.php');
		exit();
	}
	
	if(isset($_FILES['fileup']))
	{
		$file = $_FILES['fileup'];
		if(!in_array($file['type'], valid_file_types()))
		{
			echo "Alert: A file is limited to a maximum of 5mb\nvalide file types: .jpg, .tiff, .png, .docx, .pdf, .txt";
			exit();
		}
		$size = (int)$file['size'];
		if($size > 5097152)
		{
			echo "Alert: A file is limited to a maximum of 5mb\nValide file types: .jpg, .tiff, .png, .docx, .pdf, .txt";
			exit();
		}
	}
	else
	{
		echo "Alert: No file available";
		exit();
	}
	
?>