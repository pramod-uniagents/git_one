<?php 

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	require_once("../class/classDb.php");
	// require_once("../class/agentClass.php");

	$db = new Database();
	// $objAgent = new agent();
	
	// echo encrypt('sanjay_agent');
	
	// $db->from('country');
	// $country = $db->fetch();
	
	
	// pr($country);
	
	// echo $db->last_query();
	// di($_POST);
	
	
	if( isset($_POST['submit']) ){
			
		
		
		if($_POST['user']==''){
			$_SESSION[error]['msg'] = '<font>Please enter the user!</font>';
		
		} else if($_POST['password']==''){
			$_SESSION[error]['msg'] = '<font>Please enter the password!</font>';
		
		} else {
			
			extract($_POST);
			
			// pr($_POST);
			
			
				
						$db->from('admin');
						$db->select();
						$db->where(array('user'=>$user,'password'=>encrypt($password)));
						$user = $db->fetch();
						// echo $db->last_query();
		
						// pr($user);
	
					
					
						if(count($user)>0){
							
								$_SESSION['login']['username']     = $user[0]['user'];
								$_SESSION['login']['id']   = $user[0]['id'];
								$_SESSION['login']['last_login']   = $user[0]['last_login'];
								$_SESSION['login']['last_login_ip']   = $user[0]['ip_address'];
								$_SESSION['login']['user_type']   = 'admin';
							// pr($_SESSION);
							
							// Need to update 
							$db->where(array('id'=>$_SESSION['login']['id']));
							$user = $db->update('admin',array('last_login'=>'now()','ip_address'=>$_SERVER['REMOTE_ADDR']));
							
							
							
							echo "<script>window.location.href='admin-dashboard.php';</script>";
							
							
							
						}	else {
							$_SESSION[error]['msg'] = '<font color="red">User and Password seems incorrect! Please try again!</font>';
							
						
						}
						
		}
	
	
	}
	
	
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>Agents Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css" />
</head>

<body>
<!-- header -->
<header>
<div class="logo"><img src="../images/uniagents-plus.png" style="max-width:123px;"></div>
<div style="position:absolute;right:30px;top:35px;font-weight:bold;color:#666;">
<div style="font-size:18px;color:#666;padding:10px 0;text-align:right;font-weight:bold;">
</div>
</div>
</header>
<div class="banner" style="max-height:100px;"><img src="../images/banner.jpg"></div>
<!-- header -->

<div class="signin-container">
<div class="container">
<!-- right -->
<div class="signin-right">
<div class="login-box">
<div class="login-head"><i class="fa fa-users"></i> UniAgents Agents CRM</div>
<form action="" method="post">
<?php 
if(isset($_SESSION[error]['msg']))
echo '<div class="fields">'.$_SESSION[error]['msg'].'</div>';
unset($_SESSION[error]['msg']);		
?>

<div class="form">
<span class="label">Username <span class="required">*</span></span>
<input name="user" id="user" type="text" required />
</div>
<div class="form">
<span class="label">Password <span class="required">*</span></span>
<input name="password" id="password" type="password" required />
</div>

<div class="form">
<div class="pull-right"><button type="submit" name="submit" class="btn_signin"><i class="fa fa-key"></i> Login</button></div>
<div class="clearfix"></div>
</div>

</form>
</div>

</div>
<!-- right -->
<!-- left -->
<div class="signin-left">
<div class="head">Agent CRM</div>
<p>Uniagents offers educational consultants an opportunity to experience flawless and streamlined office operations. By using our Agent CRM the consultants will be able to manage and control all their branch offices and counsellors working in respective offices. You can manage all your institutions which you represent, define your own country specific application process, track all your applications and enjoy the flexibility, transparency and control of office operations as never before.</p>
<p class="highlights"><img src="../images/tracking.png" width="40" height="40" align="absmiddle"> Experience better Conversion Rate by Live tracking</p>
<p class="highlights"><img src="../images/application.png" width="40" height="40" align="absmiddle"> Allow students to track application status from your website</p>
<p class="highlights"><img src="../images/lead-management.png" width="40" height="40" align="absmiddle"> Application and Lead Management</p>
<p class="highlights"><img src="../images/crm.png" width="40" height="40" align="absmiddle"> Integrate your website enquiries with the CRM for better management and follow ups</p>
<p class="highlights"><a href="https://www.youtube.com/watch?v=E7KG7nsWY6A&feature=youtu.be" target="_blank"><img src="../images/info.png" width="40" height="40" align="absmiddle"> Easy understanding of features and usage of the system Watch sample video</a></p>
<p class="highlights"><a href="https://www.youtube.com/watch?v=oPuiTBqVu1g" target="_blank"> <img src="../images/youtube.png" width="40" height="40" align="absmiddle">Watch how our Agent CRM can help your business</a></p>
</div>
<!-- left -->
<div class="clearfix"></div>
<a class="learn-more" href="https://www.uniagents.com/agents-crm.php" target="_blank"><strong>Learn More benefits of Agent CRM</strong> <i class="fa fa-arrow-right"></i></a>
</div>
</div>
<!-- signin container -->
<div class="spacer"></div>
<div class="powered-by">&copy; Copyright UniAgents.com</div>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery_cookie_plugin.js"></script>
</body>
</html>


