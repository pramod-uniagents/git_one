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
      
      <div class="form-sub-head"> 
       <div class="pull-left"> View Suggestion Category Name </div>
       <div class="pull-right"><a href="view_suggestion.php" class="btn-success"><i class="fa fa-eye"></i> View Suggestion </a></div>
       <div class="clearfix"></div>
      </div>
      
      <table width="100%" border="0" class="listing">
        <tr align="left">
          <th width="50">S.No.</th>
          <th>Category Name</th>
          <th>Video Url</th>
          <th>Add Date</th>
        </tr>
          <?php
			  //$suggestion_category_query = "select * from suggestion_category";
			  
			  $suggestion_category_query = "select category.id,category.name,category.update_date,video.video_mp4 from suggestion_category as category 
										    INNER JOIN agent_suggesition_videos as video ON category.id=video.suggestion_category_id";
											
			  $where 		  			 = " order by category.id desc";
			  $sql 						 = $suggestion_category_query.$where;
		   
		   //======= Total Records Count =======// 
			  $db->query($sql);
			  $db->execute();
			  $total_record = $db->affected_rows;
		   //======= End Count =======//
			
		  //========= Pagination Code ======//
			  $limit_per_page = 50;
			  if (isset($_GET['page']) && !empty($_GET['page']))
				$page_no 	= $_GET['page'];
			  else
				$page_no 	= 1;
		
			  $start_limit 	= ($page_no - 1) * $limit_per_page;
			 
		   //======== Total Record Fetch =======//	
			  $db->limit($limit_per_page, $start_limit);
			  $db->query($sql);
			  $all_record = $db->fetch();
               
		     if(count($all_record)>=1) {
			   $i = $start_limit + 1;
			   foreach($all_record as $key=>$category_val) {
          ?>
          <tr>
            <td width="5"><?php echo $i; ?></td>
            <td><?php echo $category_val['name']; ?></td>
            <td><?php echo $category_val['video_mp4']; ?></td>
            <td><?php echo $category_val['update_date']; ?></td>
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