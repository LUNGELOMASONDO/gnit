<?php
	use PHPMailer\PHPMailer\PHPMailer;
    require '../vendor/autoload.php';
                    
                try{
                    $mail = new PHPMailer;
                    $mail->isSMTP(); 
                    //$mail->Host = 'localhost';
            	 	$mail->SMTPDebug = 2;  
            	 	$mail->SMTPAuth = true;                             
	                $mail->SMTPSecure = 'ssl';                                 
				    $mail->Host = 'mail.gnit.co.za';
					$mail->Port = 465;      
				      
				                              
				    $mail->Username = 'gsupport@gnit.co.za';                 
				    $mail->Password = 'fbzaatnt12d$fv';                           
				    $mail->setFrom('gsupport@gnit.co.za');	
			     	$mail->addAddress("lsxmasondo@gmail.com", "Content Admin");
	                $mail->Subject = "Please verify email!";
	                $mail->isHTML(true); 
	                $mail->Body = " Please click on the link below:<br><br> ";
      
          			$mail->send();
	     
	      			echo "You have been registered! Please verify your email!";

          		}catch (Exception $e) {
				    echo "Something wrong happened! Please try again!";
				}
?>