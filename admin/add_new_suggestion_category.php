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

  $total_consumed_space = $objAgent->check_consumed_data($_SESSION['login']['agent_id']);

  if (isset($_POST['submit'])) 
  {
	  extract($_POST);

	  if (trim($suggestion_category_name)=='') {
		  $_SESSION[error]['msg'] = '<font color="red">Please select suggestion category name!</font>';
	  } else if (trim($video_url)=="") {
		  $_SESSION[error]['msg'] = '<font color="red">Please enter Video Url</font>';
	  } else {
		 $category_exits_query  = "select * from suggestion_category where name='".$suggestion_category_name."'";
		 $db->query($category_exits_query);
		 $db->execute();
		 $category_exists_count = $db->affected_rows;
		 
		 if($category_exists_count<=0) {
			$data = array(
			  'name' => $suggestion_category_name,
			  'status' => 'Y',
			  'add_date' => 'now()',
			  'update_date' => 'now()');
			
			$task_id 	= $db->insert('suggestion_category',$data);
			
			$video_data = array(
			  'video_mp4' => $video_url,
			  'suggestion_category_id' => $task_id,
			  'add_date' => 'now()',
			  'update_date' => 'now()');
			
			$add_video_data = $db->insert('agent_suggesition_videos',$video_data);

			if($task_id) {
			  $_SESSION[error]['msg'] = '<font color="green">Suggestion Category has been added successfully</font>';  
			}else {
			  $_SESSION[error]['msg'] = '<font color="red">Sorry, Please Try Again.</font>';  
			}
		 }else {
			$_SESSION[error]['msg'] = '<font color="red">Sorry, Suggestion Category Already Exists.</font>'; 
		 }
		  
		  echo "<script>window.location.href='add_new_suggestion_category.php';</script>";
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
         <div class="pull-left"> Add New Suggestion Category </div>
         <div class="pull-right">
            <!--<a href="view_suggestion_category.php" class="btn-success"><i class="fa fa-eye"></i> View Suggestion Category </a>-->
            <a href="add-suggestion-keyword.php" class="btn-success"><i class="fa fa-eye"></i> Add New Suggestion </a>
         </div>
         <div class="clearfix"></div>
      </div>
      
      <div class="clearfix"></div>
      <form action="" method="post" enctype="multipart/form-data">
      <fieldset>
      <legend style="color:#F00; font-weight:bold;">Add New Category</legend>
      <div class="form-container clearfix">
        <ul class="fields">
          <li>
           <span class="lable">Enter Suggestion Category Name <span class="required" style="color:#F00;">*</span></span>
           <input name="suggestion_category_name" id="suggestion_category_name" value="" type="text" placeholder="Enter New Suggestion Category " required/>
          </li>

          <li>
            <span class="lable">Enter Video Url Name <span class="required" style="color:#F00;">*</span></span>
            <input name="video_url" id="video_url" value="" type="text" placeholder="Enter New Video Url" required/>
          </li>
           
          <div class="spacer"></div>
         </ul>  
        </div>
      <div class="spacer"></div>
      </fieldset>
        <div class="spacer"></div>
        <div align="center"><button name="submit">SUBMIT</button></div>
        <div class="spacer"></div>
     </form>
     </div>
    </div>
    <!-- add branch office form -->
</div>


<?php //include('../includes/agent-footer.php'); ?>
<?php include_once('toolbox.php'); ?>
<?php include_once('common_file.php'); ?>
</body>
</html>