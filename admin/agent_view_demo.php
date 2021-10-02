<?php 

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	require_once("../class/classDbDemo.php");
	$db = new Database();
	require_once("../class/agentClass.php");
	$objAgent = new agent();
	
	require_once("../class/commonClass.php");
	$objCommon = new common();
	
	require_once("../class/admin.php");
	$objAdmin = new admin();
	
	// To check admin is login or not
	$objAdmin->check_admin_login();
	
	extract($_POST);
	
	if( isset($search) ){
		$_SESSION['search']['country'] = $country;
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
	<div class="form-sub-head">View Agent 
	<!--
	<a href="add_agent.php"  ><i class="fa  fa-hand-o-up"></i> Add Agent </a>
	-->
	</div>
	<form method="post" >
	<fieldset class="search-box">
	<ul class="fields">
	<li><span class="lable"> Country </span>
	
	<?php echo $objAgent->country('country',$_SESSION['search']['country'],' '); ?>
		
	</li>
	
	
	<li><span class="lable"> Keyword </span><input type="text" name="keyword" placeholder="Keyword" value="<?php echo $_SESSION['search']['keyword']; ?>"></li>
	
	<li style="text-align:right;"><button name="search"><i class="fa fa-search"></i> View</button></li>
	</ul>
	</fieldset>
	
	</form>
	

	<!-- listing -->
	
	<?php 
				
				// condition for pagination  count record 
				// $where['agent_id'] = $_SESSION['login']['id'];
		
				if( isset($_SESSION['search']['country']) AND $_SESSION['search']['country']!='' ){
				
					$where['country'] = $_SESSION['search']['country'];
				
				}
				
				if( isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword']!='' ){
				
					
					$or_like =  "  (username LIKE '%{$_SESSION['search']['keyword']}%' OR contactPerson LIKE '%{$_SESSION['search']['keyword']}%' OR city LIKE '%{$_SESSION['search']['keyword']}%' OR agencyName LIKE '%{$_SESSION['search']['keyword']}%' OR emailAddress LIKE '%{$_SESSION['search']['keyword']}%' OR designation LIKE '%{$_SESSION['search']['keyword']}%' OR skypeId LIKE '%{$_SESSION['search']['keyword']}%' OR state LIKE '%{$_SESSION['search']['keyword']}%' )";
				
				}
				
				
				if($or_like!='')
				$db->where($or_like);
				
				
				if(count($where)>0)
				$db->where($where);
				$db->select();
				$db->from('agent');
				$record = $db->fetch();
				// echo $db->last_query();
				
				
				$total_record = count($record);
				
				
				$limit_per_page = 1;
				$limit_per_page = 50;
				if(isset($_GET['page']) && !empty($_GET['page']))
					$page_no = $_GET['page'];
				else 
					$page_no = 1;
					
				$start_limit = ($page_no-1)*$limit_per_page;
				
				if($or_like!='')
				$db->where($or_like);
				
				if(count($where)>0)
				$db->where($where);
				
				
				$db->order_by('agentId','desc');
				
				$db->select();
				$db->from('agent');
				$db->limit($limit_per_page,$start_limit);
				$record = $db->fetch();
				
				// echo $db->last_query();
				
				// pr($record);
			
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
	
	<?php 
				
		// echo $_SESSION['login']['agentLogo'];
		if(isset( $value['agentLogo'] ) AND  $value['agentLogo']!=''){
		
		if(file_exists('../../agent-crm-demo/agent_logo/'. $value['agentLogo'])){
			echo '<img src="../../agent-crm-demo/agent_logo/'. $value['agentLogo'].'" >';
		
		} else { echo '<img src="../images/no_image_available.png" >'; }
		} else { echo '<img src="../images/no_image_available.png" >'; } 
	  
	?>
	
	<?php echo $value['agencyName']; ?>
	
	<?php
		// pr($value['email_verified']); 
		if($value['email_verified']=='N')
		echo ' | <a href="send_verify_link.php?id='.$objCommon->url_encrypt($value['agentId']).'" style="color:red;" rel="[facebox]" rev="iframe|650|400" > Send Email Verify Link </a>';
	?>
	
	</div>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<th>Contact Details (<font title="User Name"> <?php echo $value['username']; ?> </font>)</th>
		<th width="100">Country</th>
		<th width="100">Remarks</th>
		<th width="40">Status</th>
		<th width="100"><i class="fa fa-calendar-o"></i> Add Date</th>
		<th width="50" class="center">Action</th>
	  </tr>
	  <tr>
		<td> <font title="Email"><?php echo $value['emailAddress']; ?> </font> | <font title="Contact Number"><?php echo $value['contact_no']; ?></font> | <font title="Contact Person"> <?php echo $value['contactPerson']; ?> </font>| <font title="Designation" ><?php echo $value['designation']; ?> </font> </td>
		<td> <?php if($contry_detail[0]['short_name']==''){ echo '-'; } else { echo $contry_detail[0]['short_name'];} ?> </td>
        <td> <font title="<?php echo $value['remarks']; ?>"><?php echo substr($value['remarks'],0,40); ?></font> </td>
		<td>
		
		
			
			<div class="chk_box_click" id="div_parent_chk_<?php echo $value['agentId']; ?>" style="display:inline-block;"  table_id="<?php echo $value['agentId']; ?>" status="<?php echo $value['agentStatus']; ?>">
							
			<input type="checkbox" class="js-switch" <?php if($value['agentStatus']=='A'){ echo 'checked="checked"'; } ?>  id="chk_box_id_<?php echo $value['agentId']; ?>" > 
			
			</div>
		
		</td>
		
		
		<td><?php echo date("d F Y",strtotime($value['addDate'])); ?> </td>
		
		<td class="center">
		
		<a href="agent-details-demo.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>" title="View Agent Details"><i class="fa fa-eye"></i></a>
		
		&nbsp; <a href="agent-edit-details-demo.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>" title="Edit Agent Details"><i class="fa fa-edit"></i></a>
		
		</td> 
		
	  </tr>
	</table>
	
	<?php }	} else  { echo '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td colspan="5"><font color="red">No record is present</font></td></tr></table>'; }?>
	
	
	
	

	<!-- listing -->
	<?php echo $objCommon->getPaginationLinks( $page_no,$total_record,$limit_per_page ); ?>
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
					if(status=="A")
					status = "D";
					else
					status = "A";
				
					// $('#loading').show();
					values = {"type":"change_common_status","table_id":table_id,"table":"agent","field":"agentId","data":{"agentStatus":status,"LastUpdated":"now()"}}
					 $.ajax({
						dataType: "json",
						url: "ajax_demo.php",
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
		
		
		
		set_left_menu('submenu_view_agent_demo','submenu_agent','button_member');
		
		
	</script>	

<?php include('../includes/agent-footer.php'); ?>