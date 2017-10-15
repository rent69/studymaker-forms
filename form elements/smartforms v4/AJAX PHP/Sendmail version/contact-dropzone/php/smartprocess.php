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
	$sendersubject = strip_tags(trim($_POST["sendersubject"]));
	$sendermessage = strip_tags(trim($_POST["sendermessage"]));
    $captcha = strtoupper(strip_tags(trim($_POST["captcha"])));
	
/*	----------------------------------------------------------------------
	: Prepare form field variables for CSV export
	----------------------------------------------------------------------- */	
	if($generateCSV == true){
		$csvFile = $csvFileName;	
		$csvData = array(
			"$sendername",
			"$emailaddress",
			"$sendersubject"
		);
	}

/*	-------------------------------------------------------------------------
	: Prepare serverside validation 
	------------------------------------------------------------------------- */ 
	$errors = array();
	
	//validate files	
	foreach($_FILES["file"]["name"] as $key => $fileOrigName){
		$fileTempName = $_FILES["file"]["tmp_name"][$key];
		$fileOrigName = $_FILES["file"]["name"][$key];
		$fileUploSize = $_FILES["file"]["size"][$key];
		$fileValError = $_FILES["file"]["error"][$key];
		
		//$file_upload_info	= finfo_open(FILEINFO_MIME_TYPE);
		//$file_valid_type	= finfo_file($file_upload_info, $fileTempName);
		$file_valid_type 	= strrchr($fileOrigName, ".");
		$file_new_name		= strtolower($fileOrigName);
		$file_new_list		= $file_unique_id.'-'.$file_new_name;
		$file_list[] 		= $file_new_list;		
		
		if($fileValError === UPLOAD_ERR_NO_FILE){
			$errors[] = "No file was uploaded";
		}  elseif($fileValError === UPLOAD_ERR_INI_SIZE){
			$errors[] = "Upload exceeds maximum size in php.ini";
		} elseif($fileValError === UPLOAD_ERR_NO_TMP_DIR){
			$errors[] = "Missing a temporary folder";
		} elseif($fileValError === UPLOAD_ERR_CANT_WRITE){
			$errors[] = "Failed to write file to disk";
		} elseif(!in_array($file_valid_type, $file_extensions)){
			$errors[] = "Upload image files only";
		} elseif($fileUploSize > $file_upload_size){
			$errors[] = "File too big - maximum size is 2MB";
		}else {										
			$moveuploads = move_uploaded_file($fileTempName,$file_folder.$file_unique_id.'-'.$file_new_name.'');
		}
	}
	
	if ($file_list[0]!=""){
		$file_list_list = implode( '<br/>', $file_list);
	}	
		
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
	
	//validate subject
	if(isset($_POST["sendersubject"])){
			if (!$sendersubject) {
				$errors[] = "You must enter a subject.";
			} elseif(strlen($sendersubject) < 4)  {
				$errors[] = "Subject must be at least 4 characters.";
			}
	}
	
	//validate message / comment
	if(isset($_POST["sendermessage"])){
		if (strlen($sendermessage) < 10) {
			if (!$sendermessage) {
				$errors[] = "You must enter a message.";
			} else {
				$errors[] = "Message must be at least 10 characters.";
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
	
		if($moveuploads === true){
			
			include dirname(__FILE__).'/phpmailer/PHPMailerAutoload.php';
			include dirname(__FILE__).'/templates/smartmessage.php';
				
			$mail = new PHPMailer();
			$mail->isSendmail();
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
			foreach($_FILES["file"]["name"] as $key => $fileOrigName){
				$mail->addAttachment($file_folder.$file_unique_id.'-'.$fileOrigName.'');
			}		
					
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
								"Subject"									
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
						$automail->isSendmail();
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
					
					//Redirect to set URL
					if($redirectForm == true){
						echo '<script>setTimeout(function () { window.location.replace("'.$redirectForm_url.'") }, 8000); </script>';
					}
					
					//Remove files from server
					if($removeFilesUploaded == true){
						$files = glob('../smuploads/*'); 
						foreach($files as $file){ 
						  if(is_file($file))
							unlink($file); 
						}
					}
							
					echo '<div class="alert notification alert-success">Message has been sent successfully!</div>';
										
				} else {
					  echo '<div class="alert notification alert-error">Message not sent - server error occured!</div>';	
				}
		}				
	}
?>