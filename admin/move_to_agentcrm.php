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
	
	
	$sql = "SELECT * FROM agent WHERE username='{$_SESSION['agent_detial'][0]['username']}' ";
	$db->query($sql);
	$record = $db->fetch();
	
	
	// pr($record);
	// pr($_SESSION['agent_detial']);
	
	if(is_array($record) AND count($record)==0){
		
		$data = array(	
						'address'=>$_SESSION['agent_detial'][0]['address'],
						'area'=>$_SESSION['agent_detial'][0]['area'],
						'city'=>$_SESSION['agent_detial'][0]['city'],
						'state'=>$_SESSION['agent_detial'][0]['state'],
						'pinCode'=>$_SESSION['agent_detial'][0]['pinCode'],
						'country'=>$_SESSION['agent_detial'][0]['country'],
						'agencyName'=>$_SESSION['agent_detial'][0]['agencyName'],
						'username'=>$_SESSION['agent_detial'][0]['username'],
						'password'=>$_SESSION['agent_detial'][0]['password'],
						'owner'=>$_SESSION['agent_detial'][0]['owner'],
						'contactPerson'=>$_SESSION['agent_detial'][0]['contactPerson'],
						'designation'=>$_SESSION['agent_detial'][0]['designation'],
						'emailAddress'=>$_SESSION['agent_detial'][0]['emailAddress'],
						'contact_email'=>$_SESSION['agent_detial'][0]['contact_email'],
						'primaryEmail'=>$_SESSION['agent_detial'][0]['primaryEmail'],
						'secondryEmail'=>$_SESSION['agent_detial'][0]['secondryEmail'],
						'directPhoneNumber'=>$_SESSION['agent_detial'][0]['directPhoneNumber'],
						'phoneNumber'=>$_SESSION['agent_detial'][0]['phoneNumber'],
						'phoneNumber2'=>$_SESSION['agent_detial'][0]['phoneNumber2'],
						'mobile'=>$_SESSION['agent_detial'][0]['mobile'],
						'website'=>$_SESSION['agent_detial'][0]['website'],
						'skypeId'=>$_SESSION['agent_detial'][0]['skypeId'],
						'facebookUrl'=>$_SESSION['agent_detial'][0]['facebookUrl'],
						'linkdinUrl'=>$_SESSION['agent_detial'][0]['linkdinUrl'],
						'twitterUrl'=>$_SESSION['agent_detial'][0]['twitterUrl'],
						'googlePlusUrl'=>$_SESSION['agent_detial'][0]['googlePlusUrl'],
						'agentLogo'=>$_SESSION['agent_detial'][0]['agentLogo'],
						'lastLogin'=>$_SESSION['agent_detial'][0]['lastLogin'],
						'lastLoginIP'=>$_SESSION['agent_detial'][0]['lastLoginIP'],
						'agentStatus'=>'D',
						'addDate'=>$_SESSION['agent_detial'][0]['addDate'],
						'email_verified'=>$_SESSION['agent_detial'][0]['email_verified'],
						'moved'=>$_SESSION['agent_detial'][0]['agentId'],
						'addDate'=>'now()'
						
						);
		
		$agent_id = $db->insert('agent',$data);
		
		// copy('source', 'target');
		
		
		
		copy('../../agent-crm-demo/agent_logo/'.$_SESSION['agent_detial'][0]['agentLogo'], '../agent_logo/'.$_SESSION['agent_detial'][0]['agentLogo']);
		
		$_SESSION['agent_added_id'] = $_SESSION['agent_detial'][0]['agentId'];
		$_SESSION[error]['msg'] = '<font color="green">Agent is successfully moved to agent CRM!</font>';
		
		
		
	} else {
		
		$_SESSION[error]['msg'] = '<font color="green">Agent is already moved to agent CRM!</font>';
		
	}
	
	
	echo "<script>window.location.href='agent-details-demo.php?id=".urlencode(encrypt($_SESSION['agent_detial'][0]['agentId']))."';</script>";
	exit;
	
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
		
		
		
        
        </div>
<!-- add branch office form -->

</div>
<!-- right-panel -->

	<script>	
		set_left_menu('submenu_view_agent_demo','submenu_agent','button_member');
	</script>
	
<?php include('../includes/agent-footer.php'); ?>