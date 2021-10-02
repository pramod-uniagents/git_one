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
	
?>

<?php include('../includes/admin-header.php'); ?>
<?php include('../includes/banner.php'); ?>
<?php include('../includes/admin-left-panel.php'); ?>


<div class="right-panel column">
  <?php include('../includes/admin_login_section.php'); ?>
   
  <!-- main-content -->
  <div class="form-container">
      <div class="pull-left"><div class="form-head"> View Feedback Details</div></div>
      <table width="100%" border="0" class="table" cellpadding="0" cellspacing="0">
        <tr align="left">
          <th width="50">S.No.</th>
          <th width="200">Feedback Posted By</th>
          <th>Suggestion</th>
          <th >Feedback Message</th>
          <th width="100"> User Satisfaction </th>
          <th width="130">Add Date</th>
        </tr>
          <?php
				$suggestion_id 				= decrypt($_REQUEST['suggestion_id']);
			
			//======== Suggesition id with Suggestion Name ========//
				$suggestion_keyword_query	= "select * from agent_suggesition_keyword order by id desc";
				$db->query($suggestion_keyword_query);
				$suggestion_keyword_data 	= $db->fetch();
				
				$suggestion_array 			= array();
				foreach($suggestion_keyword_data as $key=>$suggestion_value) {
					$suggestion_array[$suggestion_value['id']] = $suggestion_value['suggesition'];
				}
				
			//========= End Code ========//
				$feedback_query = "select * from video_help_feedback where suggestion_id='$suggestion_id'";
				$where 		  	= " order by id desc";
				$sql 			= $feedback_query.$where;
		   
		   //======= Total Records Count =======// 
			    $db->query($sql);
			    $db->execute();
			    $total_record = $db->affected_rows;
		   //======= End Count =======//
			
		  //========= Pagination Code ======//
			    $limit_per_page = 50;
			    if (isset($_GET['page']) && !empty($_GET['page']))
				  $page_no = $_GET['page'];
			    else
				  $page_no = 1;
		  
			    $start_limit = ($page_no - 1) * $limit_per_page;
			 
		   //======== Total Record Fetch =======//	
			    $db->limit($limit_per_page, $start_limit);
			    $db->query($sql);
			    $all_record = $db->fetch();
				
			    //pr($all_record);
			    //echo $db->last_query();
               
		   if(count($all_record)>=1) {
			   $i = $start_limit + 1;
			   foreach($all_record as $key=>$feedback_val) {
          ?>
          <tr>
              <td width="5"><?php echo $i; ?></td>
              <td>
              <span style='color: #F6F'></span>
              <?php 
				  $posted_by ="";
				  if($feedback_val['feedback_by_type']=='S'){
					  $posted_by = $objCommon->detail_info_with_limit('agent','','',array('agentId'=>$feedback_val['feedback_by']));
					  echo ucfirst($posted_by[0]['agencyName']). "<br> <span style='color:#F6F'>( Super Admin )</span>";
				  }else if($feedback_val['feedback_by_type']=='B'){
					  $posted_by = $objCommon->detail_info_with_limit('branches','','',array('branch_id'=>$feedback_val['feedback_by']));
					  echo ucfirst($posted_by[0]['name']). "<br> <span style='color:#F6F'>( Branch Office ) </span>";
				  }else if($feedback_val['feedback_by_type']=='C'){
					  $posted_by = $objCommon->detail_info_with_limit('counselor','','',array('id'=>$feedback_val['feedback_by']));
					  echo ucfirst($posted_by[0]['name']). "<br> <span style='color:#F6F'>( Counsellor ) </span>";
				  }else if($feedback_val['feedback_by_type']=='F'){
					  $posted_by = $objCommon->detail_info_with_limit('front_office','','',array('id'=>$feedback_val['feedback_by']));
					  echo ucfirst($posted_by[0]['name']) . "<br> <span style='color:#F6F'>( Front Office ) </span>";
				  }else if($feedback_val['feedback_by_type']=='P'){
					  $posted_by = $objCommon->detail_info_with_limit('processing_office','','',array('id'=>$feedback_val['feedback_by']));
					  echo ucfirst($posted_by[0]['name']) . "<br> <span style='color:#F6F'>( Processing Office ) </span>";
				  }else if($feedback_val['feedback_by_type']=='M'){
					  $posted_by = $objCommon->detail_info_with_limit('commission_manager','','',array('id'=>$feedback_val['feedback_by']));
					  echo ucfirst($posted_by[0]['name']) . "<br> <span style='color:#F6F'>( Commission Manager ) </span>";
				  }else {
					  $posted_by ="";
				  }
				?>
              </td>
              <td><?php echo $suggestion_array[$feedback_val['suggestion_id']]; ?> </td>
              <td><?php echo $feedback_val['feedback_message']; ?></td>
              <td><?php echo $feedback_val['video_usefull_or_not']; ?></td>
              <td><?php echo $feedback_val['add_date']; ?></td>
          </tr>
          <?php $i++; } ?>
          
          <?php }else { ?>
          <tr><td colspan="7"><div id="error_msg" style="padding: 10px 0;" ><font color="red">No Records.</font></div></td></tr>
          <?php } ?>
          </table>
          
          <div class="pagination clearfix">
		  	<?php 
				$page_name = "view-feedback.php?suggestion_id=".urlencode($_REQUEST['suggestion_id'])."";
				echo $objCommon->getPaginationLinks($page_no, $total_record, $limit_per_page, $page_name); 
			?>
		  </div>
      
          <div class="clearfix"></div>
          <div class="spacer"></div>
  <!-- <span class="quick-action" id="preview">Preview Document</span> -->
  </div>
	<!-- main-content -->

</div>
<!-- right body -->
<div class="clearfix"></div>

<?php include_once('toolbox.php');   ?>
<?php include_once('common_file.php');   ?>
</body>
</html>