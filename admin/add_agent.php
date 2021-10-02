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
		
		if ((!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$emailAddress)) AND ($emailAddress!='') ) {
			$_SESSION[error]['msg'] = '<font color="red">Please enter valid email!</font>';
		
		} else if($password==''){
			$_SESSION[error]['msg'] = '<font color="red">Please enter the password!</font>';
		
		} else if($_POST['username']==''){
			$_SESSION[error]['msg'] = '<font color="red">Please enter user name!</font>';
			
		}  else {
			
			$db->where( array('username'=>trim($username) ) );
			$db->from('agent');
			$record_agent = $db->fetch();
			
		
			
			
			
			if(count($record_agent)>0) {
			
				
				$_SESSION[error]['msg'] = '<font color="red">This agent is not available!</font>';
				
			}  else {
			
				$random_no = mt_rand(100,9999);
			
			if(isset($_FILES['image']['name']) AND $_FILES['image']['name']!='') {

					$ext  = end((explode(".", $_FILES['image']['name'])));  
				   	
				   $new_name      = $random_no."_agentid_"."_date_".date("d_m_Y").".".$ext;			   
				   $upload_dir_main   = "../agent_logo/".$new_name;
				   $upload_dir_thumbs = "../agent_logo/".$new_name;
				   chmod("../agent_logo",0777);
				   move_uploaded_file($_FILES["image"]["tmp_name"],$upload_dir_main);
				   
				   
				   require_once("../class/ImageResize.php");
				   $image = new SimpleImage();
				   $image->load($upload_dir_main);
				   $image->resizeToWidth(200);
				   $image->save($upload_dir_thumbs);
				   
					$logo = $new_name;
					@unlink("../agent_logo/".$record[0]['agentLogo']);
				} else {
				
					$logo = $record[0]['agentLogo'];
				}
				
				
				$data = array(	
								'emailAddress'=>trim($emailAddress),
								'username'=>$username,
								'password'=>encrypt($password),
								'contact_email'=>$contact_email,
								'secondryEmail'=>$secondryEmail,
								'agencyName'=>$agencyName,
								'address'=>$address,
								'area'=>$area,
								'city'=>$city,
								'state'=>$state,
								'country'=>$country,
								'pinCode'=>$pinCode,
								'owner'=>$owner,
								'website'=>$website,
								'contactPerson'=>$contactPerson,
								'designation'=>$designation,
								'directPhoneNumber'=>$directPhoneNumber,
								'phoneNumber'=>$phoneNumber,
								'mobile'=>$mobile,
								'website'=>$website,
								'skypeId'=>$skypeId,
								'facebookUrl'=>$facebookUrl,
								'linkdinUrl'=>$linkdinUrl,
								'twitterUrl'=>$twitterUrl,
								'googlePlusUrl'=>$googlePlusUrl,
								'agentLogo'=>$logo,
								'addDate'=>'now()',
								'LastUpdated'=>'now()'
								);
				
				$db->insert('agent',$data);
				
				$_SESSION[error]['msg'] = '<font color="green">Agent is successfully added!</font>';
				
				
				
				echo "<script>window.location.href='add_agent.php';</script>";
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
		<form action="" method="post" enctype="multipart/form-data">
		<div class="form-container">
		
		<?php if( isset($_SESSION[error]['msg']) ) { ?>
		<div id="error_msg"><font color="red"><?php echo $_SESSION[error]['msg']; ?> </font></div>
		<?php unset($_SESSION[error]['msg']); } ?>
		
		<div class="form-sub-head"> Add Agent  <!-- <a href="view-branch-office.php" ><i class="fa  fa-hand-o-up"></i> View Branch </a> --> </div>

		

		<fieldset>
		<legend> Agent Information</legend>
		<ul class="fields">
		
		
		
		<li><span class="lable"> Agency Name </span> <input name="agencyName" id="agencyName" type="text" value="<?php echo $agencyName; ?>" placeholder="Agency Name" required1 /> </li>
		
		
		<li> <span class="lable"> Address </span><input name="address" id="address" type="text" value="<?php echo $address; ?>" placeholder="Address"/> </li>
		
		
		<li><span class="lable">City</span><input name="city" id="city" type="text" value="<?php echo $address; ?>" placeholder="City"/></li>
		
		<li><span class="lable">State</span><input name="state" id="state" value="<?php echo $state; ?>" type="text" placeholder="State"/></li>
		
		<li><span class="lable">Country  </span>
			
			<?php 
				// echo $record[0]['country'];
				echo $objAgent->country('country',$country, ' required1'); 
			?>
			
		</li>
		
		<li><span class="lable"> Zipcode </span><input name="pinCode" id="pinCode" value="<?php echo $pinCode; ?>" type="text" placeholder="Pincode"/></li>
		
		
		
		
		<li><span class="lable">Phone Number</span><input name="phoneNumber" id="phoneNumber" value="<?php echo $phoneNumber; ?>" type="text" placeholder="Phone Number" /></li>
		
		<li><span class="lable">Email Address </span><i class="fa fa-envelope icon"></i> <input name="emailAddress" id="emailAddress" type="email" value="<?php echo $emailAddress; ?>" placeholder="Email"  /></li>
		
		<li><span class="lable">Website</span><input name="website" id="website" value="<?php echo $website; ?>" type="text" placeholder="Website" /></li>
		
		</ul>
		</fieldset>
		
		
		<fieldset>
		<legend> Point of contact </legend>
		<ul class="fields">
		
		<li><span class="lable"> Contact Person  </span><input name="contactPerson" id="contactPerson" value="<?php echo $contactPerson; ?>" type="text" placeholder="Contact Person" required1 /></li>
		
		<li><span class="lable"> Designation  </span>  <input name="designation" id="designation" value="<?php echo $designation; ?>"  type="text" placeholder="Designation" required1 /></li>
		
		<li><span class="lable"> Email </span> <i class="fa fa-envelope icon"></i> <input name="contact_email" id="contact_email" value="<?php echo $contact_email; ?>"  type="email" placeholder="Email of Contact Person" required1 /></li>
		
		<li><span class="lable"> Phone Number </span><input name="directPhoneNumber" id="directPhoneNumber" value="<?php echo $directPhoneNumber; ?>" type="text" placeholder="Direct Phone Number" /></li>
		
		<li><span class="lable">Mobile Number  </span><input name="mobile" id="mobile" value="<?php echo $mobile; ?>" type="text" placeholder="Mobile Number" required1 /></li>
		
		<li><span class="lable">Skype Id</span><input name="skypeId" id="skypeId" value="<?php echo $skypeId; ?>" type="text" placeholder="Skype Id" /></li>
		
	
		</ul>
		</fieldset>
		
		<fieldset>
		<legend> Login Detail </legend>
		<ul class="fields">
		
		
		
		
		
		
		<li>
		<span class="lable"> Username <font color="red"> * </font> </span> <input name="username" id="username" type="text" value="<?php echo $username; ?>" placeholder="Username" onblur="check_user();" required/>
		
		</li>
		
		
		<li><span class="lable"> Password <font color="red"> * </font> </span><input name="password" id="password" type="password" value="<?php echo $password; ?>" required/></li>

		
		</ul>
		</fieldset>
		
		<fieldset>
		<legend> Additional Information</legend>
		<ul class="fields">
		
		<li><span class="lable"> Facebook Url</span><input name="facebookUrl" id="facebookUrl" value="<?php echo $facebookUrl; ?>" type="text" placeholder="Facebook Url" /></li>
		
		<li><span class="lable"> LinkedIn Url</span><input name="linkdinUrl" id="linkdinUrl" value="<?php echo $linkdinUrl; ?>" type="text" placeholder="LinkedIn Url" /></li>
		
		<li><span class="lable"> Twitter Url</span><input name="twitterUrl" id="twitterUrl" value="<?php echo $twitterUrl; ?>" type="text" placeholder="Twitter Url" /></li>
		
		<li><span class="lable"> Google Url</span><input name="googlePlusUrl" id="googlePlusUrl" value="<?php echo $googlePlusUrl; ?>" type="text" placeholder="Google Url" /></li>
		
		
		<!-- <li><span class="lable"> &nbsp; </span> &nbsp; </li> -->
		
		<li><span class="lable"> Logo </span><input name="image" id="image"  type="file" />
			
			
		</li>
		
		
		
		
		
		</ul>
		</fieldset>
	
		<button name="submit"><i class="fa fa-plus"></i> Submit </button>

		</div>
		
		</form>
		<!-- add branch office form -->


</div>

<!-- right-panel -->

	<script>
	
		function check_user(){
				
				
				var username = $('#username').val();
				
				// alert(email);
		
				if(username!=''){
					// $('#loading').show();
					values = {"type":"agent","username":username}
					 $.ajax({
						dataType: "json",
						url: "ajax.php",
						type: "post",
						data: values,
						success: function(data){
							// alert(data.content);
							if(data.content=='exist')
							alert('This user is not available!');
						},
						error:function(){
							
							// $('#loading').hide();
						}
					});
					
				} 
		}
		
		
		
		$.cookie('add_active_claass','submenu_agent');
		$.cookie('submenu_id','');
		$.cookie('submenu_button_id','');
	</script>
	
<?php include('../includes/admin-footer.php'); ?>