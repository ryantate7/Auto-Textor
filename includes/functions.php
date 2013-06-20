<?php
require 'includes/class.phpmailer.php';

function confirm_query($result_set)  //confirms queries are successful
{
  if(!$result_set)
	{
		die("Database query falied: " . mysql_error());
	}	
}

function get_current_date()  //returns the current date in YYYY-MM-DD format
{
	$date = getdate();
	$current_date = "${date['year']}-{$date['mon']}-{$date['mday']}";
	return $current_date;
}

function get_data($sent = null, $date=null)  //returns all records depending on the sent variable passed
{
	global $connection;
		
		$query = 	"SELECT * "; 
		$query .=	"FROM numbers ";
	
	if($sent == 9)
	{
		$query .=	"WHERE sent = 9 ";
	}
	elseif($sent == 1)
	{
		$query .=	"WHERE sent = 1 ";
	}
	else
	{
		$query .=	"WHERE sent IS NULL ";
	}	
	
	if($date != null)
	{
		$query .=	"AND date = '{$date}' ";
	}
	
	$query .=	"ORDER BY id ASC";
	
	$data_set = mysql_query($query, $connection);
	confirm_query($data_set);
	return $data_set;
}


function check_email_address($email_address)  //checks to make sure that the number is valid as well as making sure there is a .com extension
{
	$number = substr($email_address, 0, 10);
	$ext = substr($email_address, -4);
	if(is_numeric($number) && ($ext == ".com"))
	{
		$valid_address = true;
	}
	
	else
	{
		$valid_address = false;
	}

	return $valid_address;
}

function send_messages()  //sends the messages out to the respondents
{
	

	$data_set = get_data();
	$record_count = mysql_num_rows($data_set);
	$subject = "Hi!";
	
	
	while($data = mysql_fetch_array($data_set))
	{
		$email_address = "{$data['phone']}";
		$email_address .= "@";
		$email_address .= "{$data['caddress']}";
		$body = "Hi {$data['name']}, how are you?";
		
		$status = check_email_address($email_address);
		if($status == true)
		{
			$to = "{$email_address}";
			$mail = new PHPMailer;								//downloaded the PHPMailer Class

			$mail->IsSMTP();                                   	// Set mailer to use SMTP
			$mail->Host = 'mail.nustats.com';  					// Specify server
			$mail->SMTPAuth = true;                             // Enable SMTP authentication
			$mail->Username = $user;                          // SMTP username
			$mail->Password = $password;                     // SMTP password
			$mail->SMTPSecure = 'tls';                          // Enable encryption, 'ssl' also accepted

			$mail->From = 'rwebb@nustats.com';
			$mail->FromName = 'Ryan';
			$mail->AddAddress($to);  								// Add a recipient


			$mail->WordWrap = 50;                                 	// Set word wrap to 50 characters
			$mail->IsHTML(true);                                  	// Set email format to HTML

			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $body;
			
			if(!$mail->Send()) 
			{
				update_data($data[id], $sent = false);//Figure out what I want to do in this instance
			}
			else
			{
				update_data($data[id]);
			}
		}
		else
		{
			update_data($data[id], $sent = false);
		}
	}
	return $record_count;

}

function send_confirmation_email($count)  //sends the confirmation e-mail to the project team
{
	$current_date = get_current_date();
	$subject = "Messages Sent for " . $current_date;
	
	$rs_set = get_data($sent = 1, $date = $current_date);
	$records_sent = mysql_num_rows($rs_set);
	
	$rf_set = get_data($sent = 9, $date = $current_date);
	$records_failed = mysql_num_rows($rf_set); 
	
	if($count > 0)
	{
		$body = "Messages have been sent for today!<br>";
		$body .= "Sent:{$records_sent}<br>";
		$body .= "Failed:{$records_failed}<br>";
	}
	else
	{
		$body = "No Messages to send for today!<br>";
		$body .= "Sent:{$records_sent}<br>";
		$body .= "Failed:{$records_failed}<br>";
	}
	$mail = new PHPMailer;

	$mail->IsSMTP();                                      	// Set mailer to use SMTP
	$mail->Host = 'mail.nustats.com';  						// Specify server
	$mail->SMTPAuth = true;                               	// Enable SMTP authentication
	$mail->Username = '$user';                            	// SMTP username
	$mail->Password = '$password';                         // SMTP password
	$mail->SMTPSecure = 'tls';                            	// Enable encryption, 'ssl' also accepted

	$mail->From = 'rwebb@nustats.com';
	$mail->FromName
	= 'Ryan';

	$mail->AddAddress('rwebb@nustats.com');  				// Add a recipient
	$mail->WordWrap = 50;                                 	// Set word wrap to 50 characters

	$mail->IsHTML(true);                                  	// Set email format to HTML

	$mail->Subject = $subject;
	$mail->Body    = $body;
	$mail->AltBody = $body;

	if(!$mail->Send()) 
	{
		$message = 'Message could not be sent.';
		$message .= 'Mailer Error: ' . $mail->ErrorInfo;
		exit;
	}
	
	$message = 'Message has been sent';
	
	return $message;

}



function update_data($id, $sent = true)  //updates 'sent' for records, sucessfully sent= 1 / failed=9
{
	global $connection;
	
	$current_date = get_current_date();
	
	if($sent)
	{
		$status = 1;
	}
	else
	{
		$status = 9;
	}
	
	$query = 	"UPDATE numbers
				SET	sent = '{$status}',
				date = '{$current_date}'
				WHERE id = {$id}";
	
	$result = mysql_query($query, $connection);
	confirm_query($result);

}

function redirect_to($location = NULL)
{
	if($location != NULL)
	{
		header("Location:{$location}");
		exit;
	}
}
?>
