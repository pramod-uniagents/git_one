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
	
?>	

	<?php include('../includes/admin-header.php'); ?>
	<?php include('../includes/banner.php'); ?>
	<?php include('../includes/admin-left-panel.php'); ?>
	
	<link rel="stylesheet" href="../css/jquery.custom-scrollbar.css" type="text/css" media="screen"/> 

	<script type="text/javascript" src="../js/jquery.custom-scrollbar.js"></script>
	<script type="text/javascript" src="../js/jquery.custom-scrollbar.min.js"></script>
	<style type="text/css">
	/*.form-container fieldset{background:#eaeaea;}
	.form-container fieldset:nth-child(2n){background:#fbfbfb;border:1px solid #e0e0e0;}
	.form-container fieldset:nth-child(2n) ul.fields li{background:#fefafa;}*/
	.form-container fieldset ul.fields{padding:6px 10px;/*margin-bottom:8px;*/}
	.form-container fieldset ul.fields li{
		border: 1px solid #dbdbdb;
		border-radius: 5px;
		text-align:left;
		background:#f4f4f4;
		box-sizing:border-box;
		display:table-cell;
		float:left;
		margin:5px;color:#767676;
	}

	.form-container fieldset{
		min-height:60px;
		padding:10px; 
		-webkit-box-shadow: 0px 0px 21px -12px rgba(0,0,0,0.5); 
		-moz-box-shadow: 0px 0px 21px -12px rgba(0,0,0,0.5); 
		box-shadow: 0px 0px 21px -12px rgba(0,0,0,0.5);
	}

	.form-container fieldset ul.fields li span.lable{padding-left:10px;}
	.form-container fieldset legend{background:#8fb5d6;border:none;color:#fff;}
	/*.responsive{width:100%;overflow-y:scroll;}*/
	.scrollable .viewport .overview{width:100%;}
	.scrollable.default-skin .scroll-bar.vertical{background:#ccc;}
	.scrollable .viewport{min-height:100px; max-height:500px;}
	.form-container table th{
		background: #204c6f;
    	color: #fff;
    	font-weight: bold;
	}
	.form-container table a{
		color: #212121;
	}
	</style>

	<div class="right-panel column">
		
		<?php include('../includes/admin_login_section.php'); ?>

		<!-- dashboard-contianer -->
		<div class="dashboard-container form-container" style="padding:10px 20px;">
			<div class="form-sub-head">Dashboard</div>
			<ul>
				<li><i class="fa fa-building background"></i><i class="fa fa-building"></i>
					<span class="number">
					<a href="javascript:void(0);">
					
					<?php 
						$agent_record = $objCommon->detail_info_with_limit('agent','','');
						echo count($agent_record);
					?>
						
					</a></span>
					<span class="title">Total Agents</span>
				</li>

				<li>
					<i class="fa fa-building background"></i><i class="fa fa-building"></i>
					<span class="number">
						<a href="agent_view_dashboard_details.php?status=A" target="_blank">
							<?php
								$sql = " select agentId from agent where agentStatus='A' ";
								$db->query($sql);
								$total_active_status = $db->execute();
								
								echo $total_active_status->affected_rows;  
							?>
						</a>
					</span>

					<span class="title">All Active Agents</span>
				</li>

				<li>
					<i class="fa fa-building background"></i><i class="fa fa-building"></i>
					<span class="number">
						<a href="agent_view_dashboard_details.php?status=AS" target="_blank">
							<?php
								$sql = " select agentId from agent where agentStatus='A' and DATE(valid_till) >= '" . date('Y-m-d') . "' ";
								$db->query($sql);
								$total_active_status_subscriber = $db->execute();
								
								echo $total_active_status_subscriber->affected_rows;                 
							?>
						</a>
					</span>

					<span class="title">All Active & Subscribed Agents</span>
				</li>

				<li>
					<i class="fa fa-building background"></i><i class="fa fa-building"></i>
					<span class="number">
						<a href="agent_view_dashboard_details.php?status=AE" target="_blank">
							<?php 
								$sql = " select agentId from agent where agentStatus='A' and DATE(valid_till) <= '" . date('Y-m-d') . "' ";
								$db->query($sql);
								$total_active_status_expired_subscriber = $db->execute();
								
								echo $total_active_status_expired_subscriber->affected_rows;  
							?>
						</a>
					</span>
					
					<span class="title">All Active & Expired Subscription</span>
				</li>
				
				<li>
					<i class="fa fa-building background"></i><i class="fa fa-building"></i>
					<span class="number">
						<a href="agent_view_dashboard_details.php?status=DS" target="_blank">
							<?php
								$sql = " select agentId from agent where agentStatus='D' and DATE(valid_till) >= '" . date('Y-m-d') . "' ";
								$db->query($sql);
								$total_deactive_status_active_subscriber = $db->execute();
								
								echo $total_deactive_status_active_subscriber->affected_rows;  
							?>
						</a>
					</span>
					
					<span class="title" style="font-size:15px;">Status Deactive But Subscription Not Expired</span>
				</li>

			</ul>
		</div>

		<!-- dashboard-contianer -->
	
		<!-- form container -->

		<div class="form-container">
			<fieldset>
				<legend>Agents login will expire in next 30 Days</legend>		
				<div class="scrollbar1 default-skin scrollable">
					<div class="viewport" style="min-height:252px;max-height:600px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th>Agency Name</th>
								<th>Email/User</th>
								<th>Contact Number</th>
								<th>Expiry Date</th>
							</tr>
							<?php 
								$today_date = date('Y-m-d');
								$to_date = date('Y-m-d', strtotime("+30 days"));
								
								$sql = "SELECT * FROM agent WHERE valid_till BETWEEN  '$today_date' AND '$to_date'  ";
								$db->query($sql);
								$record = $db->fetch();
							
								// di();
								foreach($record as $key=>$value) {
									$agent_detial_link = 'agent-details.php?id='.urlencode(encrypt($value['agentId']));
							?>
							<tr>
								<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo $value['agencyName']; ?></a></td>
								<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo $value['username']; ?></a></td>
								<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo $value['mobile']; ?></a></td>
								<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo date('d-m-Y',strtotime($value['valid_till'])); ?></a></td>
							</tr>
							<?php } ?>
						</table>
					</div>
				</div>
			</fieldset>
		</div>

		<div class="form-container">
		<fieldset>
		<legend>Expired Agent login</legend>		
		<div class="scrollbar1  default-skin scrollable">
		<div class="viewport" style="min-height:400px;max-height:600px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				<th>Agency Name</th>
				<th>Email/User</th>
				<th>Contact Number</th>
				<th>Expiry Date</th>
				</tr>
				<?php 
					$sql = "SELECT * FROM agent WHERE valid_till <  '$today_date'   ";
				
					$db->query($sql);
					$record = $db->fetch();
					
					// di();
					
					foreach($record as $key=>$value) {
						$agent_detial_link = 'agent-details.php?id='.urlencode(encrypt($value['agentId']));
				?>
				<tr>
				<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo $value['agencyName']; ?></a></td>
				<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo $value['username']; ?></a></td>
				<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo $value['mobile']; ?></a></td>
				<td><a href="<?php echo $agent_detial_link; ?>" target="_blank"><?php echo date('d-m-Y',strtotime($value['valid_till'])); ?></a></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		</div>		
		</fieldset>
		</div>
		<!-- form container -->		
		
		</div>
		<!-- right-panel -->

	<script>
	
		$(".scrollbar1").customScrollbar();
		
		$.cookie('add_active_claass','submenu_dashboard');
		$.cookie('submenu_id','');
		$.cookie('submenu_button_id','');
	</script>
<?php include('../includes/admin-footer.php'); ?>