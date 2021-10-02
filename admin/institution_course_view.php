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
	
	extract($_POST);
	
	if( isset($search) ){
				
		$_SESSION['search']['course_level'] = $course_level;
		$_SESSION['search']['course_title'] = $course_title;
		$_SESSION['search']['campus_location'] = $campus_location;
		$_SESSION['search']['keyword'] = $keyword;
	
	}
	
	// pr($_SESSION['search']);
?>

	<?php include('../includes/admin-header.php'); ?>
	<?php include('../includes/banner.php'); ?>
	<?php include('../includes/admin-left-panel.php'); ?>


	<!-- right-panel -->
	<div class="right-panel column">
	
		<?php include('../includes/admin_login_section.php'); ?>
	
	
	<!-- view branch office form -->
	<div class="form-container">
	<div class="form-sub-head">View Courses of <?php echo $record[0]['institute_name']; ?> <a href="institution_course_add.php?id=<?php echo $institute_id; ?>" onclick="set_left_menu('submenu_add_institution','submenu_institution','button_member');" ><i class="fa  fa-hand-o-up"></i> Add New Course </a></div>
	<form method="post" action="?id=<?php echo $institute_id; ?>">
	<fieldset class="search-box">
	<ul class="fields">
	<li><span class="lable"> Course Level </span>
	
	<?php echo $objAgent->course_level("course_level",$_SESSION['search']['course_level']," required1",$_SESSION['login']['id']); ?>
	
	
		
	</li>
	
	<li><span class="lable"> Course name </span><input type="text" name="course_title"  placeholder="Course name" value="<?php echo $_SESSION['search']['course_title']; ?>"></li>
	
	<li><span class="lable">Course Campus</span><input type="text" name="campus_location" placeholder="Course location" value="<?php echo $_SESSION['search']['campus_location']; ?>"></li>
	
	<li><span class="lable"> Keyword </span><input type="text" name="keyword" placeholder="Keyword" value="<?php echo $_SESSION['search']['keyword']; ?>"></li>
	
	<li style="text-align:right;"><button name="search"><i class="fa fa-search"></i> View</button></li>
	</ul>
	</fieldset>
	
	</form>
	

	<!-- listing -->
	
	<?php 
				
				// pr($record); LEFT JOIN lead_assigned ON leads.id=lead_assigned.lead_assigned_id
				
				
				$sql = "SELECT * FROM course  ";
				$where = " WHERE ( course.representing_institute_id='{$institute_id}' )";
				
				
				// condition for listing 
		
				if(  isset($_SESSION['search']['course_level']) AND $_SESSION['search']['course_level']!='' ){
				
					$where .= " AND course_level='{$_SESSION['search']['course_level']}'";
				
				}
				
				
				
				if( isset($_SESSION['search']['course_title']) AND $_SESSION['search']['course_title']!='' ){
				
					$where .= " AND (course_title LIKE '%{$_SESSION['search']['course_title']}%')";
				}
				
				if( isset($_SESSION['search']['campus_location']) AND $_SESSION['search']['campus_location']!='' ){
				
				
					$where .= " AND (campus_location LIKE '%{$_SESSION['search']['campus_location']}%')";
				}
				
				
				
				if( isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword']!='' ){
				
					$where .= " AND ( course_title LIKE '%{$_SESSION['search']['keyword']}%' OR general_eligibility LIKE '%{$_SESSION['search']['keyword']}%' OR awarding_body LIKE '%{$_SESSION['search']['keyword']}%'  OR languages LIKE '%{$_SESSION['search']['keyword']}%' OR course_fee LIKE '%{$_SESSION['search']['keyword']}%' )";
				}
				
				$sql .= $where;
				$db->query($sql);
				$record = $db->fetch();
				
				$total_record = count($record);
				
				
				$limit_per_page = 1;
				$limit_per_page = 50;
				if(isset($_GET['page']) && !empty($_GET['page']))
					$page_no = $_GET['page'];
				else 
					$page_no = 1;
					
				$start_limit = ($page_no-1)*$limit_per_page;
				
				$sql .= " order by id desc ";
				$sql .= " LIMIT $start_limit,  $limit_per_page";
				$db->query($sql);
				$record = $db->fetch();
				// echo $db->last_query();
		?>
	
	<div class="table-head-left"><i class="fa fa-institution"></i> Total Representing Institution : <span><?php echo $total_record; ?></span></div>
	
	<?php if($total_record!=0) { ?>
	<div class="table-head-left" align="right">Displaying From <span><?php echo $start_limit+1; ?></span></div>
	<?php } ?>
	<?php 
	
		// pr($record);
		
		if(count($record)>0) {
		foreach($record as $key=>$value){
		
		$contry_detail = $objAgent->country_detail('country_id',$value['country']);
		
		// pr($contry_detail);
	?>
	
	<div class="blue-head">
	
	
	<i class="fa fa-arrow-right " style="font-size:17px;"></i> &nbsp;
	<?php echo $value['course_title']; ?>
	
	</div>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		
		<th width="200">Course Duration</th>
		<th width="150">Course Level</th>
		<th width="100">Course Fee</th>
		
		<th width="100">Campus</th>
		<th>Course Eligibility</th>
		<th width="50">Status</th>
		
		<th width="50" class="center">Action</th>
	  </tr>
	  <tr>
		
		<td> <?php 
		$duration_txt = '';
		if(!empty($value['course_duration']))
		$duration_txt .= $value['course_duration']. ' Year ';
		if(!empty($value['course_month']))
		$duration_txt .= $value['course_month']. ' Month ';
		if(!empty($value['course_week']))
		$duration_txt .= $value['course_week']. ' Week ';
		
		echo $duration_txt; 
		
		
		?> </td>
		<td> <?php echo $objAgent->course_level[$value['course_level']]; ?> </td>
		<td> <?php echo $value['course_fee']; ?> </td>
		<td> <?php echo $value['campus_location']; ?> </td>
		<td><?php echo $value['general_eligibility']; ?>  </td>
		
		<td>
		
		
			
			<div class="chk_box_click" id="div_parent_chk_<?php echo $value['id']; ?>" style="display:inline-block;"  table_id="<?php echo $value['id']; ?>" status="<?php echo $value['status']; ?>">
							
			<input type="checkbox" class="js-switch" <?php if($value['status']=='Y'){ echo 'checked="checked"'; } ?>  id="chk_box_id_<?php echo $value['id']; ?>"> 
			
			</div>
		
		</td>
		
		
		
		
		<td class="center">
		
		
		<a href="institution_course_edit.php?id=<?php echo $value['id']; ?>&institute_id=<?php echo $institute_id; ?>" title="Edit"><i class="fa fa-edit"></i></a>
		
		
		<!--
		<a href="institution_course_view.php?id=<?php echo $value['id']; ?>" title="View Course"><i class="fa fa-eye "></i></a>
		-->
		</td>
	  </tr>
	</table>
	
	<?php }	} else  { echo '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td colspan="5"><font color="red">No record is present</font></td></tr></table>'; }?>
	
	
	
	

	<!-- listing -->
	<?php echo $objCommon->getPaginationLinks( $page_no,$total_record,$limit_per_page,"?id=$institute_id" ); ?>
	</div>
	<!-- view branch office form -->

	</div>
	<!-- right-panel -->

	
	<script>
	
		$(document).ready(function(){
			$('.chk_box_click').click(function(){
			
			// alert($(this).attr('table_id'));
			var obj_div = $(this);
			var table_id = obj_div.attr('table_id');
			var status = obj_div.attr('status');
			
			
			
			
				// alert(table_id);
					if(status=="Y")
					status = "N";
					else
					status = "Y";
				
					// $('#loading').show();
					values = {"type":"change_common_status","table_id":table_id,"table":"course","field":"id","data":{"status":status,"update_date":"now()"}}
					 $.ajax({
						dataType: "json",
						url: "ajax.php",
						type: "post",
						data: values,
						success: function(data){
							// alert(data.content);
							if(data.content=='updated'){
								obj_div.attr('status',status);
								alert('Your status is changed!');
							
							}
						},
						error:function(){
							
							// $('#loading').hide();
						}
					});
					
				
			
			
		});
		});
		
		
		
	</script>	

<?php include('../includes/agent-footer.php'); ?>