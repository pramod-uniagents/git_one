<?php

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	require_once("../class/classDb.php");
	$db 		= new Database();
	
	require_once("../class/agentClass.php");
	$objAgent   = new agent();
	
	require_once("../class/commonClass.php");
	$objCommon  = new common();
	
	require_once("../class/admin.php");
	$objAdmin   = new admin();
	
	// To check admin is login or not
	$objAdmin->check_admin_login();

	$total_consumed_space = $objAgent->check_consumed_data($_SESSION['login']['agent_id']);
	
	if (isset($_POST['submit'])) 
	{
		extract($_POST);
		
		$sql 			 = "select id from admin where id='{$_SESSION['login']['id']}' and password='".encrypt($password)."' ";
		$db->query($sql);
        $password_exists = $db->fetch_first();
		
		
		
		if (empty($password)) {
			$_SESSION['error']['msg'] = '<font color="red">Please Enter Old Password</font>';
		}
		
		if (empty($new_password)) {
			$_SESSION['error']['msg'] = '<font color="red">Please Enter New Password</font>';
		}
		
		if (empty($confirm_password)) {
			$_SESSION['error']['msg']  = '<font color="red">Please Enter Confirm Password</font>';
		}
		
		if($new_password!=$confirm_password) {
			
			$_SESSION['error']['msg']  = '<font color="red">Confirmation Password  must match New Password.</font>';
			
		}
		
		if($password_exists['id']<=0) {
			
			$_SESSION['error']['msg']  = '<font color="red">Old Password Not Match.</font>';
		}
		
		
		if(empty($_SESSION['error']['msg'])) {
			
			$data = array(
			  'password' => addslashes(encrypt($new_password)),
			  'add_date' => 'now()'
			);
			
			$updated = $db->where('id',$_SESSION['login']['id'])
							->update('admin',$data);
			
			if($updated) {
			  $_SESSION['error']['msg'] = '<font color="green">Password Changed successfully</font>';  
			}else {
			  $_SESSION['error']['msg'] = '<font color="red">Sorry, Please Try Again.</font>';  
			}
			
			echo "<script>window.location.href='change-password.php';</script>";
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

    <!-- add branch office form -->
    
       <div class="form-container">

            <?php if (isset($_SESSION['error']['msg'])) { ?>
                <div id="error_msg"><?php echo $_SESSION['error']['msg']; ?></div>
                <div class="clearfix"></div>
                <div class="clearfix"></div>
                <?php unset($_SESSION['error']['msg']);
            } ?>
            
            <div class="form-sub-head"> 
             <div class="pull-left">Change Password</div>
             
             <div class="clearfix"></div>
            </div>
           
            <div class="clearfix"></div>
            <fieldset >
            
            <form action="" method="post" enctype="multipart/form-data">
            
            <ul class="fields">

              <li>
                  <span class="lable"> Old Password <span class="required" style="color:#F00;">*</span></span>
              </li>
              
              <li>    
                  <input name="password" id="password" value="" type="password" placeholder="Please Enter Old Password" required/>
              </li>
              
              <li>
                  <span class="lable"> New Password <span class="required" style="color:#F00;">*</span></span>
              </li>
              
              <li>    
                  <input name="new_password" id="new_password" value="" type="password" placeholder="Please Enter New Password" required/>
              </li>
              
              <li>
                  <span class="lable"> Confirmed Password <span class="required" style="color:#F00;">*</span></span>
              </li>
              
              <li>    
                  <input name="confirm_password" id="confirm_password" value="" type="password" placeholder="Please Enter Confirm Password" required/>
              </li>
              
               
              <div class="spacer"></div>
                
            </ul>
            </fieldset>
            
            <div class="spacer"></div>
            <div class="spacer"></div>
            
            <div align="center">
                <button name="submit">SUBMIT</button>
            </div>
            
            </form>
        </div>
    
    <!-- add branch office form -->

</div>

<!-- right-panel -->



<?php //include('../includes/agent-footer.php'); ?>
<?php include_once('toolbox.php'); ?>
<?php include_once('common_file.php'); ?>
</body>
</html>