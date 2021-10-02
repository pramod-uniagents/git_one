<?php

  require_once("../includes/config.php");
  require_once("../includes/function.php");
  
  require_once("../class/classDbDemo.php");
  $db 			= new Database();
  
  require_once("../class/agentClass.php");
  $objAgent   	= new agent();
  
  require_once("../class/commonClass.php");
  $objCommon  	= new common();
  
  require_once("../class/admin.php");
  $objAdmin   	= new admin();
  
  // To check admin is login or not
  $objAdmin->check_admin_login();
  
  if(isset($_REQUEST['suggestion']) && $_REQUEST['suggestion']!="") {
	  
	  $keyword_id 	= decrypt($_REQUEST['keyword_id']);
	  
	  $category_id 	= decrypt($_REQUEST['category_id']);
	  
	  $suggestion 	= decrypt($_REQUEST['suggestion']);
  }
  
  $total_consumed_space = $objAgent->check_consumed_data($_SESSION['login']['agent_id']);
   
  if(isset($_POST['submit'])) {
	  extract($_POST);
	  if ($suggestion_category_id == '') {
		  $_SESSION[error]['msg'] = '<font color="red">Please select suggestion category name!</font>';
	  }else if (trim($suggestion_name)=="") {
		  $_SESSION[error]['msg'] = '<font color="red">Please enter suggestion name</font>';
	  }else {
		if(!empty($keyword_id)) {
			
		  $data = array(
					'suggesition' => addslashes($suggestion_name),
					'suggestion_category_id' => $suggestion_category_id,
					'update_date' => 'now()');
		  
		  $db->where(array('id' => $keyword_id))->limit(1);
		  $data 			 	= $objAgent->sanitize_data($data);
		  $suggestion_update 	= $db->update('agent_suggesition_keyword', $data);
		  
		  if($suggestion_update) {
			  
			$_SESSION[error]['msg'] = '<font color="green">Suggestion Updated Successfully</font>'; 
		  }else {
			  
			$_SESSION[error]['msg'] = '<font color="red">Sorry, Please Try Again.</font>';    
		  }
		  
		}else {
			
			$_SESSION[error]['msg'] = '<font color="red">Sorry, Please Try Again.</font>';    
		}
		  
		echo "<script>window.location.href='edit_suggestion.php?keyword_id=".urlencode($_GET['keyword_id'])."&category_id=".urlencode($_GET['category_id'])."&suggestion=".urlencode(encrypt($suggestion_name))."';</script>";
		
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

            <?php if (isset($_SESSION[error]['msg'])) { ?>
                <div id="error_msg"><?php echo $_SESSION[error]['msg']; ?></div>
                <div class="clearfix"></div>
                <div class="clearfix"></div>
                <?php unset($_SESSION[error]['msg']);
            } ?>
            
            <div class="form-sub-head"> 
                 <div class="pull-left">Update New Suggestion Keyword</div>
                 <div class="pull-right">
                    <!-- <a href="view_suggestion_category.php" class="btn-success"><i class="fa fa-eye"></i> View Suggestion Category </a>-->
                    <a href="add_new_suggestion_category.php" class="btn-success"><i class="fa fa-eye"></i> Add New Suggestion Category</a>
                 </div>
                 <div class="clearfix"></div>
            </div>
           
            <div class="clearfix"></div>
            <fieldset>
            
            <form action="" method="post" enctype="multipart/form-data">
                <ul class="fields">
                  <li>
                    <span class="lable">Select Suggestion Categroy <span class="required" style="color:#F00;">*</span></span>
                    <label class="select">
                      <select name="suggestion_category_id" id="suggestion_category_id" required>
                        <option value="">Select Suggestion Categroy Name</option>
                        <?php
                           $suggestion_category_query   = "SELECT * FROM suggestion_category WHERE status='Y' order by id asc";
                           $objAgent->query($suggestion_category_query);
                           $suggestion_category_data    = $objAgent->fetch();
                           foreach($suggestion_category_data as $key=>$category_data) {
                        ?>
                        <option value="<?php echo $category_data['id']; ?>" <?php if(@$category_id== $category_data['id']) { ?> selected <?php } ?>><?php echo $category_data['name']; ?></option> 
                        <?php } ?>
                      </select>
                    </label>   
                  </li>
    
                  <li>
                  <span class="lable"> Suggestion Keyword Name <span class="required" style="color:#F00;">*</span></span>
                  <input name="suggestion_name" id="suggestion_name" value="<?php echo trim($suggestion); ?>" type="text" placeholder="Add Name Suggestion" required/>
                  </li>
                   
                  <div class="spacer"></div>
                    
                </ul>
            </fieldset>
            <div class="spacer"></div>
            <div align="center"><button name="submit">SUBMIT</button></div>
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