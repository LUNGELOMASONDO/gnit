<?php
	function sendmail($to, $subject, $message)
	{
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <gsuppot@gnit.co.za>';
		
		return mail($to,$subject,$message,$headers);
	}
?>