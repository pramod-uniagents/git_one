<?php 
		require_once("includes/config.php");
		require_once("includes/function.php");
		require_once("class/classDb.php");
		require_once("class/agentClass.php");
		$db = new Database();
		$objAgent = new agent();
		
		
		
		require_once("class/commonClass.php");
		$objCommon = new common();
		
		
		$sql = "SELECT GROUP_CONCAT( id ) as all_appl_id FROM application WHERE COALESCE(`agent_id`,0)  GROUP BY agent_id";
		$db->query($sql);
		$record = $db->fetch();
		
		// $application_full_detail = 	$objAgent->application_full_detail(2);
		// di($record);
		foreach($record as $key=>$value){
			
			
			
		pr($value['all_appl_id']);


		


		// pr($total_application);
		
		
		$sql_appl = "SELECT 
		
				application.id,
				application.agent_id,
				application.purpose_doc,
				application.additional_docs,
				application.additional_docs_1,
				application.additional_docs_2,
				application.additional_docs_3,
				application.additional_docs_4,
				application.applicant_img,
				application.copy_of_proof,

				application_exam_summary.ielts_scanned_doc,
				application_exam_summary.toefl_scanned_doc,
				application_exam_summary.pte_scanned_doc,
				application_exam_summary.gmat_scanned_doc,
				application_exam_summary.others_doc,
				application_work_experience.scanned_doc_1,
				application_work_experience.scanned_doc_2,
				application_work_experience.scanned_doc_3,
				application_work_experience.scanned_doc_4,
				application_work_experience.scanned_doc_5 
				
				FROM application  
				LEFT JOIN application_exam_summary ON application.id=application_exam_summary.application_id
				LEFT JOIN application_work_experience ON application.id=application_work_experience.application_id WHERE application.id IN ({$value['all_appl_id']}) ";
		
		
		$db->query($sql_appl);


		$total_application_detail = $db->fetch();
		
		// echo $db->last_query();
		
		// IN (9,30,29,28,65,64,63,62,61,60,59,58,57,56,55,1,2,5)
		// di($total_application_detail);
		
		

		$total_file_size = 0;


		foreach($total_application_detail as $appl_key=>$appl_val){


			// pr($appl_val);


			if(file_exists(DOC_ROOT."/purpose_doc/".$appl_val['purpose_doc']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/purpose_doc/".$appl_val['purpose_doc']);


			


			if(file_exists(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs']);


			


			if(file_exists(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs_1']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/appl_additional_docs/".$appl_val[0]['additional_docs_1']);


			


			if(file_exists(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs_2']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs_2']);


			


			if(file_exists(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs_3']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs_3']);


			


			if(file_exists(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs_4']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/appl_additional_docs/".$appl_val['additional_docs_4']);


			


			if(file_exists(DOC_ROOT."/exam_summ_doc/".$appl_val['ielts_scanned_doc']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/exam_summ_doc/".$appl_val['ielts_scanned_doc']);


			


			if(file_exists(DOC_ROOT."/exam_summ_doc/".$appl_val['toefl_scanned_doc']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/exam_summ_doc/".$appl_val['toefl_scanned_doc']);


			


			if(file_exists(DOC_ROOT."/exam_summ_doc/".$appl_val['pte_scanned_doc']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/exam_summ_doc/".$appl_val['pte_scanned_doc']);


			


			if(file_exists(DOC_ROOT."/exam_summ_doc/".$appl_val['gmat_scanned_doc']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/exam_summ_doc/".$appl_val['gmat_scanned_doc']);


			


			if(file_exists(DOC_ROOT."/exam_summ_doc/".$appl_val['others_doc']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/exam_summ_doc/".$appl_val['others_doc']);


			


			if(file_exists(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_1']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_1']);


			


			if(file_exists(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_2']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_2']);


			


			if(file_exists(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_3']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_3']);


			


			if(file_exists(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_4']))


			$total_file_size = $total_file_size + filesize(DOC_ROOT."/work_doc/".$appl_val['scanned_doc_4']);


			


			


			


			// Tracking document 


			$track_record = $objAgent->detail_info_with_limit('application_tracking', '', '', array('application_id'=>$appl_val['id']), 'application_tracking_id','desc');


			


			// pr($track_record);


			foreach($track_record as $track_key=>$track_value) {


				if(file_exists(DOC_ROOT."/appl_status_doc/".$track_value['document']))


				$total_file_size = $total_file_size + filesize(DOC_ROOT."/appl_status_doc/".$track_value['document']); 


			}


			


			$reminder_email_record = $objAgent->detail_info_with_limit('reminder_email', '', '', array('application_id'=>$appl_val['id']), 'id','desc');


			


			foreach($reminder_email_record as $admin_note_key=>$admin_note_value) {


				if(file_exists(DOC_ROOT."/reminder_mail/".$admin_note_value['document']))


				$total_file_size = $total_file_size + filesize(DOC_ROOT."/reminder_mail/".$admin_note_value['document']); 


			}


			


			 


		}


		


		$agent_info = $objAgent->detail_info_with_limit('agent', '', '', array('agentId'=>$appl_val['agent_id']));


			


		// pr($agent_info[0]['agentLogo']);  di();


		if(file_exists(DOC_ROOT."/agent_logo/".$agent_info[0]['agentLogo']))
		$total_file_size = $total_file_size + filesize(DOC_ROOT."/agent_logo/".$agent_info[0]['agentLogo']);

		echo $total_file_size.'<br>';
		}
		
		
		
		
		