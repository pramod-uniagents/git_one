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
	
	//======== Video Array ========//
	$vidoe_deatils = $objAgent->suggestion_video_array();
	
	$suggestion_id 		= decrypt($_REQUEST['suggestion_id']);
	$suggestion_name 	= decrypt($_REQUEST['suggestion_name']);
?>

<?php include('../includes/admin-header.php'); ?>
<?php include('../includes/banner.php'); ?>
<?php include('../includes/admin-left-panel.php'); ?>

<div class="right-panel column">
  <?php include('../includes/admin_login_section.php'); ?>
   
  <!-- main-content -->
  <div class="form-container">
      
      <div class="form-sub-head"> 
       <div class="pull-left"> View Suggestion Search Histroy </div>
       <div class="pull-right"><a href="view-suggestion-log.php" class="btn-success"><i class="fa fa-eye"></i> View Suggestion Search History </a></div>
       <div class="clearfix"></div>
      </div>
      
      <table width="100%" border="0" class="listing">
        <tr align="left">
          <th width="50">S.No.</th>
          <th>Super Admin</th>
          <th>Search By</th>
          <th>Suggestion</th>
          <th>Video Name</th>
          <th>Add Date</th>
        </tr>
          <?php
		  	 $suggestion_name = addslashes($suggestion_name);
			 $suggestion_history_query = "select * from suggestion_search_history where suggestion_id='$suggestion_id' and suggestion like '$suggestion_name' 
			 							  order by id desc";
										  
			 //$where 		  			= " where keywords.status='Y' order by keywords.id desc";
			 $sql 					    = $suggestion_history_query;
		   
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
			 
		     if(count($all_record)>=1) {
			   $i = $start_limit + 1;
			   foreach($all_record as $key=>$log_val) {
          ?>
              <tr>
                <td width="50"><?php echo $i; ?></td>
                <td>
					<?php 
						$agent_name = $objCommon->detail_info_with_limit('agent','','',array('agentId'=>$log_val['agent_id']));
						echo $agent_name[0]['agencyName'];
                    ?>
                </td>
                <td>
					<?php 
                      //echo $log_val['search_by_type']; 
                      $posted_by ="";
                      if($log_val['search_by_type']=='S'){
                          $posted_by = $objCommon->detail_info_with_limit('agent','','',array('agentId'=>$log_val['search_by']));
                          echo ucfirst($posted_by[0]['agencyName']). "<br> <span style='color:#F6F'>( Super Admin )</span>";
                      }else if($log_val['search_by_type']=='B'){
                          $posted_by = $objCommon->detail_info_with_limit('branches','','',array('branch_id'=>$log_val['search_by']));
                          echo ucfirst($posted_by[0]['name']). "<br> <span style='color:#F6F'>( Branch Office ) </span>";
                      }else if($log_val['search_by_type']=='C'){
                          $posted_by = $objCommon->detail_info_with_limit('counselor','','',array('id'=>$log_val['search_by']));
                          echo ucfirst($posted_by[0]['name']). "<br> <span style='color:#F6F'>( Counsellor ) </span>";
                      }else if($log_val['search_by_type']=='F'){
                          $posted_by = $objCommon->detail_info_with_limit('front_office','','',array('id'=>$log_val['search_by']));
                          echo ucfirst($posted_by[0]['name']) . "<br> <span style='color:#F6F'>( Front Office ) </span>";
                      }else if($log_val['search_by_type']=='P'){
                          $posted_by = $objCommon->detail_info_with_limit('processing_office','','',array('id'=>$log_val['search_by']));
                          echo ucfirst($posted_by[0]['name']) . "<br> <span style='color:#F6F'>( Processing Office ) </span>";
                      }else if($log_val['search_by_type']=='M'){
                          $posted_by = $objCommon->detail_info_with_limit('commission_manager','','',array('id'=>$log_val['search_by']));
                          echo ucfirst($posted_by[0]['name']) . "<br> <span style='color:#F6F'>( Commission Manager ) </span>";
                      }else {
                          $posted_by ="";
                      }
                    ?>
                </td>
                <td><?php echo $log_val['suggestion']; ?></td>
                <td>
					<?php 
						$video_name = "Video Not Available";
						if(!empty($vidoe_deatils[$log_val['suggestion_category_id']])) {
							$video_name = $vidoe_deatils[$log_val['suggestion_category_id']];
						}
						echo $video_name;
					?>
                </td>
                <td><?php echo $log_val['add_date']; ?></td>
              </tr>
          <?php $i++; } ?>
          <?php }else { ?>
          	 <tr><td colspan="7"><div id="error_msg" style="padding: 10px 0;" ><font color="red">No Records.</font></div></td></tr>
          <?php } ?>
          </table>
           
          <div class="pagination clearfix">
		  	<?php 
				$page_name = "complete-view-suggestion-log.php?suggestion_id=".urlencode($_REQUEST['suggestion_id'])."&suggestion_name=".urlencode($_REQUEST['suggestion_name'])."";
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