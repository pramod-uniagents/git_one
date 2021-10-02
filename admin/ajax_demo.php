<?php 
	require_once("../includes/config.php");
	require_once("../includes/function.php");
	require_once("../class/classDbDemo.php");
	require_once("../class/agentClass.php");
	
	$db = new Database();
	$objAgent = new agent();
	
	require_once("../class/commonClass.php");
	$objCommon = new common();
	
	require_once("../class/branchClass.php");
	$objBranch = new branch();
	
	// print_r($_REQUEST);
	extract($_REQUEST);
	
	
	
	switch ($type) {
	
		case "agent":{
			
		
			$db->where(array('username'=>$username));
			$db->from('agent');
			$record = $db->fetch();
			// echo $db->last_query();
			// pr($record);
			$content = "";
			
			
			if(count($record)>0) {
				
				$content = 'exist';
				
			} else {
			
				$content = "not_exist";
			
			}
			
			
			$msg = array("content"=>$content);
			echo json_encode($msg);
			
			}
			
			break;
			
		case "change_common_status":{
			
		
			$db->where(array($field=>$table_id));
			// print_r($data);
			$update_status = $db->update($table,$data);
			// echo $db->last_query();
			// pr($update_status->affected_rows);
			$content = "";
			
			
			if($update_status->affected_rows>0) {
				
				$content = 'updated';
				
			} else {
			
				$content = "not_updated";
			
			}
			
			
			$msg = array("content"=>$content);
			echo json_encode($msg);
			
			}
			
			
			break;
			
			
		default:
			echo "Your request goes in default request!";
	}

	

?>
