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
	
	
	if(isset($_GET['id']) ){
	
		$institute_id = $_GET['id'];
		
		$db->where(array('id'=>$institute_id));
		$db->from('representing_institute');
		$record = $db->fetch();
		
		// pr($record);
		
	
	} else {
	
		echo "<script>window.location.href=view-representing-institution.php';</script>";
	}
	
	if( isset($_POST['submit']) ) {
			
		// pr($_FILES);
		// pr($_POST);
		// pr($_SESSION); 
		
		extract($_POST);
		
		if($course_title=='') {
		 
			$_SESSION[error]['msg'] = '<font color="red">Please enter course title!</font>';
			
		}  else if ($course_level=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please select the course level!</font>';
		} else if ($campus_location=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please enter the campus location!</font>';
		} else if ($course_fee=='') {
				$_SESSION[error]['msg'] = '<font color="red">Please enter the course fee!</font>';
		} else {
					
				// pr($_FILES);
				if(isset($_FILES['additional_info_doc_1']['name']) AND $_FILES['additional_info_doc_1']['name']!='') {
					
					require_once("../class/ImageResize.php");
				
				
				   $ext               = end((explode(".", $_FILES['additional_info_doc_1']['name'])));  
				   	
				   $new_name      = strtolower($record[0]['institute_name'])."_1"."agent_id_".$_SESSION[login]['id']."_date_".date("d_m_Y").".".$ext;			   
				   $upload_dir_main   = "../course_doc/".$new_name;
				   // $upload_dir_thumbs = "../inst_logo/thumb/".$new_name;
				   move_uploaded_file($_FILES["additional_info_doc_1"]["tmp_name"],$upload_dir_main);
				   
				   // $image = new SimpleImage();
				   // $image->load($upload_dir_main);
				   // $image->resizeToWidth(200);
				   // $image->save($upload_dir_thumbs);
				   // unlink("../inst_logo/".$old_logo);
				   // unlink("../inst_logo/thumb/".$old_logo);
				   
					$additional_info_doc_1 = $new_name;
				} 
				
				if(isset($_FILES['additional_info_doc_2']['name']) AND $_FILES['additional_info_doc_2']['name']!='') {
					
					require_once("../class/ImageResize.php");
				
				
				   $ext               = end((explode(".", $_FILES['additional_info_doc_2']['name'])));  
				   	
				   $new_name      = strtolower($record[0]['institute_name'])."_2"."agent_id_".$_SESSION[login]['id']."_date_".date("d_m_Y").".".$ext;			   
				   $upload_dir_main   = "../course_doc/".$new_name;
				   // $upload_dir_thumbs = "../inst_logo/thumb/".$new_name;
				   move_uploaded_file($_FILES["additional_info_doc_2"]["tmp_name"],$upload_dir_main);
				   
				   // $image = new SimpleImage();
				   // $image->load($upload_dir_main);
				   // $image->resizeToWidth(200);
				   // $image->save($upload_dir_thumbs);
				   // unlink("../inst_logo/".$old_logo);
				   // unlink("../inst_logo/thumb/".$old_logo);
				   
					$additional_info_doc_2 = $new_name;
				} 
				
				if(isset($_FILES['additional_info_doc_3']['name']) AND $_FILES['additional_info_doc_3']['name']!='') {
					
					require_once("../class/ImageResize.php");
				
				
				   $ext               = end((explode(".", $_FILES['additional_info_doc_3']['name'])));  
				   	
				   $new_name      = strtolower($record[0]['institute_name'])."_3"."agent_id_".$_SESSION[login]['id']."_date_".date("d_m_Y").".".$ext;			   
				   $upload_dir_main   = "../course_doc/".$new_name;
				   // $upload_dir_thumbs = "../inst_logo/thumb/".$new_name;
				   move_uploaded_file($_FILES["additional_info_doc_3"]["tmp_name"],$upload_dir_main);
				   
				   // $image = new SimpleImage();
				   // $image->load($upload_dir_main);
				   // $image->resizeToWidth(200);
				   // $image->save($upload_dir_thumbs);
				   // unlink("../inst_logo/".$old_logo);
				   // unlink("../inst_logo/thumb/".$old_logo);
				   
					$additional_info_doc_3 = $new_name;
				}
				
				if(isset($_FILES['additional_info_doc_4']['name']) AND $_FILES['additional_info_doc_4']['name']!='') {
					
					require_once("../class/ImageResize.php");
				
				
				   $ext               = end((explode(".", $_FILES['additional_info_doc_4']['name'])));  
				   	
				   $new_name      = strtolower($record[0]['institute_name'])."_4"."agent_id_".$_SESSION[login]['id']."_date_".date("d_m_Y").".".$ext;			   
				   $upload_dir_main   = "../course_doc/".$new_name;
				   // $upload_dir_thumbs = "../inst_logo/thumb/".$new_name;
				   move_uploaded_file($_FILES["additional_info_doc_4"]["tmp_name"],$upload_dir_main);
				   
				   // $image = new SimpleImage();
				   // $image->load($upload_dir_main);
				   // $image->resizeToWidth(200);
				   // $image->save($upload_dir_thumbs);
				   // unlink("../inst_logo/".$old_logo);
				   // unlink("../inst_logo/thumb/".$old_logo);
				   
					$additional_info_doc_4 = $new_name;
				}
				
				if(isset($_FILES['additional_info_doc_5']['name']) AND $_FILES['additional_info_doc_5']['name']!='') {
					
					require_once("../class/ImageResize.php");
				
				
				   $ext               = end((explode(".", $_FILES['additional_info_doc_5']['name'])));  
				   	
				   $new_name      = strtolower($record[0]['institute_name'])."_5"."agent_id_".$_SESSION[login]['id']."_date_".date("d_m_Y").".".$ext;			   
				   $upload_dir_main   = "../course_doc/".$new_name;
				   // $upload_dir_thumbs = "../inst_logo/thumb/".$new_name;
				   move_uploaded_file($_FILES["additional_info_doc_5"]["tmp_name"],$upload_dir_main);
				   
				   // $image = new SimpleImage();
				   // $image->load($upload_dir_main);
				   // $image->resizeToWidth(200);
				   // $image->save($upload_dir_thumbs);
				   // unlink("../inst_logo/".$old_logo);
				   // unlink("../inst_logo/thumb/".$old_logo);
				   
					$additional_info_doc_5 = $new_name;
				}
					
				$data = array(	
							
							'agent_id'=>$_SESSION['login']['id'],
							'representing_institute_id'=>$institute_id,
							'course_title'=>$course_title,
							'course_level'=>$course_level,
							'course_month'=>$course_month,
							'course_duration'=>$course_duration,
							'course_week'=>$course_week,
							'campus_location'=>$campus_location,
							'awarding_body'=>$awarding_body,
							'course_fee'=>$course_fee,
							'general_eligibility'=>$general_eligibility,
							'languages'=>$languages,
							'additional_info_doc_1'=>$additional_info_doc_1,
							'additional_info_doc_2'=>$additional_info_doc_2,
							'additional_info_doc_3'=>$additional_info_doc_3,
							'additional_info_doc_4'=>$additional_info_doc_4,
							'additional_info_doc_5'=>$additional_info_doc_5,
							'title_doc_1'=>$title_doc_1,
							'title_doc_2'=>$title_doc_2,
							'title_doc_3'=>$title_doc_3,
							'title_doc_4'=>$title_doc_4,
							'title_doc_5'=>$title_doc_5,
							'title_doc_5'=>$title_doc_5,
							'add_date'=>'now()',
							'update_date'=>'now()'
							);
							
			
				// $db->where(array('id'=>$institute_id));
				$course_id = $db->insert('course',$data);
				// echo $db->last_query();
					
				// echo $course_id ; 
				
				foreach($module as $key=>$value){
					
					$value_module = trim($value);
					
					if($value_module!='') {
					
						$data = array(	
									
									'agent_id'=>$_SESSION[login]['id'],
									'course_id'=>$course_id,
									'key_module'=>$value_module,
									'add_date'=>'now()',
									'update_date'=>'now()'
									);
					
						$db->insert('course_key_module',$data);
					}
				}
				
				
				
				$_SESSION[error]['msg'] = '<font color="green">Record is successfully added!</font>';
				
				
				
				echo "<script>window.location.href='institution_course_add.php?id=$institute_id';</script>";
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
	<div class="form-sub-head">Add Course of <?php echo $record[0]['institute_name']; ?> <a href="institution_course_view.php?id=<?php echo $institute_id; ?>" ><i class="fa  fa-hand-o-up"></i> View Courses </a></div>
	
	<?php if( isset($_SESSION[error]['msg']) ) { ?>
		<div id="error_msg" style="padding: 10px 0;" ><font color="red"><?php echo $_SESSION[error]['msg']; ?> </font></div>
	<?php unset($_SESSION[error]['msg']); } ?>
	
	
	<fieldset>
	<legend>Course Details</legend>
	<ul class="fields">
	
	
	<li><span class="lable">Course Title <font color="red"> * </font></span><input type="text" name="course_title" id="course_title" value="<?php echo $course_title; ?>" placeholder="Course Title" required></li>
	
	
	<li><span class="lable">Course Level <font color="red"> * </font> </span><?php echo $objAgent->course_level('course_level',$course_level,' required',$_SESSION['login']['id']); ?></li>
	
	<li><span class="lable">Course Duration <font color="red"> * </font> </span>
	
	<!--
	<input type="text" name="course_duration" id="course_duration" value="<?php echo $course_duration; ?>" placeholder="Course Duration" required>
	-->
	
	<?php echo $objCommon->course_year('course_duration',$course_duration,' style="width:175px;"'); ?>
	<?php echo $objCommon->year_month('course_month',$course_month,' style="width:175px;"'); ?>
	<?php echo $objCommon->course_week('course_week',$course_week,' style="width:175px;"'); ?>
	</li>
	
	<li><span class="lable">Campus
	 Location <font color="red"> * </font> </span><input type="text" name="campus_location" id="campus_location" value="<?php echo $campus_location; ?>" placeholder="Campus Location" required></li>
	
	<li><span class="lable">Awarding Body</span><input type="text" name="awarding_body" id="awarding_body" value="<?php echo $awarding_body; ?>" placeholder="Awarding Body" required1 ></li>
	
	<li><span class="lable">Course Fee <font color="red"> * </font> </span></i><input type="text" name="course_fee" id="course_fee" value="<?php echo $course_fee; ?>" class="" placeholder="Course Fee" required></li>
	
	<li><span class="lable"> General Eligibility </span>
		
		<textarea name="general_eligibility" id="general_eligibility"><?php echo $general_eligibility; ?></textarea>
		
	
	</li>
	
	<li><span class="lable"> Languages Requirement </span>
	
	
	<textarea name="languages" id="languages"><?php echo $languages; ?></textarea>
	</li>
	</ul>
	</fieldset>
	
	<fieldset>
	<legend>Additional Information</legend>
	<ul class="fields">
			
			<li><span class="lable"> Document 1 Description </span><input type="text" name="title_doc_1" id="title_doc_1"  value="<?php echo $title_doc_1; ?>" placeholder="Document 1 Description" required1></li>
	
			<li><span class="lable">&nbsp;</span>
				<input type="file" name="additional_info_doc_1" placeholder="">
				
			</li>
			
			<li><span class="lable"> Document 2 Description </span><input type="text" name="title_doc_2" id="title_doc_2"  value="<?php echo $title_doc_2; ?>" placeholder="Document 2 Description" required1></li>
	
			<li><span class="lable">&nbsp;</span>
				<input type="file" name="additional_info_doc_2" placeholder="">
				
			</li>
			
			<li><span class="lable"> Document 3 Description </span><input type="text" name="title_doc_3" id="title_doc_3"  value="<?php echo $title_doc_3; ?>" placeholder="Document 3 Description" required1></li>
	
			<li><span class="lable">&nbsp;</span>
				<input type="file" name="additional_info_doc_3" placeholder="">
				
			</li>
			
			<li><span class="lable"> Document 4 Description </span><input type="text" name="title_doc_4" id="title_doc_4"  value="<?php echo $title_doc_4; ?>" placeholder="Document 4 Description" required1></li>
	
			<li><span class="lable">&nbsp;</span>
				<input type="file" name="additional_info_doc_4" placeholder="">
				
			</li>
			
			<li><span class="lable"> Document 5 Description </span><input type="text" name="title_doc_5" id="title_doc_5"  value="<?php echo $title_doc_5; ?>" placeholder="Document 5 Description" required1></li>
	
			<li><span class="lable">&nbsp;</span>
				<input type="file" name="additional_info_doc_5" placeholder="">
				
			</li>
	</ul>
	</fieldset>
	
	<fieldset>
	<legend>Key Modules</legend>
	<ul class="fields">
	
		<li class="full "><span class="lable inline-lable"> Module 1 </span><input name="module[]" type="text" value="" class="inline-textbox" ></li>
		
		
		<div id="more_text_div"> </div>
		
		<input type="hidden" name="status_cnt" id="status_cnt" value="1">
		<li class="full" style="text-align:right;" id="add_more_button"><span class="lable"><a href="javascript:void(0);" class="ad-steps" onclick="add_more_text();"><i class="fa fa-plus-circle"></i> Click here to add more</a></span></li>
	
	</ul>
	</fieldset>
	
	
	<div style="text-align:center;" ><button name="submit" style="text-align:center;"><i class="fa fa-save"></i> Submit</button></div>
	</div>
	</form>
	<!-- view branch office form -->

	</div>
	<!-- right-panel -->
	
	<script>
		function add_more_text(){
		
			var status_cnt = $("#status_cnt").val();
			status_cnt = parseInt(status_cnt)+1;
			$("#status_cnt").val(status_cnt);
			
			if(status_cnt<16){
			
				$("#more_text_div").append('<li class="full"><span class="lable inline-lable">Module '+status_cnt+'</span><input type="text" name="module[]" class="inline-textbox check_required"></li>');
				
				if(status_cnt==15)
				$("#add_more_button").hide();
				
			}
			
			
			
			$(".left-panel").height($(".right-panel").height());
		}

	</script>	

<?php include('../includes/agent-footer.php'); ?>