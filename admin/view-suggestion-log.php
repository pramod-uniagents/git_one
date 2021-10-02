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
	
	if(isset($_REQUEST['reset'])){
		unset($_SESSION['search']);
	}

	extract($_REQUEST);
	if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Submit') {
	   $_SESSION['search']['orderby'] = $orderby;
	   $_SESSION['search']['keyword'] = trim($keyword);
	}
	
	//======== video array 
	
	$history_name_query = "SELECT suggestion_id,suggestion,COUNT(*) as Total_history FROM suggestion_search_history WHERE suggestion_id>0 GROUP BY suggestion_id";
    $db->query($history_name_query);
	$history_name_data  = $db->fetch();
	
   //========= Not Feedback Where Condition ========//
   $where_not_zero 		= "";
   if(isset($_SESSION['search']['orderby']) AND $_SESSION['search']['orderby']=='not_feedback' ){
	   $where_not_zero .= " where history.suggestion_id='".trim($_SESSION['search']['orderby'])."'";
   }
   //======== End  =======//
	 
	$where = ""; 
	if( isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword']!='' ){
		if($_SESSION['search']['orderby']=='total_feedback_desc' || $_SESSION['search']['orderby']=='total_feedback_asc') {
			
			$suggestion_id_query = "SELECT id FROM agent_suggesition_keyword WHERE suggesition LIKE '%{$_SESSION['search']['keyword']}%'";
			$db->query($suggestion_id_query);
			$suggestion_id_data  = $db->fetch();
			
			$where .= " where suggestion_id='".$suggestion_id_data['0']['id']."'";
		}else {
			
			$where .= " where history.suggestion='".trim($_SESSION['search']['keyword'])."'";
		}
		
		if(isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword']=='Video Not Available') {
			
			$where = "";
		}
		
	  //========= Not Feedback When kewyword Value inserted ========//
	  
		$where_not_zero   = "";
		if(isset($_SESSION['search']['orderby']) AND $_SESSION['search']['orderby']=='not_feedback' ) {
		 
		 	$where_not_zero .= " and history.suggestion_id='".trim($_SESSION['search']['orderby'])."'";
		}
		
	  //======== End  =======//
	}
	
	 $where .= $where_not_zero;  
	 $order_type = "desc";
	 
	 if(isset($_SESSION['search']['orderby']) AND $_SESSION['search']['orderby']!='' ){
		if($_SESSION['search']['orderby']=="total_query_asc") {
			$order_type = "asc";
		}
		
		if($_SESSION['search']['orderby']=="total_query_asc") {
			$order_type = "asc";
		}
		
		if($_SESSION['search']['orderby']=="total_feedback_asc") {
			$order_type = "asc";
		}
	 }
	//===== 
	 
	//=============== Video Name Value In Array =======//
	
	$vidoe_deatils = $objAgent->suggestion_video_array();
	 
	//=============== Suggestion history count in Array =======//
	$history_name_query = "SELECT suggestion_id,suggestion,COUNT(*) as Total_history FROM suggestion_search_history WHERE suggestion_id>0 GROUP BY suggestion_id";
    $db->query($history_name_query);
	$history_name_data  = $db->fetch();
	
	$history_count_array  		=  array("");
	$final_history_count_array 	= "";
	foreach($history_name_data as $key=>$history_count) {
		$history_count_array[$history_count['suggestion_id']] = array('suggestion'=>$history_count['suggestion'],'count'=>$history_count['Total_history']); 
	}
	
	$final_history_count_array = array_filter($history_count_array);
	
	//=============== Suggestion Feedback count in Array =======//
	$feedback_name_query 	= "select suggestion_id,count(*) as Total_feedback from video_help_feedback group by suggestion_id order by Total_feedback $order_type";
    $db->query($feedback_name_query);
	$feedback_name_query  	= $db->fetch();
	
	$feedback_count_array	=  array("");
	foreach($feedback_name_query as $key=>$feedback_count) {
		$feedback_count_array[$feedback_count['suggestion_id']] = $feedback_count['Total_feedback']; 
	}
	
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
       <div class="pull-right"><a href="view-seach-video-history.php" class="btn-success"><i class="fa fa-eye"></i> View Video Search History </a></div>
       <div class="clearfix"></div>
      </div>
      
      
      <div class="main-content search-container">
      <form method="post" action="view-suggestion-log.php">
        <div class="form-container">
          <fieldset class="search-box">
          <ul class="fields">
           <li>
              <span class="lable">Total Query Sort By</span>
              
              <!--<input type="text" name="graterthan" id="graterthan" value="<?php echo $_SESSION['search']['graterthan']; ?>" placeholder="Enter Total Query Grater Than" required1>-->
              
              <?php 
                  $orderby = array('Total Query Decending'=>'total_query_desc',
								   'Total Query Ascending'=>'total_query_asc',
								   'Total Feedback Decending'=>'total_feedback_desc',
								   'Total Feedback Ascending'=>'total_feedback_asc',
								   'Not Submit Feedback'=>'query_available_not_feedback',
								   'Video Not Available'=>'video_not_available');
              ?>
              <select name="orderby" id="orderby">
                <?php foreach($orderby as $key=>$value) { ?>
                  <option value="<?php echo $value; ?>" <?php if($_SESSION['search']['orderby']==$value) { ?> selected <?php } ?>><?php echo $key; ?></option>
                <?php } ?>
              </select> 
           </li>
          
           <li>
             <span class="lable">Suggestion Name</span>
             <input type="text" name="keyword" id="keyword"  value="<?php echo $_SESSION['search']['keyword']; ?>" placeholder="Enter Suggestion Name" required1>
           </li>
           
           <li>
             <button name="submit" value="Submit"><i class="fa fa-save"></i> Search </button>
             <button type="button" onClick="if(confirm('Are You sure you want to reset your search preference?'))window.location.href='?reset=reset_search';"><i class="fa fa-refresh"></i> Reset</button>
           </li>
           
          </ul>
            
          </fieldset>
        </div>
      </form>
  </div>
      
  <table width="100%" border="0" class="listing">
    <tr align="left">
      <th width="50">S.No.</th>
      <th>Total Query</th>
      <th>Total Feedback</th>
      <th>Suggestion Name</th>
      <th>Video Name</th>
    </tr>
      <?php
         if($_SESSION['search']['orderby']=="total_feedback_desc" || $_SESSION['search']['orderby']=="total_feedback_asc") {
			 
             $history_query = "SELECT video_id,suggestion_id,COUNT(*) AS Totla_feedback FROM video_help_feedback  ";
             $where 	   .= " where suggestion_id>0 GROUP BY suggestion_id ORDER BY Totla_feedback $order_type";
		 }
         else if($_SESSION['search']['orderby']=="query_available_not_feedback" || $_SESSION['search']['orderby']=="query_available_not_feedback") {
             $history_query  = "SELECT history.suggestion,history.suggestion_id,history.suggestion_category_id,COUNT(history.id) AS Total_Click,video.video_mp4 
             FROM suggestion_search_history AS history LEFT JOIN agent_suggesition_videos AS video ON history.suggestion_category_id=video.suggestion_category_id ";
             
             $where_query 	 = "where";
             if( isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword']!='' ) {
                $where_query = "and"; 
             }
             
             $where 	   .= " $where_query history.suggestion_id NOT IN(SELECT DISTINCT(suggestion_id) FROM video_help_feedback) GROUP BY history.suggestion 
			 					ORDER BY Total_Click DESC";
								
         }else if($_SESSION['search']['orderby']=="video_not_available") {
			 
			 $history_query = " SELECT history.suggestion,history.suggestion_id,history.suggestion_category_id,COUNT(history.id) AS Total_Click,
                          		video.video_mp4 FROM suggestion_search_history AS history LEFT JOIN agent_suggesition_videos AS video ON 
                          		history.suggestion_category_id=video.suggestion_category_id where video.video_mp4 IS NULL ";
                          
			 $where .= " where (history.suggestion_id>0) group by history.suggestion order by Total_Click $order_type ";
			 
		 }else {
			 
             $history_query = "SELECT history.suggestion,history.suggestion_id,history.suggestion_category_id,COUNT(history.id) AS Total_Click,video.video_mp4
             FROM suggestion_search_history AS history LEFT JOIN agent_suggesition_videos AS video ON history.suggestion_category_id=video.suggestion_category_id";
			 
             $where .= " where (history.suggestion_id>0) group by history.suggestion order by Total_Click $order_type";
         }
         
         $sql = $history_query.$where;
       
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
        
		 //pr($all_record);
		 
         // echo $db->last_query();
         // pr($all_record);
         // die(); 
         
         if(count($all_record)>=1) {
           $i = $start_limit + 1;
           foreach($all_record as $key=>$sugggestion_val) 
           {
               //========== Create url =======//
               $total_history_count = $video_name = $suggestion_name = "";
               if($_SESSION['search']['orderby']=="total_feedback_desc" || $_SESSION['search']['orderby']=="total_feedback_asc") {
                   
                  $video_name 			= $vidoe_deatils[$sugggestion_val['video_id']];
				  
				  if(empty($video_name)) {
					  
					 $sql="SELECT suggestion_category_id FROM agent_suggesition_keyword where id='".$sugggestion_val['suggestion_id']."' order by id desc limit 1 ";
					 $db->query($sql);
         			 $suggestion_category_id = $db->fetch_first();
					 
					 if($suggestion_category_id['suggestion_category_id']>0) {
						 
						$video_name = $vidoe_deatils[$suggestion_category_id['suggestion_category_id']];
					 }
				  }
				  
                  $suggestion_name 		= $final_history_count_array[$sugggestion_val['suggestion_id']]['suggestion'];
                  $total_history_count	= $final_history_count_array[$sugggestion_val['suggestion_id']]['count'];
                  
               }else {
                   
                   $total_history_count = $sugggestion_val['Total_Click'];
                   $suggestion_name 	= $sugggestion_val['suggestion'];
                   
                   $video_name 			= "Video Not Available";
                   if(!empty($sugggestion_val['video_mp4'])) {
                       $video_name 		= $sugggestion_val['video_mp4'];
                   }
                   
                   //========== Video Not Available searching condition ========//
                   if($_SESSION['search']['keyword']=="Video Not Available") {
                     if(!empty($sugggestion_val['video_mp4'])) {
                         continue;
                     }
                   }
               }
               
               $feedbackurl   = 'view-feedback.php?suggestion_id='.urlencode(encrypt($sugggestion_val['suggestion_id']));
               
               $totalqueryurl = 'complete-view-suggestion-log.php?suggestion_id='.urlencode(encrypt($sugggestion_val['suggestion_id'])).'&suggestion_name='.urlencode(encrypt($suggestion_name));
               
              //echo $totalqueryurl = 'complete-view-suggestion-log.php?suggestion_id='.$sugggestion_val['suggestion_id'].'&suggestion_name='.$suggestion_name; 
      ?> 
          <tr>
            <td width="50"><?php echo $i; ?></td>
            <td><a href="<?php echo $totalqueryurl; ?>" title="View Details"><?php echo $total_history_count; ?>
            </a></td>
            <td>
              <?php  
                $feedback = 0;
                if(!empty($feedback_count_array[$sugggestion_val['suggestion_id']])) {
					
                    $feedback = $feedback_count_array[$sugggestion_val['suggestion_id']];
                    echo "<a href=$feedbackurl title='View Feedback Details'>$feedback</a>";
                }else {
				
					echo $feedback;
                }
              ?>
            </td>
            <td><a href="<?php echo $totalqueryurl; ?>" title="View Query Details"><?php echo $suggestion_name; ?></a></td>
            <td><?php echo $video_name; ?></td>
          </tr> 
      <?php $i++; } ?>
      <?php }else { ?>
         <tr><td colspan="7"><div id="error_msg" style="padding: 10px 0;" ><font color="red">No Records.</font></div></td></tr>
      <?php } ?>
      </table>
           
      <div class="pagination clearfix">
        <?php echo $objCommon->getPaginationLinks($page_no, $total_record, $limit_per_page, $page_name); ?>
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