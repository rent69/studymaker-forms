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
	$sendername = strip_tags(trim($_POST["sendername"]));	
	$emailaddress = strip_tags(trim($_POST["emailaddress"]));
	$telephone = strip_tags(trim($_POST["telephone"]));
	$senderwebsite = strip_tags(trim($_POST["senderwebsite"]));
	$services = strip_tags(trim($_POST["services"]));
	$budget = strip_tags(trim($_POST["budget"]));
	$timeframe = strip_tags(trim($_POST["timeframe"]));
	$details = strip_tags(trim($_POST["details"]));
    $captcha = strtoupper(strip_tags(trim($_POST["captcha"])));
	
/*	----------------------------------------------------------------------
	: Prepare form field variables for CSV export
	----------------------------------------------------------------------- */	
	if($generateCSV == true){
		$csvFile = $csvFileName;	
		$csvData = array(
			"$sendername",
			"$emailaddress",
			"$telephone",
			"$senderwebsite",
			"$services",
			"$budget",
			"$timeframe"			
		);
	}

/*	-------------------------------------------------------------------------
	: Prepare serverside validation 
	------------------------------------------------------------------------- */ 
	$errors = array();
	 //validate name
	if(isset($_POST["sendername"])){
	 
			if (!$sendername) {
				$errors[] = "You must enter a name.";
			} elseif(strlen($sendername) < 2)  {
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
		
	
	//validate checkin date
	if(isset($_POST["services"])){
			if (!$services) {
				$errors[] = "Please choose a service.";
			}
	}
	
	//validate checkout date
	if(isset($_POST["budget"])){
			if (!$budget) {
				$errors[] = "Pleease set your project budget";
			}
	}		
	
	//validate message / comment
	if(isset($_POST["details"])){
		if (strlen($details) < 10) {
			if (!$details) {
				$errors[] = "You must enter project details or specifics.";
			} else {
				$errors[] = "Project details must at least be 10 characters.";
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
			$mail->setFrom($emailaddress,$sendername);
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
					"address@example.com" => "Recipient Name",
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
							"Name",
							"Email Address",
							"Telephone",
							"Website",
							"Services",
							"Budget",
							"Time Frame"										
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
					$automail->AddAddress($emailaddress, $sendername);
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