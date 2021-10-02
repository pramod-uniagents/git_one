<?php 

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	require_once("../class/classDb.php");
	$db = new Database();
	
	require_once("../class/agentClass.php");
	$objAgent = new agent();
	
	require_once("../class/admin.php");
	$objAdmin = new admin();
	
	require_once("../class/commonClass.php");
	$objCommon = new common();
	
	// To check agent is login or not
	$objAdmin->check_admin_login();
	
	
	
	if( isset($_POST['submit']) ){
			
		// pr($_POST);
		// pr($_SESSION);
		
		extract($_POST);
		
		if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
			$_SESSION[error]['msg'] = '<font color="red">Please enter valid email!</font>';
		
		} else if($password==''){
			$_SESSION[error]['msg'] = '<font color="red">Please enter the password!</font>';
		
		} else if($_POST['name']==''){
			$_SESSION[error]['msg'] = '<font color="red">Please enter branch name!</font>';
			
		} else if($_POST['country']==''){
			$_SESSION[error]['msg'] = '<font color="red">Please select country!</font>';
			
		} else if($_POST['contact_person']==''){
			$_SESSION[error]['msg'] = '<font color="red">Please enter contact person!</font>';
			
		} else if($_POST['designation']==''){
			$_SESSION[error]['msg'] = '<font color="red">Please enter designation!</font>';
			
		} else if($_POST['mobile']==''){
			$_SESSION[error]['msg'] = '<font color="red">Please enter mobile!</font>';
			
		} else {
			
			$db->where( array('name'=>trim($name) ) );
			$db->from('branches');
			$record_branch = $db->fetch();
			
			$db->where( array('email'=>trim($email) ) );
			$db->from('branches');
			$record_email = $db->fetch();
			
			
			
			if(count($record_branch)>0) {
			
				
				$_SESSION[error]['msg'] = '<font color="red">This branch is not available!</font>';
				
			} else if(count($record_email)>0) {
				
				$_SESSION[error]['msg'] = '<font color="red">This email is not available!</font>';
				
			} else {
			
				$data = array(	
								'email'=>trim($email),
								'branch_email'=>trim($branch_email),
								'agent_id'=>$_SESSION[login]['id'],
								'password'=>encrypt($password),
								'name'=>trim($name),
								'address'=>$address,
								'city'=>$city,
								'state'=>$state,
								'country'=>$country,
								'contact_person'=>$contact_person,
								'contact_phone'=>$contact_phone,
								'designation'=>$designation,
								'phone'=>$phone,
								'mobile'=>$mobile,
								'website'=>$website,
								'skype'=>$skype,
								'status'=>'Y',
								'add_date'=>'now()',
								'update_date'=>'now()'
								);
				
				$db->insert('branches',$data);
				
				$_SESSION[error]['msg'] = '<font color="green">Your branch is successfully added!</font>';
				
				
				// For mail settings and class
				include_once("../phpmailer/class.phpmailer.php");
				include_once ('../phpmailer/mail-settings.php');
				// For mail settings and class
				
				
				$mail_body.="

				Hello $name,<br/><br/>		
			
				<table border=\"0\">
					<tr>
						<td style=\"font-size:12px;width:120px\"><b>Branch Name :</b></td>
						<td style=\"font-size:12px\">$name</td>
					</tr>
					<tr>
						<td style=\"font-size:12px;width:120px\"><b>User Name</b></td>
						<td style=\"font-size:12px\">$email</td>
					</tr>
					<tr>
						<td style=\"font-size:12px;width:120px\"><b>Password :</b></td>
						<td style=\"font-size:12px\">$password</td>
					</tr>
					
				</table>
			";
				
				
					

				$mail->SetFrom("admin@uniagents.com","Uniagents - Admin");
				$mail->AddReplyTo("admin@uniagents.com","Uniagents - Admin");

				$mail->Subject = "Your account configuration detail";
				
				$mail->MsgHTML($mail_body);	

				// $mail->AddAddress("sanjay@uniagents.com", "$name");
				$mail->AddAddress("$email", "$name");
				
				// $mail->AddBcc("rv1605@gmail.com", "{$_POST['institution']}");

				$mail->Send();
				
				// End of mail 
				
				
				
				echo "<script>window.location.href='add-branch-office.php';</script>";
				exit;
				
			}
			
		}
	
	
	}
	
?>	

<?php include('../includes/admin-header.php'); ?>
<?php include('../includes/banner.php'); ?>
<?php include('../includes/admin-left-panel.php'); ?>

<!-- For calender -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<!-- For calender -->

<!-- right-panel -->
<div class="right-panel column">
		
		<?php include('../includes/admin_login_section.php'); ?>

		<!-- add branch office form -->
		<form action="" method="post">
		<div class="form-container">
		
		<?php if( isset($_SESSION[error]['msg']) ) { ?>
		<div id="error_msg"><font color="red"><?php echo $_SESSION[error]['msg']; ?> </font></div>
		<?php unset($_SESSION[error]['msg']); } ?>
		
		<div class="form-sub-head"> Add Agents <a href="view-branch-office.php" ><i class="fa  fa-hand-o-up"></i> View Branch </a> </div>

		
		<fieldset>
		<legend>Branch Information</legend>
		<ul class="fields">
		
		<li> <span class="lable"> Branch Name <font color="red"> * </font></span>  <input name="name" id="name" type="text" value="<?php echo $name; ?>" onblur="check_branch_name();"   placeholder="Branch Name" required /></li>
		
		<li> <span class="lable"> Address </span>  <input name="address" id="address" type="text" value="<?php echo $address; ?>" placeholder="Address"/></li>
		
		<li> <span class="lable"> City </span>  <input name="city" id="city" type="text" value="<?php echo $city; ?>" placeholder="City"/></li>
		
		<li> <span class="lable"> State </span>  <input name="state" id="state" value="<?php echo $state; ?>" type="text" placeholder="State"/></li>
		
		<li> <span class="lable"> Country <font color="red"> * </font></span> 
		<?php echo $objAgent->country('country',$country, ' required'); ?>
			
		</li>
		
		<li><span class="lable"> Email Address </span>  <i class="fa fa-envelope icon"></i> <input name="branch_email" id="branch_email" type="email" value="<?php echo $branch_email; ?>" placeholder="Email Of Branch"   /></li>
		
		<li> <span class="lable"> Phone</span>  <input name="phone" id="phone" value="<?php echo $phone; ?>" type="text" placeholder="Phone Number" /></li>
		
		<li> <span class="lable"> Website </span>  <input name="website" id="website" value="<?php echo $website; ?>" type="text" placeholder="Website" /></li>
		
		</ul>
		</fieldset>
		
		<fieldset>
		<legend> Point Of Contact </legend>
		<ul class="fields">
		
		<li> <span class="lable"> Contact Person <font color="red"> * </font></span>  <input name="contact_person" id="contact_person" value="<?php echo $contact_person; ?>" type="text" placeholder="B.O. Contact Person" required /></li>
		
		<li> <span class="lable"> Designation <font color="red"> * </font></span>  <input name="designation" id="designation" value="<?php echo $designation; ?>"  type="text" placeholder="Designation" required /></li>
		
		<li> <span class="lable">  Phone </span>  <input name="contact_phone" id="contact_phone" value="<?php echo $contact_phone; ?>" type="text" placeholder="Phone Number" /></li>
		
		<li> <span class="lable"> Mobile <font color="red"> * </font></span>  <input name="mobile" id="mobile" value="<?php echo $mobile; ?>" type="text" placeholder="Mobile Number" required /></li>
		
		<li> <span class="lable"> Skype Id  </span>  <input name="skype" id="skype" value="<?php echo $skype; ?>" type="text" placeholder="Skype Id" /></li>
		
		</ul>
		</fieldset>
		
		
		<fieldset>
		<legend> Login Detail </legend>
		<ul class="fields">
		
		
		<li><span class="lable"> Email Address  (User Name)<font color="red"> * </font></span>  <i class="fa fa-envelope icon"></i> <input name="email" id="email" type="email" value="<?php echo $email; ?>" placeholder="Email Address Of Contact Person" onblur="check_email();" required /></li>
		
		<li><span class="lable"> Password <font color="red"> * </font></span>  <input name="password" id="password" type="password" value="" placeholder="Password" required/></li>
		
		</ul>
		</fieldset>
		<!--
		<fieldset>
		<legend>Branch Information</legend>
		<ul class="fields">
		
		
		<li><span class="lable"> Email Address <font color="red"> * </font></span>  <i class="fa fa-envelope icon"></i> <input name="email" id="email" type="email" value="<?php echo $email; ?>" placeholder="Email Address" onblur="check_email();" required /></li>
		
		<li><span class="lable"> Password <font color="red"> * </font></span>  <input name="password" id="password" type="password" value="" placeholder="Password" required/></li>
		
		<li> <span class="lable"> Branch Name <font color="red"> * </font></span>  <input name="name" id="name" type="text" value="<?php echo $name; ?>" onblur="check_branch_name();"   placeholder="Branch Name" required /></li>
		<li> <span class="lable"> Address <font color="red"> * </font></span>  <input name="address" id="address" type="text" value="<?php echo $address; ?>" placeholder="Address"/></li>
		
		<li> <span class="lable"> City <font color="red"> * </font></span>  <input name="city" id="city" type="text" value="<?php echo $city; ?>" placeholder="City"/></li>
		<li> <span class="lable"> State <font color="red"> * </font></span>  <input name="state" id="state" value="<?php echo $state; ?>" type="text" placeholder="State"/></li>
		
		<li> <span class="lable"> Country <font color="red"> * </font></span> 
		<?php echo $objAgent->country('country',$country, ' required'); ?>
			
		</li>
		
		<li> <span class="lable"> Owner / Director <font color="red"> * </font></span>  <input name="owner" id="owner" value="<?php echo $owner; ?>" type="text" placeholder="Owner / Director"/></li>
		
		<li> <span class="lable"> Established Year  <font color="red"> * </font></span>  <input name="established_year" id="established_year" type="text" value="<?php echo $established_year; ?>" placeholder="Established Year" /></li>
		
		 <script>
				$( "#established_year" ).datepicker({ dateFormat: "yy-mm-dd" , changeYear: true });
		</script>
		
		<li> <span class="lable"> Contact Person <font color="red"> * </font></span>  <input name="contact_person" id="contact_person" value="<?php echo $contact_person; ?>" type="text" placeholder="B.O. Contact Person" required /></li>
		
		<li> <span class="lable"> Designation <font color="red"> * </font></span>  <input name="designation" id="designation" value="<?php echo $designation; ?>"  type="text" placeholder="Designation" /></li>
		
		<li> <span class="lable"> Phone <font color="red"> * </font></span>  <input name="phone" id="phone" value="<?php echo $phone; ?>" type="text" placeholder="Phone Number" /></li>
		
		<li> <span class="lable"> Mobile <font color="red"> * </font></span>  <input name="mobile" id="mobile" value="<?php echo $mobile; ?>" type="text" placeholder="Mobile Number" required /></li>
		
		<li> <span class="lable"> Website <font color="red"> * </font></span>  <input name="website" id="website" value="<?php echo $website; ?>" type="text" placeholder="Website" /></li>
		
		</ul>
		</fieldset>
		
		-->
		<center><button name="submit">SUBMIT</button></center>

		</div>
		
		</form>
		<!-- add branch office form -->

</div>

<!-- right-panel -->

	<script>
			function check_email(){
				
				
				var email = $('#email').val();
				
				// alert(email);
		
				if(email!=''){
					// $('#loading').show();
					values = {"type":"agent","email":email}
					 $.ajax({
						dataType: "json",
						url: "ajax.php",
						type: "post",
						data: values,
						success: function(data){
							// alert(data.content);
							if(data.content=='exist')
							alert('This email is not available!');
						},
						error:function(){
							
							// $('#loading').hide();
						}
					});
					
				} 
		}
		
		function check_branch_name(){
				
				
				var name = $('#name').val();
				
				// alert(name);
		
				if(name!=''){
					// $('#loading').show();
					values = {"type":"check_branch_name","name":name}
					 $.ajax({
						dataType: "json",
						url: "ajax.php",
						type: "post",
						data: values,
						success: function(data){
							// alert(data.content);
							if(data.content=='exist')
							alert('This branch name is not available!');
						},
						error:function(){
							
							// $('#loading').hide();
						}
					});
					
				} 
		}
	</script>
	
<?php include('../includes/admin-footer.php'); ?>