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
	
	extract($_POST);
	
	if( isset($search) ){
		$_SESSION['search']['country'] = $country;
		$_SESSION['search']['keyword'] = $keyword;
		$_SESSION['search']['status'] = $status;
	
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
	<div class="form-sub-head">View Institution
	
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
	
	<li><span class="lable"> Status </span>
		<select name="status" id="status">
		<option value="">Select</option>
		<option value="1" <?php if($_SESSION['search']['status']=='1') echo 'selected'; ?>>Active</option>
		<option value="0" <?php if($_SESSION['search']['status']=='0') echo 'selected'; ?> >Inactive</option></select></li>
	
	
	
	<li style="text-align:right;"><button name="search"><i class="fa fa-search"></i> View</button></li>
	</ul>
	</fieldset>
	
	</form>
	

	<!-- listing -->
	
	<?php 
				
				// pr($record);
				
				
				$sql = "SELECT * FROM  institute  ";
				$where = " WHERE (  user_type='AD' ) ";
				
				
				// condition for listing 
				
				if(  isset($_SESSION['search']['country']) AND $_SESSION['search']['country']!='' ){
				
					$where .= " AND country='{$_SESSION['search']['country']}'";
				
				}
				
				
				
				if( isset($_SESSION['search']['status']) AND $_SESSION['search']['status']!='' ){
				
					$where .= " AND status='{$_SESSION['search']['status']}'";
				}
				
				
				if( isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword']!='' ){
				
					$where .= " AND (  email LIKE '%{$_SESSION['search']['keyword']}%'  OR institute_name LIKE '%{$_SESSION['search']['keyword']}%' OR campus LIKE '%{$_SESSION['search']['keyword']}%' OR contact_person LIKE '%{$_SESSION['search']['keyword']}%' OR user_id LIKE '%{$_SESSION['search']['keyword']}%' )";
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
				$_SESSION['download']['csv_query'] = $db->last_query();
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
	
	<?php 
				
		// echo $_SESSION['login']['agentLogo'];
		if(isset( $value['logo'] ) AND  $value['logo']!=''){
		
		if(file_exists('../inst_logo/thumb/'. $value['logo'])){
			echo '<img src="../inst_logo/thumb/'. $value['logo'].'" >';
		
		} else { echo '<img src="../images/no_image_available.png" >'; }
		} else { echo '<img src="../images/no_image_available.png" >'; } 
	  
	?>
	
	<?php echo $value['institute_name']; ?>
	
	</div>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<th>Contact Details</th>
		
		
		<th width="100">Country</th>
		
		<th width="100">User Name</th>
		<th width="50">Status</th>
		<th width="130"><i class="fa fa-calendar-o"></i> Add Date</th>
		<th width="50" class="center">Action</th>
	  </tr>
	  <tr>
		<td> <font title="Email"><?php echo $value['email']; ?> </font> | <font title="Contact Number"><?php echo $value['contact_no']; ?></font> | <font title="Contact Person"> <?php echo $value['contact_person']; ?> </font> 
		<?php if( !empty($value['campus']) ) { ?>
		| <font title="Campus" ><?php echo $value['campus']; ?> </font> 
		<?php } ?>
		</td>
		
	
		
		
		<td> <?php if($contry_detail[0]['short_name']==''){ echo '-'; } else { echo $contry_detail[0]['short_name'];} ?> </td>
		
		<td> <font title="User Name"> <?php echo $value['user_id']; ?> </font></td>
		<td>
		
		
			
			<div class="chk_box_click" id="div_parent_chk_<?php echo $value['id']; ?>" style="display:inline-block;"  table_id="<?php echo $value['id']; ?>" status="<?php echo $value['status']; ?>">
							
			<input type="checkbox" class="js-switch" <?php if($value['status']=='1'){ echo 'checked="checked"'; } ?>  id="chk_box_id_<?php echo $value['id']; ?>" > 
			
			</div>
		
		</td>
		
		
		<td><?php echo date("d F Y",strtotime($value['addDate'])); ?> </td>
		
		<td class="center">
		<!--
		<a href="agent-details.php?id=<?php echo urlencode(encrypt($value['id'])); ?>" title="View Details"><i class="fa fa-eye"></i></a>
		-->
		
		&nbsp; <a href="institution_edit.php?id=<?php echo urlencode(encrypt($value['id'])); ?>" title="Edit Details"><i class="fa fa-edit"></i></a>
		
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
					if(status=="1")
					status = "0";
					else
					status = "1";
				
					// $('#loading').show();
					values = {"type":"change_common_status","table_id":table_id,"table":"institute","field":"id","data":{"status":status}}
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
		
		
		
		set_left_menu('submenu_view_inst','submenu_inst','button_member');
		
		
	</script>	

<?php include('../includes/agent-footer.php'); ?>