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
	
	
	if(isset($_GET['id'])) {
	$institute_id = decrypt($_GET['id']);
	
	// pr($agent_id);
	
	$sql = "SELECT * FROM institute WHERE id='{$institute_id}' ";
	$db->query($sql);
	$record = $db->fetch();
	// pr($record);
	}
	
	
	
	if( isset($_POST['submit']) ) {
			
		// pr($_FILES);
		// pr($_POST); 
		// di();
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
				   
					@unlink('../inst_logo/thumb/'.$old_image);
					@unlink('../inst_logo/'.$old_image);
					
					$logo = $new_name;
				} else {
					$logo = $old_image;
					
				}
				
				
				
				$sql = "SELECT * FROM institute WHERE LCASE(institute_name)='".addslashes(strtolower($institute))."' AND country='$country' AND website='$website' AND id!='".$institute_id."' AND user_type='AD' ";
				$db->query($sql);
				$record_inst_exist = $db->fetch();
				// echo $db->last_query();
				// pr($record_inst_exist); di(); 
				if(count($record_inst_exist)==0){
					
				 					
				
				$password = "un".rand();
				$password_enc = encrypt($password); 	
				
				$data = array(	
							'country'=>$country,
							'institute_name'=>$institute,
							'campus'=>$campus,
							'contact_person'=>$contact_person,
							'email'=>$email,
							'password'=>$password_enc,
							'contact_no'=>$contact_no,
							'website'=>$website,
							'logo'=>$logo,
							'added_by'=>$_SESSION['login']['id'],
							'user_type'=>'AD',
							'update_date'=>'now()'
							
							);
					$data = $objAgent->sanitize_data($data);
					$db->where('id',$institute_id);
					$db->update('institute',$data);
					
						
					
					
					
					
				$_SESSION['error']['msg'] = '<font color="green">'.$institute.'  is updated successfully!</font>';
				
				echo "<script>window.location.href='institution_edit.php?id=".urlencode($_GET['id'])."';</script>";
				exit;
					
				
				
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
	<div class="form-sub-head">View Institution 
	 <a href="institution_view.php" ><i class="fa  fa-hand-o-up"></i> View  Institution </a> 
	</div>
	
	<?php if( isset($_SESSION['error']['msg']) ) { ?>
		<div id="error_msg" style="padding: 10px 0;" ><font color="red"><?php echo $_SESSION['error']['msg']; ?> </font></div>
	<?php unset($_SESSION['error']['msg']); } ?>
	
	
	<fieldset>
	<legend>Institution Details</legend>
	<ul class="fields">
	
	<li><span class="lable">Country <font color="red" >*</font> </span>
		<?php echo $objAgent->inst_reprsented_country('country',$record[0]['country'],' required1 ',$_SESSION['login']['id']); ?>
	</li>
	
	
	
	
	<li><span class="lable">Institution Name <font color="red" >*</font> </span><input type="text" name="institute" id="institute" value="<?php echo $record[0]['institute_name']; ?>" placeholder="Enter institute name" required1 ></li>
	
	<li><span class="lable">Campus</span><input type="text" name="campus" id="campus" value="<?php echo $record[0]['campus']; ?>" placeholder="Campus" ></li>
	
	
	<li><span class="lable">Website</span><input type="text" name="website" id="website"  value="<?php echo $record[0]['website']; ?>" placeholder="Website" required1></li>
	
	<li >
	<span class="lable">Institution Logo</span> <span id="browse_file_id">
	<input type="file" name="image" placeholder="Institution Logo"> 
	
	<input type="hidden" name="old_image" value="<?php echo $record[0]['logo']; ?>"> 
	
	</span>

	<span id="img_logo">
	<?php 
				
		// echo $_SESSION['login']['agentLogo'];
		if(isset( $record[0]['logo']) AND  $record[0]['logo']!=''){
		
		if(file_exists('../inst_logo/thumb/'. $record[0]['logo'])){
			echo '<img src="../inst_logo/thumb/'. $record[0]['logo'].'" width="100">';
		
		} else { echo '<img src="../images/no_image_available.png" width="100">'; }
		} else { echo '<img src="../images/no_image_available.png" width="100">'; } 
	  
	?> </span></li>
	
	</ul>
	</fieldset>
	
	<fieldset>
	<legend>Personal Details</legend>
	<ul class="fields">
	
	
	
	
	<li><span class="lable">International Contact Person</span><input type="text" name="contact_person" id="contact_person" value="<?php echo $record[0]['contact_person']; ?>" placeholder="International Contact Person" required1 ></li>
	
	<li><span class="lable">Email</span><i class="fa fa-envelope icon"></i><input type="email" name="email" id="email" value="<?php echo $record[0]['email']; ?>" placeholder="Email" required ></li>
	
	
	
	<li><span class="lable">User Id</span><i class="fa fa-envelope icon"></i><input type="email" name="user_id" id="user_id" value="<?php echo $record[0]['user_id']; ?>" placeholder="Enter Email as user id" readonly ></li>
	
	<li><span class="lable">Contact No</span><i class="fa fa-phone icon"></i><input type="text" name="contact_no" id="contact_no" value="<?php echo $record[0]['contact_no']; ?>" class="field-icon" placeholder="Contact No" ></li>
	
	
	
	
	
	
	
	</ul>
	</fieldset>
	
	</fieldset><ul style="decoration:none;"><li class="full" style="text-align:center;list-style:none;"><button name="submit"><i class="fa fa-save"></i> Update</button></li>
	</ul></fieldset>
	</div>
	</form>
	<!-- view branch office form -->

	</div>
	<!-- right-panel -->
	
	<script>
		set_left_menu('submenu_view_inst','submenu_inst','button_member');
	</script>

<?php include('../includes/agent-footer.php'); ?>