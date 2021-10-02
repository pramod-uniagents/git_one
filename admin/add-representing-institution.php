<?php 

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	
	
	require_once("../class/classDb.php");
	$db = new Database();
	require_once("../class/agentClass.php");
	$objAgent = new agent();
	
	require_once("../class/admin.php");
	$objAdmin = new admin();
	
	// To check agent is login or not
	$objAdmin->check_admin_login();
	
	if( isset($_POST['submit']) ) {
			
		// pr($_FILES);
		// pr($_POST);
		// pr($_SESSION); 
		
		extract($_POST);
		
		if($country=='') {
		 
			$_SESSION[error]['msg'] = '<font color="red">Please select country!</font>';
			
		}  else if ($institute_name=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please enter the institute name!</font>';
		} else if ($contact_person=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please enter the contact person!</font>';
		} else if ($email=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please enter the email!</font>';
		} else if ($contact_no=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please enter the contact number!</font>';
		} else if ($website=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please enter the website!</font>';
		} else {
					
				// pr($_FILES);
				if(isset($_FILES['image']['name'])) {

					require_once("../class/ImageResize.php");
				
				
				   $ext               = end((explode(".", $_FILES['image']['name'])));  
				   	
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
				
				
					
				$data = array(							
							'country'=>$country,
							'institute_name'=>$institute_name,
							'campus'=>$campus,
							'contact_person'=>$contact_person,
							'email'=>$email,
							'contact_no'=>$contact_no,
							'website'=>$website,
							'logo'=>$logo,
							'added_by'=>$_SESSION[login]['id'],
							'user_type'=>'A',
							'status'=>'1',
							'add_date'=>'now()'							
							);
			
				$db->insert('institute',$data);
					
				
				$_SESSION[error]['msg'] = '<font color="green">Record is successfully added!</font>';
				
				
				
				echo "<script>window.location.href='add-representing-institution.php';</script>";
				exit;
				
			
			
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
	<div class="form-sub-head">Add Representing Institution <a href="view-representing-institution.php" onclick="set_left_menu('submenu_view_institution','submenu_institution','button_member');"><i class="fa  fa-hand-o-up"></i> View Representing Institution </a></div>
	
	<?php if( isset($_SESSION[error]['msg']) ) { ?>
		<div id="error_msg" style="padding: 10px 0;" ><font color="red"><?php echo $_SESSION[error]['msg']; ?> </font></div>
	<?php unset($_SESSION[error]['msg']); } ?>
	
	
	<fieldset>
	<legend>Institution Details</legend>
	<ul class="fields">
	
	<li><span class="lable">Country</span>
		<?php echo $objAgent->inst_reprsented_country('country',$country,' required',$_SESSION['login']['id']); ?>
	</li>
	
	<li><span class="lable">Institution Name</span><input type="text" name="institute_name" id="institute_name" value="<?php echo $institute_name; ?>" placeholder="Institution Name" required></li>
	
	<li><span class="lable">Campus</span><input type="text" name="campus" id="campus" value="<?php echo $campus; ?>" placeholder="Campus" ></li>
	
	<li><span class="lable">International Contact Person</span><input type="text" name="contact_person" id="contact_person" value="<?php echo $contact_person; ?>" placeholder="International Contact Person" required></li>
	
	<li><span class="lable">Email</span><i class="fa fa-envelope icon"></i><input type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email" required></li>
	
	<li><span class="lable">Contact No</span><i class="fa fa-phone icon"></i><input type="text" name="contact_no" id="contact_no" value="<?php echo $contact_no; ?>" class="field-icon" placeholder="Contact No" required></li>
	
	<li><span class="lable">Website</span><input type="text" name="website" id="website"  value="<?php echo $website; ?>" placeholder="Website" required></li>
	
	<li><span class="lable">Institution Logo</span><input type="file" name="image" placeholder="Institution Logo"> </li>
	
	<li class="full" style="text-align:center;"><button name="submit"><i class="fa fa-save"></i> Save</button></li>
	</ul>
	</fieldset>
	</div>
	</form>
	<!-- view branch office form -->

	</div>
	<!-- right-panel -->


<?php include('../includes/agent-footer.php'); ?>