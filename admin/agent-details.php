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
	
	if(isset($_GET['id'])) {
	$agent_id = decrypt($_GET['id']);
	
	$sql = "SELECT * FROM agent WHERE agentId='{$agent_id}' ";
	$db->query($sql);
	$record = $db->fetch();
	}
	
	$country_array = $objAgent->country_array();
	// pr($country_array);
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
		<div class="form-container">
        <div class="form-sub-head">Agent Details</div>
		<fieldset>
		<legend>Personal Information</legend>
		<ul class="fields border">
		<li><span class="half half-head">Agency Name </span><span class="half"><?php echo $record[0]['agencyName']; ?></span></li>		
        <li><span class="half half-head">Address</span><span class="half"><?php echo $record[0]['address']; ?></span></li>
        <li><span class="half half-head">City</span><span class="half"><?php echo $record[0]['city']; ?></span></li>
        <li><span class="half half-head">State</span><span class="half"><?php echo $objAgent->country('country',$_SESSION['search']['country'],' '); ?></span></li>
        <li><span class="half half-head">Country</span><span class="half"><?php echo $country_array[$record[0]['country']]['short_name']; ?></span></li>
        <li><span class="half half-head">Zipcode</span><span class="half"><?php echo $record[0]['pinCode']; ?></span></li>
        <li><span class="half half-head">Phone Number</span><span class="half"><?php echo $record[0]['phoneNumber']; ?></span></li>
        <li><span class="half half-head">Email Address</span><span class="half"><?php echo $record[0]['emailAddress']; ?></span></li>
        <li><span class="half half-head">Website</span><span class="half"><?php echo $record[0]['website']; ?></span></li>
		</ul>
		</fieldset>	
		<fieldset>
		<legend>Point of contact</legend>
		<ul class="fields border">
		<li><span class="half half-head">Contact Person</span><span class="half"><?php echo $record[0]['contactPerson']; ?></span></li>		
        <li><span class="half half-head">Designation</span><span class="half"><?php echo $record[0]['designation']; ?></span></li>
        <li><span class="half half-head">Email</span><span class="half"><?php echo $record[0]['contact_email']; ?></span></li>
        <li><span class="half half-head">Phone Number</span><span class="half"><?php echo $record[0]['directPhoneNumber']; ?></span></li>
        <li><span class="half half-head">Mobile Number</span><span class="half"><?php echo $record[0]['mobile']; ?></span></li>
        <li><span class="half half-head">Skype Id</span><span class="half"><?php echo $record[0]['skypeId']; ?></span></li>        
		</ul>
		</fieldset>	
		<fieldset>
		<legend>Login Detail</legend>
		<ul class="fields border">
		<li><span class="half half-head">Username</span><span class="half"><?php echo $record[0]['username']; ?></span></li>
        <li><span class="half half-head">Password</span><span class="half">*****</span></li>
        <li><span class="half half-head">Agent Status</span><span class="half"><?php 
		if($record[0]['agentStatus']=='A')
		echo "Active";
		else 
		echo "Inactive";
		?></span></li>
        <li><span class="half half-head">Email Verified</span><span class="half">
		<?php
		//echo $record[0]['email_verified']; 
		if($record[0]['email_verified']=='Y')
		echo "Yes";
		else 
		echo "No";
		?></span></li>
        <li><span class="half half-head">Valid From</span><span class="half">
		<?php
		//echo $record[0]['subscription_date']; 
		
		if($record[0]['subscription_date']=='0000-00-00'){ echo "-"; }else { echo date('d F Y H:i:s', strtotime($record[0]['subscription_date'])); }
		
		?></span></li>
        <li><span class="half half-head">Valid To</span><span class="half">
		<?php
		// echo $record[0]['valid_till']; 
		if($record[0]['valid_till']=='0000-00-00'){ echo "-"; }else { echo date('d F Y H:i:s', strtotime($record[0]['valid_till'])); }
		?></span></li>
        <li><span class="half half-head">File Allowed</span><span class="half">
		<?php 
		if(!empty($record[0]['file_allowed']))
		echo $record[0]['file_allowed']. " MB";
		?></span></li>
        <li><span class="half half-head">Register on</span><span class="half">
		<?php 
		// echo $record[0]['addDate'];
		if($record[0]['addDate']=='0000-00-00 00:00:00'){ echo "-"; }else { echo date('d F Y H:i:s', strtotime($record[0]['addDate'])); }
		?></span></li>
        <li><span class="half half-head">Last Login</span><span class="half"><?php 
		// echo $record[0]['lastLogin']; 
		
		if($record[0]['lastLogin']=='0000-00-00 00:00:00'){ echo "-"; }else { echo date('d F Y H:i:s', strtotime($record[0]['lastLogin'])); }
		
		?></span></li>
		
		
        <li><span class="half half-head">Last Login IP</span><span class="half"><?php echo $record[0]['lastLoginIP']; ?></span></li>
		
		<li><span class="half half-head">Payments</span><span class="half"><?php echo $record[0]['payment']; ?></span></li>
		
		<li><span class="half half-head">Payment Description</span><span class="half"><?php echo $record[0]['details']; ?></span></li>
		
		</ul>
		</fieldset>	
		<fieldset>
		<legend>Additional Information</legend>
		<ul class="fields border">
        <li><span class="half half-head">Facebook Url</span><span class="half"><?php echo $record[0]['facebookUrl']; ?></span></li>
        <li><span class="half half-head">LinkedIn Url</span><span class="half"><?php echo $record[0]['linkdinUrl']; ?></span></li>
        <li><span class="half half-head">Twitter Url</span><span class="half"><?php echo $record[0]['twitterUrl']; ?></span></li>
        <li><span class="half half-head">Google Url</span><span class="half"><?php echo $record[0]['googlePlusUrl']; ?></span></li>
        <li><span class="half half-head" style="vertical-align:middle;">Logo</span><span class="half" style="vertical-align:middle;">
		<?php 
			// echo $record[0]['agentLogo'];
			
			// echo $_SESSION['login']['agentLogo'];
			if(isset( $record[0]['agentLogo'] ) AND  $record[0]['agentLogo']!=''){
			
			if(file_exists('../agent_logo/'. $record[0]['agentLogo'])){
				echo '<img src="../agent_logo/'. $record[0]['agentLogo'].'" width="80">';
			
			} else { echo '<img src="../images/no_image_available.png" width="80">'; }
			} else { echo '<img src="../images/no_image_available.png" width="80">'; } 
		?>
		</span></li>
		</ul>
		</fieldset>
        </div>
<!-- add branch office form -->

</div>
<!-- right-panel -->

	<script>	
		set_left_menu('submenu_view_agent','submenu_agent','button_member');
	</script>
	
<?php include('../includes/agent-footer.php'); ?>