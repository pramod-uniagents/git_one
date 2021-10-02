<?php 

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	require_once("../class/classDb.php");
	$db = new Database();
	require_once("../class/agentClass.php");
	$objAgent = new agent();
	
	require_once("../class/branchClass.php");
	$objBranch = new branch();
	
	require_once("../class/commonClass.php");
	$objCommon = new common();
	
	require_once("../class/admin.php");
	$objAdmin = new admin();
	
	// To check admin is login or not
	$objAdmin->check_admin_login();
	
	
	if(isset($_GET['id']) ){
	
		$agent_id = decrypt($_GET['id']);
		
		$sql = "SELECT * FROM agent WHERE agentId='{$agent_id}' ";
		$db->query($sql);
		$record = $db->fetch();
		// pr($record); di();
	} 
	
	
	
	
	
	if( isset($_POST['submit']) ) {
			
		// pr($_FILES);
		// pr($_POST);
		// pr($_SESSION); 
		
		extract($_POST);
		
				$rdr_content = urlencode(encrypt("UCRM00".$agent_id));
				
				require_once("../phpmailer/class.phpmailer.php");

				require_once("../phpmailer/mail-settings.php");



				$mail_body = '



					<table width="800" border="0" align="center" cellpadding="0" cellspacing="1" style="border:1px solid #ccc;">

					  <tr>

						<td  style="padding:10px;background:#666;color:#fff;font-size:16px;font-family:Arial, Helvetica, sans-serif;text-align:center;">Registration Confirmation Mail </td>

					  </tr>

					   <tr>

						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;">Dear '.$record[0]['contactPerson'].',	 </td>

					  </tr>

					  <tr>

						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;">Thanks for registering with us. <br /><br />

This email has been sent automatically by Uniagents in response to your request to registration with Uniagents Agent CRM. This is done for your protection; only you, the recipient of this email can take the next step to verify your email and activate your account.	


To access your account and proceed to payment, either click on the button or copy and paste the following link into the address bar of your browser:
 </td>

					  </tr>

					  <tr>

						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"><strong><a href="'.HTTP_HOST.'/email-verified.php?rdr='.$rdr_content.'">Click here to verify your email</a></strong><br /><br />'.HTTP_HOST.'/email-verified.php?rdr='.$rdr_content.'</td>

					  </tr>
  <tr>

						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"><p>We are pleased  to welcome you on board and assist you further in configuring your account. </p>
						  
						  <p>Warm Regards,  <br />
						    Support Team  Uniagents   <br />
  <a href="http://www.uniagents.com">www.uniagents.com</a></p></td>

					  </tr>

					</table>
					';



				$mail->SetFrom("agentcrm@uniagents.com","UniAgents Agent CRM");

				// $mail->AddReplyTo("ucie@unicasolutions.com","Professor David Faulkner");
				$mail->Subject = "Email verification for UniAgents Agent CRM";

				
				
				$mail->MsgHTML($mail_body);

				// echo $mail_body;

				//$mail->AddAddress("sanjay@uniagents.com", "$receiver_name");

				$mail->AddAddress($record[0]['username'], "");
				if( !empty($another_email) )
				$mail->AddAddress($another_email, "");
				
				$mail->send();				
			
				$_SESSION[error]['msg'] = '<font color="green">Email Verify Link is successfully sent to  '.$record[0]['username'].' and '.$another_email.'</font>';
	
	}
	
	
?>

	<?php include('../includes/facebox_header.php'); ?>

		<script>
		
		// parent.refresh('view-representing-country.php?contry_id=<?php echo $record_country[0]['country_id']; ?>');
		
		$(document).ready(function(){
		$('.lable strong').css({'font-size':'16px','color':'#50a732'});
		$('.spacer').css({'height':'20px'});
		var h = $('.popup-container').height();
		if(h > 350){
			$('.popup-container').css({'max-height':'350px','overflow-y':'scroll'});
		}
		else{ $('.popup-container').removeAttr('style'); }
		});

		//resize function
		
		$('.popup-container').resize(function(){
		var h = $('.popup-container').height();
		if(h > 350){
		}
		else{ $('.popup-container').removeAttr('style'); }
		});
		</script>
        <style>
		.new-popup{width:97%;margin:10px auto;min-height:350px;}
		</style>
		<form action="" enctype="multipart/form-data" method="post">
        <div class="head-blue">Send Verify Link To <?php echo $record[0]['agencyName']; ?></div>
		<div class="popup-container new-popup">
		<div class="head"> Add New Email To Send The Verify Link </div>
		<?php 
		
		
			if(isset($_SESSION[error]['msg'])) {
				echo $_SESSION[error]['msg'];
				echo '<div class="spacer"></div>';
				unset($_SESSION[error]['msg']);
			
			}
		?>
		<div class="fields-container">

		<div class="lable"> Email  </div>



		<input type="text" name="another_email" id="another_email" value="<?php echo $another_email; ?>" placeholder="Another Email If you want to send verify link" required1>

		
		<input type="submit" value="Send" name="submit">


		</div>
		<div class="spacer"></div>





		</div>
		</form>

		
		






