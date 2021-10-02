<?php 

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	require_once("../class/classDb.php");
	$db = new Database();
	require_once("../class/agentClass.php");
	$objAgent = new agent();
	
	require_once("../class/commonClass.php");
	$objCommon = new common();
	
	require_once("../class/admin.php");
	$objAdmin = new admin();
	
	require_once("../class/commonClass.php");
	$objCommon = new common();
	
	// To check agent is login or not
	$objAdmin->check_admin_login();
	
	
	
	
	if( isset($_POST['submit']) ) {
			
		// pr($_FILES);
		// pr($_POST);
		// pr($_SESSION); 
		
		extract($_POST);
		
		if($country=='') {
		 
			$_SESSION['error']['msg'] = '<font color="red">Please select country!</font>';
			
		}  else if ($institute=='') {
				$_SESSION['error']['msg'] = '<font color="red">Please enter the name of institute!</font>';
		} else if ($email=='') {
				$_SESSION['error']['msg'] = '<font color="red">Please enter the email!</font>';
		} else {
					
				// pr($_FILES);
				if(isset($_FILES['image']['name']) AND $_FILES['image']['name']!='') {

					require_once("../class/ImageResize.php");
				
				
				   $ext = end((explode(".", $_FILES['image']['name'])));  
				   	
					$new_name      = $_SESSION[login]['id']."_".date("dmyHis").".".strtolower($ext);
					
				   $upload_dir_main   = "../inst_logo/".$new_name;
				   $upload_dir_thumbs = "../inst_logo/thumb/".$new_name;
				   move_uploaded_file($_FILES["image"]["tmp_name"],$upload_dir_main);
				   
				   $image = new SimpleImage();
				   $image->load($upload_dir_main);
				   $image->resizeToWidth(200);
				   $image->save($upload_dir_thumbs);
				   
					$logo = $new_name;
				}
				
				
				
				$sql = "SELECT * FROM institute WHERE LCASE(institute_name)='".addslashes(strtolower($institute))."' AND country='$country' AND website='$website' AND user_type='AD' ";
				$db->query($sql);
				$record_inst_exist = $db->fetch();
				// echo $db->last_query();
				// pr($record_inst_exist); di(); 
				if(count($record_inst_exist)==0){
					
				$sql = "SELECT * FROM institute WHERE user_id='$user_id' ";
				$db->query($sql);
				$user_inst_exist = $db->fetch();	
				
				if( is_array($user_inst_exist) AND count($user_inst_exist)>0 ) {
					
					$_SESSION['error']['msg'] = '<font color="red">'.$user_id.'  is already present. Please try another email!</font>';
					
				} else { 					
				
				$password = "un".rand();
				$password_enc = encrypt($password); 	
				
				$data = array(	
							'country'=>$country,
							'institute_name'=>$institute,
							'campus'=>$campus,
							'contact_person'=>$contact_person,
							'email'=>$email,
							'user_id'=>$user_id,
							'password'=>$password_enc,
							'contact_no'=>$contact_no,
							'website'=>$website,
							'logo'=>$logo,
							'added_by'=>$_SESSION['login']['id'],
							'user_type'=>'AD',
							'add_date'=>'now()'
							
							);
					$data = $objAgent->sanitize_data($data);
					$institute_id = $db->insert('institute',$data);
					
					
					
					
					require_once("../phpmailer/class.phpmailer.php");
					require_once("../phpmailer/mail-settings.php");
					
					
					$mail_body = '

					<table width="800" border="0" align="center" cellpadding="0" cellspacing="1" style="border:1px solid #ccc;">


					  <tr>
						<td  <td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;">Dear '.$contact_person.',  </td>


					  </tr>
					  
						<tr>
							<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"> Thanks for your registration as an Institution in UniAgents Global Access platform. </td>
						</tr> 
						
						<tr>
							<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"> Click here to login into your account: https://www.uniagents.com/agent-crm/institute-login.php  </td>
						</tr> 
						
						
						<tr>


						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"><strong>Your login details are:</strong> : </td>

						</tr>
						<tr>


						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"><strong>User Name</strong> : '.$user_id.'</td>

						</tr>


					  <tr>


						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"><strong> Password </strong> : '.$password.'</td>

					</tr>
					
					
					
					<tr>
						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"> Please login and click on Edit Profile  and update your Institution Profile, Add the courses with up to date information and Add the Terms & Conditions for Agents and Students (Commission Policies) </td>
					</tr> 
					
					<tr>
						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"> 
						<br>
						<br>
						
						Thanks and Regards 
						<br>
						UniAgents Support team
						<br>
						 www.uniagents.com 
						</td>
					</tr> 

					<tr>
						<td style="padding:8px 10px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666;"> **This Email has been generated using the Uniagents Technology Platform.  </td>
					</tr> 

					</table>';


				$mail_body.=$footer_content;
				$mail->SetFrom("info@uniagents.com","Uniagents");

				$mail->Subject = "Your user name and password";

				$mail->MsgHTML($mail_body);

				$mail->AddAddress($user_id, $institute);
					
					
				$mail->Send();	
					
					
					
					
				$_SESSION['error']['msg'] = '<font color="green">'.$institute.'  is added successfully!</font>';
				
				echo "<script>window.location.href='institution_add.php';</script>";
				exit;
					
				}
				
				} else {
			
				$_SESSION['error']['msg'] = '<font color="red">'.$institute.'  is already present!</font>';
			}
				
				
		} 	
				
	}
	
	
	
?>

	<?php include('../includes/admin-header.php'); ?>
	<?php include('../includes/banner.php'); ?>
	<?php include('../includes/admin-left-panel.php'); ?>

	<!-- right-panel -->
	<div class="right-panel column">
	
	<?php include('../includes/admin_login_section.php'); ?>
	
	
	
	<!-- view branch office form -->
	<form method="post" enctype="multipart/form-data">
	<div class="form-container">
	<div class="form-sub-head">Add Institution 
	 <a href="institution_view.php" ><i class="fa  fa-hand-o-up"></i> View  Institution </a> 
	</div>
	
	<?php if( isset($_SESSION['error']['msg']) ) { ?>
		<div id="error_msg" style="padding: 10px 0;" ><font color="red"><?php echo $_SESSION['error']['msg']; ?> </font></div>
	<?php unset($_SESSION['error']['msg']); } ?>
	
	
	<fieldset>
	<legend>Institution Details</legend>
	<ul class="fields">
	
	<li><span class="lable">Country <font color="red" >*</font> </span>
		<?php echo $objAgent->inst_reprsented_country('country',$country,' required1 ',$_SESSION['login']['id']); ?>
	</li>
	
	
	
	
	<li><span class="lable">Institution Name <font color="red" >*</font> </span><input type="text" name="institute" id="institute" value="<?php echo $institute; ?>" placeholder="Enter institute name" required1 ></li>
	
	<li><span class="lable">Campus</span><input type="text" name="campus" id="campus" value="<?php echo $campus; ?>" placeholder="Campus" ></li>
	
	
	<li><span class="lable">Website</span><input type="text" name="website" id="website"  value="<?php echo $website; ?>" placeholder="Website" required1></li>
	
	<li ><span class="lable">Institution Logo</span> <span id="browse_file_id"><input type="file" name="image" placeholder="Institution Logo"> </span><span id="img_logo"></span></li>
	
	
	</ul>
	</fieldset>
	
	<fieldset>
	<legend>Personal Details</legend>
	<ul class="fields">
	
	
	
	
	<li><span class="lable">International Contact Person</span><input type="text" name="contact_person" id="contact_person" value="<?php echo $contact_person; ?>" placeholder="International Contact Person" required1 ></li>
	
	<li><span class="lable">Email</span><i class="fa fa-envelope icon"></i><input type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email" required1  onblur="update_user();" ></li>
	
	<script>
		function update_user(){
			
			$("#user_id").val($("#email").val())
		}
	</script>
	
	<li><span class="lable">User Id</span><i class="fa fa-envelope icon"></i><input type="email" name="user_id" id="user_id" value="<?php echo $user_id; ?>" placeholder="Enter Email as user id" required1 ></li>
	
	<li><span class="lable">Contact No</span><i class="fa fa-phone icon"></i><input type="text" name="contact_no" id="contact_no" value="<?php echo $contact_no; ?>" class="field-icon" placeholder="Contact No" ></li>
	
	
	
	
	
	
	
	</ul>
	</fieldset>
	
	</fieldset><ul style="decoration:none;"><li class="full" style="text-align:center;list-style:none;"><button name="submit"><i class="fa fa-save"></i> Save</button></li>
	</ul></fieldset>
	</div>
	</form>
	<!-- view branch office form -->

	</div>
	<!-- right-panel -->
	
	<script>
		set_left_menu('submenu_add_inst','submenu_inst','button_member');
	</script>

<?php include('../includes/agent-footer.php'); ?>