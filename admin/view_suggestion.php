<?php

  require_once("../includes/config.php");
  require_once("../includes/function.php");
  
  require_once("../class/classDbDemo.php");
  $db 		= new Database();
  
  require_once("../class/agentClass.php");
  $objAgent = new agent();
  
  require_once("../class/commonClass.php");
  $objCommon  = new common();
  
  require_once("../class/admin.php");
  $objAdmin   = new admin();
  
  // To check admin is login or not
  $objAdmin->check_admin_login();
  
  //======== Reset Search Data ======//
  if(isset($_REQUEST['reset'])) {
	  unset($_SESSION['search']);
  }
  
  //===== Getting Form Data ======//
  extract($_REQUEST);
  if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Submit') {
	 $_SESSION['search']['suggestion_name'] = $suggestion_name;
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
       <div class="pull-left"> View Suggestion Name </div>
       <div class="pull-right"><a href="view_suggestion_category.php" class="btn-success"><i class="fa fa-eye"></i> View Suggestion Category </a></div>
       <div class="clearfix"></div>
      </div>
      
      <div class="main-content search-container">
        <form method="post" action="view_suggestion.php">
          <div class="form-container">
            <fieldset class="search-box">
              <ul class="fields">
               <li>
               <span class="lable">Suggestion Name</span>
               <input type="text" name="suggestion_name" id="suggestion_name"  value="<?php echo $_SESSION['search']['suggestion_name']; ?>" placeholder="Enter Suggestion Name" required1>
               </li>
               
               <li>
                  <button name="submit" value="Submit"><i class="fa fa-save"></i> Search </button>
                  <button type="button" onclick="if(confirm('Are You sure you want to reset your search preference?'))window.location.href='?reset=reset_search';">
                  <i class="fa fa-refresh"></i> Reset</button>
               </li>  
              </ul>
             </fieldset>
          </div>
        </form>
  	   </div>
      
      
      <table width="100%" border="0" class="listing">
        <tr align="left">
          <th width="50">S.No.</th>
          <th>Name</th>
          <th>Category Name</th>
          <th>Add Date</th>
          <th>Action</th>
        </tr>
          <?php
		    $where 		= " where keywords.status='Y' "; 
			if( isset($_SESSION['search']['suggestion_name']) AND $_SESSION['search']['suggestion_name']!='' ){
				$where .= " and keywords.suggesition='".trim($_SESSION['search']['suggestion_name'])."' ";	
			}
		  
		    $suggestion_category_query = "select keywords.id,keywords.suggesition,keywords.update_date,category.name,category.id as cat_id 
										  from agent_suggesition_keyword as keywords INNER JOIN suggestion_category as category ON 
										  keywords.suggestion_category_id=category.id";
										
		    $where 		  			  .= "  order by keywords.id desc";
		    $sql 					   = $suggestion_category_query.$where;
		   
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
			   foreach($all_record as $key=>$category_val) {
          ?>
          <tr>
              <td width="5"><?php echo $i; ?></td>
              <td><?php echo $category_val['suggesition']; ?></td>
              <td><?php echo $category_val['name']; ?></td>
              <td><?php echo $category_val['update_date']; ?></td>
              <td>
                 <a href="edit_suggestion.php?keyword_id=<?php echo urlencode(encrypt($category_val['id'])); ?>&category_id=<?php echo urlencode(encrypt($category_val['cat_id'])); ?>&suggestion=<?php echo urlencode(encrypt($category_val['suggesition'])) ?>" target="_blank" title="Edit Details"><i class="fa fa-edit"></i></a>
              </td>
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