<?php 

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	require_once("../class/classDb.php");
	$db 		= new Database();
	
	require_once("../class/agentClass.php");
	$objAgent 	= new agent();
	
	require_once("../class/commonClass.php");
	$objCommon 	= new common();
	
	require_once("../class/admin.php");
	$objAdmin 	= new admin();
	
	require_once("../class/classUniagentDb.php");
	$uniagentDb = new UniagentDatabase();
	
	require_once("../class/globalClass.php");
	$globalDB 	= new globalClass();
	
	// To check agent is login or not
	$objAdmin->check_admin_login();

	extract($_POST);
	
	if(isset($search))  {
		
		$_SESSION['search']['country'] 			= $country;
		
		$_SESSION['search']['keyword'] 			= $keyword;
	
	}
	
	if(isset($_REQUEST['reset'])) {
		
		$status = $_SESSION['search']['status'];

		unset($_SESSION['search']);
		
		echo '<script>window.location.href="agent_view_dashboard_details.php?status='.$status.' "</script>';
		exit();
	  
    }
	
	$sql 		   = "select memberType,agent_crm_id,agent_crm_link_date,username from agent where agent_crm_id>0 and memberType>0";
	$uniagentDb->query($sql);
	$ga_agent_list = $uniagentDb->fetch();
	 
	$certified_user_details = $globalDB->resultByKey($ga_agent_list,'agent_crm_id');
	
?>

<?php include('../includes/admin-header.php'); ?>
<?php include('../includes/banner.php'); ?>
<?php include('../includes/admin-left-panel.php'); ?>

	<!-- right-panel -->
	<div class="right-panel column">
	
	<?php include('../includes/admin_login_section.php'); ?>
	
	<!-- view branch office form -->
	<div class="form-container">
		<div class="form-sub-head">View Agent</div>
		<form method="post" >
			<fieldset class="search-box">
				<ul class="fields">
					<li>
						<span class="lable"> Country </span>
						<?php echo $objAgent->country('country',$_SESSION['search']['country'],' '); ?>
					</li>
					
					<li>
						<span class="lable"> Keyword </span>
						<input type="text" name="keyword" placeholder="Keyword" value="<?php echo $_SESSION['search']['keyword']; ?>">
					</li>
					
					<li style="text-align:right;">
						<button name="search"><i class="fa fa-search"></i> View</button>
						<button type="button" onclick="if(confirm('Are You sure you want to reset your search preference?'))window.location.href='?reset=reset_search';">
									<i class="fa fa-refresh"></i> Reset</button>
					</li>
				
				</ul>
			</fieldset>
		</form>

	    <!-- listing -->
	
		<?php 
			
			$or_like = " agentId>0 ";
					
			if(isset($_GET['status']) && $_GET['status']!='') {

				$_SESSION['search']['status'] = $_GET['status'];

			}

			

			//=========== Account Active  ====//
			
			if( isset($_SESSION['search']['status']) AND ($_SESSION['search']['status']=='A' ||  $_SESSION['search']['status']=='D') ){
			
				$where['agentStatus'] = $_SESSION['search']['status'];
			
			}

			//=========== Account Active and subscribed ====//

			if( isset($_SESSION['search']['status']) AND $_SESSION['search']['status']=='AS' ){
			
				$or_like .= " and agentStatus='A' and  DATE(valid_till) >= '" . date('Y-m-d') . "' ";	
		
			}

			//=========== Account Active and Account Valid Date Expired ====//

			if( isset($_SESSION['search']['status']) AND $_SESSION['search']['status']=='AE' ){
			
				$or_like .= " and agentStatus='A' and DATE(valid_till) <= '" . date('Y-m-d') . "' ";
		
			}

			//=========== Account Deactive and Account Valid Date Not Expired ====//

			if( isset($_SESSION['search']['status']) AND $_SESSION['search']['status']=='DS' ){
			
				$or_like .= " and agentStatus='D' and  DATE(valid_till) >= '" . date('Y-m-d') . "' ";	

			}

			if( isset($_SESSION['search']['country']) AND $_SESSION['search']['country']!='' ){
		  
				$where['country'] = $_SESSION['search']['country'];
			
			}

			if( isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword']!='' ){
			  
				$or_like .=  " and (username LIKE '%{$_SESSION['search']['keyword']}%' 
							   OR contactPerson LIKE '%{$_SESSION['search']['keyword']}%' 
							   OR city LIKE '%{$_SESSION['search']['keyword']}%' 
							   OR agencyName LIKE '%{$_SESSION['search']['keyword']}%' 
							   OR emailAddress LIKE '%{$_SESSION['search']['keyword']}%' 
							   OR designation LIKE '%{$_SESSION['search']['keyword']}%' 
							   OR skypeId LIKE '%{$_SESSION['search']['keyword']}%' 
							   OR state LIKE '%{$_SESSION['search']['keyword']}%' ) ";
			
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

			//echo $db->last_query();
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
		
		if(file_exists('../agent_logo/'. $value['agentLogo'])){
			echo '<img src="../agent_logo/'. $value['agentLogo'].'" >';
		
		} else { echo '<img src="../images/no_image_available.png" >'; }
		} else { echo '<img src="../images/no_image_available.png" >'; } 
	  
	?>
	
	<?php echo $value['agencyName']; ?>
	
	<?php
		// pr($value['email_verified']); 
		if($value['email_verified']=='N')
		echo ' | <a href="send_verify_link_live.php?id='.$objCommon->url_encrypt($value['agentId']).'" style="color:red;" rel="[facebox]" rev="iframe|650|400" > Send Email Verify Link </a>';
		
	?>
    <?php
		if(!empty($certified_user_details[$value['agentId']])) {
			if($certified_user_details[$value['agentId']]['memberType']=='3') {
				echo "| <span style='color:yellow; font-weight:bold;'> Silver (".$certified_user_details[$value['agentId']]['username'].")</span>";
			}else if($certified_user_details[$value['agentId']]['memberType']=='4') {
				echo "| <span style='color:yellow; font-weight:bold;'> Gold (".$certified_user_details[$value['agentId']]['username'].")</span>";
			}else if($certified_user_details[$value['agentId']]['memberType']=='5') {
				echo "";
			}else {
				echo "";	
			}
		}
	?>
	</div>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<th>Contact Details</th>
		<th width="100">Data Consumed</th>
		
		<th width="100">Country</th>
		
		<th width="100">User Name</th>
		<th width="50">Status</th>
		<th width="130"><i class="fa fa-calendar-o"></i> Add Date</th>
        <th width="130"><i class="fa fa-calendar-o"></i> Active From</th>
        <th width="130"><i class="fa fa-calendar-o"></i> Active Till Date</th>
		<th width="50" class="center">Action</th>
	  </tr>
	  <tr>
		<td> <font title="Email"><?php echo $value['emailAddress']; ?> </font> | <font title="Contact Number"><?php echo $value['contact_no']; ?></font> | <font title="Contact Person"> <?php echo $value['contactPerson']; ?> </font>| <font title="Designation" ><?php echo $value['designation']; ?> </font> </td>
		
		<td> <font title="User Name"> <?php echo $objAgent->agent_consumed_data($value['agentId']); ?> MB </font></td>
		
		
		<td> <?php if($contry_detail[0]['short_name']==''){ echo '-'; } else { echo $contry_detail[0]['short_name'];} ?> </td>
		
		<td> <font title="User Name"> <?php echo $value['username']; ?> </font></td>
		<td>
		
		
			
			<div class="chk_box_click" id="div_parent_chk_<?php echo $value['agentId']; ?>" style="display:inline-block;"  table_id="<?php echo $value['agentId']; ?>" status="<?php echo $value['agentStatus']; ?>">
							
			<input type="checkbox" class="js-switch" <?php if($value['agentStatus']=='A'){ echo 'checked="checked"'; } ?>  id="chk_box_id_<?php echo $value['agentId']; ?>" > 
			
			</div>
		
		</td>
		
		<td><?php echo date("d F Y",strtotime($value['addDate'])); ?> </td>
		<td><?php echo $value['subscription_date']; ?></td>
        <td><?php echo $value['valid_till']; ?></td>
        
        
		<td class="center">
		
		<a href="agent-details.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>" title="View Agent Details"><i class="fa fa-eye"></i></a>
		
		
		&nbsp; <a href="agent-edit-details.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>" title="Edit Agent Details"><i class="fa fa-edit"></i></a>
		
		&nbsp; 
		<a href="dynamic_payment_link.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>" title="Make Payment Link"><i class="fa fa-money"></i></a>
		
		
		
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
		
		set_left_menu('submenu_view_agent','submenu_agent','button_member');
		
		
	</script>	

<?php include('../includes/agent-footer.php'); ?>