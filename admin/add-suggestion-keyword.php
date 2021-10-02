<?php

	require_once("../includes/config.php");
	require_once("../includes/function.php");
	
	require_once("../class/classDbDemo.php");
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
		foreach ($_POST['suggestion_name'] as $value)
			$suggestion_name .= "'$value',";
		
		$suggestion_name = rtrim($suggestion_name, ',');
		
		
		$suggention_exists_name = "";
		
		$sql  = "SELECT * FROM agent_suggesition_keyword WHERE suggesition IN($suggestion_name) and suggestion_category_id='".$_POST['suggestion_category_id']."'";
		$objAgent->query($sql);
	    $suggestion_exists 	= $objAgent->fetch();
		foreach($suggestion_exists as $key => $value) {
			$suggention_exists_name .= $value['suggesition'].',';
		}
		
		if(count($suggestion_exists)>0 and !empty($suggestion_exists)) {
			
			$_SESSION['error']['msg'] = "<font color='red'>($suggention_exists_name) Already Exists. Please Enter Correct Suggestion Query.</font>";
		}
		if($_POST['suggestion_category_id']=='' || $_POST['suggestion_category_id']<=0) {
			
			$_SESSION['error']['msg'] = '<font color="red">Please select suggestion category name!</font>';
		}
		if(trim($_POST['suggestion_name']['0'])=="") {
			
			$_SESSION['error']['msg'] = '<font color="red">Please enter first suggestion name</font>';
		}
		
		if(empty($_SESSION['error']['msg'])){
			foreach($_POST['suggestion_name'] as $key=>$value) {
			  $data 	= "";
			  if(!empty($value)) {
				$data 	= array(
							'suggesition' =>$value,
							'suggestion_category_id' =>$_POST['suggestion_category_id'],
							'status' => 'Y',
							'add_date' => 'now()',
							'update_date' => 'now()'
						  );
				$task_id = $db->insert('agent_suggesition_keyword',$data);
			  }
			}
			
			if($task_id) {
				
			  $_SESSION['error']['msg'] = '<font color="green">Suggestion has been added successfully</font>';  
			}else {
				
			  $_SESSION['error']['msg'] = '<font color="red">Sorry, Please Try Again.</font>';  
			}
			
			echo "<script>window.location.href='add-suggestion-keyword.php';</script>";
			exit;
	  	}
   }
   
   if(isset($_GET['category_id']) && $_GET['category_id']!='') {
	   
	   $category_id 	= $_GET['category_id'];
	   
	   $sql   			= "SELECT * FROM agent_suggesition_videos WHERE status='Y' and suggestion_category_id='$category_id' order by id asc";
	   $objAgent->query($sql);
	   $video_list    	= $objAgent->fetch_first();
	   
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
      
      <div class="form-sub-head"> 
       <div class="pull-left">Add New Suggestion Keyword</div>
       <div class="pull-right"><a href="add_new_suggestion_category.php" class="btn-success"><i class="fa fa-eye"></i> Add New Suggestion Category</a></div>
       <div class="clearfix"></div>
      </div>
     
      <div class="clearfix"></div>
      
      <?php if (isset($_SESSION['error']['msg'])) { ?>
          <div id="error_msg"><?php echo $_SESSION['error']['msg']; unset($_SESSION['error']['msg']); ?></div>
          <div class="clearfix"></div>
          <div class="clearfix"></div>
          <?php unset($_SESSION['error']['msg']);
      } ?>
      
      <fieldset>
      
      <form action="" method="post" enctype="multipart/form-data" onSubmit="javascript:return form_suggestion_submit();">
      <ul class="fields">
        <li>
          <span class="lable">Select Suggestion Categroy <span class="required" style="color:#F00;">*</span></span>
          <label class="select">
            <select name="suggestion_category_id" id="suggestion_category_id" required onChange="javascript:get_category_id(this.value)">
              <option value="">Select Suggestion Categroy Name</option>
              <?php
                 $suggestion_category_query   = "SELECT * FROM suggestion_category WHERE status='Y' order by id asc";
                 $objAgent->query($suggestion_category_query);
                 $suggestion_category_data    = $objAgent->fetch();
				 
                 foreach($suggestion_category_data as $key=>$category_data) {
              ?>
              <option value="<?php echo $category_data['id']; ?>" <?php if($_GET['category_id']==$category_data['id']) { ?> selected <?php } ?>><?php echo $category_data['name']; ?></option>
              <?php } ?>
            </select>
          </label>   
        </li>

        <?php if(!empty($video_list['video_mp4'])) { ?>		
        <li>
          <span class="lable"> Suggestion Video</span>
          <input name="video" id="video" value="<?php echo $video_list['video_mp4']; ?>" type="text" readonly/>
        </li>
        <?php }?>

        <li>
          <span class="lable"> Suggestion Query1 <span class="required" style="color:#F00;">*</span></span>
          <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['0']; ?>" type="text" placeholder="Add Name Suggestion" required/>
        </li> 
        
        <li>
         <span class="lable"> Suggestion Query2 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['1']; ?>" type="text" placeholder="Add Name Suggestion" />
        </li> 
        
        <li>
         <span class="lable"> Suggestion Query3 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['2']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li> 
        
        <li>
         <span class="lable"> Suggestion Query4 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['3']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li> 
        
        <li>
         <span class="lable"> Suggestion Query5 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['4']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li>
        
        <li>
         <span class="lable"> Suggestion Query6 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['5']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li>
        
        <li>
         <span class="lable"> Suggestion Query7 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['6']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li>
         
        <li>
         <span class="lable"> Suggestion Query8 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['7']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li>
        
        <li>
         <span class="lable"> Suggestion Query9 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['8']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li> 
        
        <li>
         <span class="lable"> Suggestion Query10 </span>
         <input name="suggestion_name[]" id="suggestion_name" value="<?php echo $_POST['suggestion_name']['9']; ?>" type="text" placeholder="Add Name Suggestion"/>
        </li> 
        
        <!--<span id="more_text_div"></span>-->
        <!--<input type="hidden" name="status_cnt" id="status_cnt" value="1">
        <li id="add_more_button">
            <a href="javascript:void(0);" onclick="add_more_text();">
            <span style="background:#7dc249; color:  #fff; padding: 6px 10px; border-radius: 8px; position: relative;">
            <i class="fa fa-plus-circle"></i>&nbsp;Add more suggestion</span></a>
        </li>-->
         
        <div class="spacer"></div>
          
      </ul>
      <div align="center">
          <button name="submit">SUBMIT</button>
      </div>
      
      <div class="spacer"></div>
      <div class="spacer"></div>
      <div id="status_li_id"></div>
      </form>
      
      </fieldset>
  </div>
    
    <!-- add branch office form -->

</div>

<!-- right-panel -->

<script>
  /*function add_more_text(){
  
	  var status_cnt = $("#status_cnt").val();
	  status_cnt 	 = parseInt(status_cnt)+1;
	  $("#status_cnt").val(status_cnt);
	  
	  if(status_cnt<11) {
	  
		  $("#more_text_div").append('<li><span class="lable"> Suggestion Keyword Name</span><input name="suggestion_name[]" id="suggestion_name" value="" type="text" placeholder="Add Name Suggestion" /></li>');
		  
		  if(status_cnt==10)
		  $("#add_more_button").hide();
		  
	  }
	  
	  $(".left-panel").height($(".right-panel").height());
  }*/
  
  function get_category_id(cat_id) {
	  
	  window.location = "add-suggestion-keyword.php?category_id="+cat_id;
  }
  
</script>		

<?php //include('../includes/agent-footer.php'); ?>
<?php include_once('toolbox.php'); ?>
<?php include_once('common_file.php'); ?>
</body>
</html>