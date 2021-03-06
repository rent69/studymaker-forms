<?php 

	if (!isset($_SESSION)) session_start(); 
	if(!$_POST) exit;
	
	include dirname(__FILE__).'/settings/settings.php';
	include dirname(__FILE__).'/functions/emailValidation.php';
	
	
	/* Current Date Year
	------------------------------- */		
	$currYear = date("Y");	
	
/*	---------------------------------------------------------------------------
	: Register all form field variables here
	--------------------------------------------------------------------------- */
	$guestname = strip_tags(trim($_POST["guestname"]));	
	$emailaddress = strip_tags(trim($_POST["emailaddress"]));
	$telephone = strip_tags(trim($_POST["telephone"]));
	$adults = strip_tags(trim($_POST["adults"]));
    $children = strip_tags(trim($_POST["children"]));
	$checkin = strip_tags(trim($_POST["checkin"]));
	$checkout = strip_tags(trim($_POST["checkout"]));
	$comment = strip_tags(trim($_POST["comment"]));
	$captcha = strtoupper(strip_tags(trim($_POST["captcha"])));
	
/*	----------------------------------------------------------------------
	: Prepare form field variables for CSV export
	----------------------------------------------------------------------- */	
	if($generateCSV == true){
		$csvFile = $csvFileName;	
		$csvData = array(
			"$guestname",
			"$emailaddress",
			"$telephone",
			"$adults",
			"$children",
			"$checkin",
			"$checkout"			
		);
	}

/*	-------------------------------------------------------------------------
	: Prepare serverside validation 
	------------------------------------------------------------------------- */
	
	$errors = array();
	 //validate name
	if(isset($_POST["guestname"])){
	 
			if (!$guestname) {
				$errors[] = "You must enter a name.";
			} elseif(strlen($guestname) < 2)  {
				$errors[] = "Name must be at least 2 characters.";
			}
	 
	}
	//validate email address
	if(isset($_POST["emailaddress"])){
		if (!$emailaddress) {
			$errors[] = "You must enter an email.";
		} else if (!validEmail($emailaddress)) {
			$errors[] = "Your must enter a valid email.";
		}
	}
		
	//validate adults
	if(isset($_POST["adults"])){
			if (!$adults) {
				$errors[] = "You must enter the number of adults.";
			} elseif(!preg_match('/^[0-9]{0,15}$/', $adults))  {
				$errors[] = "Please enter numeric values only for adults";
			}
	}
	
	//validate children
	if(isset($_POST["children"])){
			if (!$children) {
				$errors[] = "You must enter the number of children.";
			} elseif(!preg_match('/^[0-9]{0,15}$/', $children))  {
				$errors[] = "Please enter numeric values only for children";
			}
	}
	
	//validate checkin date
	if(isset($_POST["checkin"])){
			if (!$checkin) {
				$errors[] = "You must enter a checkin date.";
			}
	}
	
	//validate checkout date
	if(isset($_POST["checkout"])){
			if (!$checkout) {
				$errors[] = "You must enter a checkout date.";
			}
	}		
	
	//validate message / comment
	if(isset($_POST["comment"])){
		if (strlen($comment) < 10) {
			if (!$comment) {
				$errors[] = "You must enter a comment or message.";
			} else {
				$errors[] = "Comment must be at least 10 characters.";
			}
		}
	}
	
	// validate security captcha 
	if(isset($_POST["captcha"])){
		if (!$captcha) {
			$errors[] = "You must enter the captcha code";
		} else if (($captcha) != $_SESSION['gfm_captcha']) {
			$errors[] = "Captcha code is incorrect";
		}
	}
		
	if ($errors) {
		//Output errors in a list
		$errortext = "";
		foreach ($errors as $error) {
			$errortext .= '<li>'. $error . "</li>";
		}
	
		echo '<div class="alert notification alert-error">The following errors occured:<br><ul>'. $errortext .'</ul></div>';
	
	} else{	
	
		include dirname(__FILE__).'/phpmailer/PHPMailerAutoload.php';
		include dirname(__FILE__).'/templates/smartmessage.php';
			
		$mail = new PHPMailer();	
		$mail->isSMTP();                                      
		$mail->Host = $SMTP_host;                    
		$mail->SMTPAuth = true;                              
		$mail->Username = $SMTP_username;               
		$mail->Password = $SMTP_password;               
		$mail->SMTPSecure = $SMTP_protocol;                            
		$mail->Port = $SMTP_port;
		$mail->IsHTML(true);
		$mail->setFrom($emailaddress,$guestname);
		$mail->CharSet = "UTF-8";
		$mail->Encoding = "base64";
		$mail->Timeout = 200;
		$mail->ContentType = "text/html";
		$mail->addAddress($receiver_email, $receiver_name);
		$mail->Subject = $receiver_subject;
		$mail->Body = $message;
		$mail->AltBody = "Use an HTML compatible email client";
				
		// For multiple email recepients from the form 
		// Simply change recepients from false to true
		// Then enter the recipients email addresses
		// echo $message;	
		$recipients = false;
		if($recipients == true){
			$recipients = array(
				"address@example.com" => "Recipient Name",
				"address@example.com" => "Recipient Name"
			);
			
			foreach($recipients as $email => $name){
				$mail->AddBCC($email, $name);
			}	
		}
		
		if($mail->Send()) {
			/*	-----------------------------------------------------------------
				: Generate the CSV file and post values if its true
				----------------------------------------------------------------- */		
				if($generateCSV == true){	
					if (file_exists($csvFile)) {
						$csvFileData = fopen($csvFile, 'a');
						fputcsv($csvFileData, $csvData );
					} else {
						$csvFileData = fopen($csvFile, 'a'); 
						$headerRowFields = array(
							"Guest Name",
							"Email Address",
							"Telephone",
							"Adults",
							"Children",
							"Checkin Date",
							"Checkout Date"										
						);
						fputcsv($csvFileData,$headerRowFields);
						fputcsv($csvFileData, $csvData );
					}
					fclose($csvFileData);
				}	
				
			/*	---------------------------------------------------------------------
				: Send the auto responder message if its true
				--------------------------------------------------------------------- */
				if($autoResponder == true){
				
					include dirname(__FILE__).'/templates/autoresponder.php';
					
					$automail = new PHPMailer();	
					$automail->isSMTP();                                      
					$automail->Host = $SMTP_host;                    
					$automail->SMTPAuth = true;                              
					$automail->Username = $SMTP_username;               
					$automail->Password = $SMTP_password;               
					$automail->SMTPSecure = $SMTP_protocol;                            
					$automail->Port = $SMTP_port;
					$automail->setFrom($receiver_email,$receiver_name);
					$automail->isHTML(true);                                 
					$automail->CharSet = "UTF-8";
					$automail->Encoding = "base64";
					$automail->Timeout = 200;
					$automail->ContentType = "text/html";
					$automail->AddAddress($emailaddress, $guestname);
					$automail->Subject = "Thank you for contacting us";
					$automail->Body = $automessage;
					$automail->AltBody = "Use an HTML compatible email client";
					$automail->Send();	 
				}
				
				if($redirectForm == true){
					echo '<script>setTimeout(function () { window.location.replace("'.$redirectForm_url.'") }, 8000); </script>';
				}
							
			  	echo '<div class="alert notification alert-success">Message has been sent successfully!</div>';
			} 
			else {
				echo '<div class="alert notification alert-error">Message not sent - server error occured!</div>';	
			}
	}
?>