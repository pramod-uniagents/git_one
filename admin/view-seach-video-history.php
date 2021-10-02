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
       <div class="pull-right"><a href="view-suggestion-log.php" class="btn-success"><i class="fa fa-eye"></i> View Video Search History </a></div>
       <div class="clearfix"></div>
      </div>
      
      <table width="100%" border="0" class="listing">
        <tr align="left">
          <th width="50">S.No.</th>
          <th>Total Click</th>
          <th>Suggestion Video</th>
          <th>Add Date</th>
        </tr>
          <?php
			 $video_history_query = "select suggestion_category_id,count(*) as Total_Click,add_date from suggestion_search_history where suggestion_category_id>0 
			 						 group by suggestion_category_id";
										  
			 //$where 		  	  = " where keywords.status='Y' order by keywords.id desc";
			 $sql 				  = $video_history_query;
		   
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
			   foreach($all_record as $key=>$sugggestion_val) {
				   $video_histroy_url   = 'view-complete-video-history.php?suggestion_category_id='.urlencode(encrypt($sugggestion_val['suggestion_category_id']));
          ?>
              <tr>
                <td width="50"><?php echo $i; ?></td>
                <td><a href="<?php echo $video_histroy_url; ?>"><?php echo $sugggestion_val['Total_Click']; ?></a></td>
                <td>
                    <?php 
						//echo $sugggestion_val['suggestion_category_id'];
                        $vidoe_name 	= "";
                        if($sugggestion_val['suggestion_category_id']<=0) {
                            $vidoe_name = "No Video";
                        }else {
                            $vidoe_name = 	$vidoe_deatils[$sugggestion_val['suggestion_category_id']];
                        }
                        echo $vidoe_name;
                    ?>
                </td>
                <td><?php echo $sugggestion_val['add_date']; ?></td>
              </tr>
          <?php $i++; } ?>
          <?php }else { ?>
          	 <tr><td colspan="7"><div id="error_msg" style="padding: 10px 0;" ><font color="red">No Records.</font></div></td></tr>
          <?php } ?>
          </table>
           
          <div class="pagination clearfix">
		  	<?php echo $objCommon->getPaginationLinks($page_no, $total_record, $limit_per_page, $page_name);  ?>
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