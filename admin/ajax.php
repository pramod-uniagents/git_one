<?php
require_once("../includes/config.php");
require_once("../includes/function.php");
require_once("../class/classDb.php");
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

    case "agent": {


        $db->where(array('username' => $username));
        $db->from('agent');
        $record = $db->fetch();
        // echo $db->last_query();
        // pr($record);
        $content = "";


        if (count($record) > 0) {

            $content = 'exist';

        } else {

            $content = "not_exist";

        }


        $msg = array("content" => $content);
        echo json_encode($msg);

    }

        break;

    case "change_common_status": {


        if ($table_id > 0) {


            $db->where(array($field => $table_id));
            // print_r($data);
            $update_status = $db->update($table, $data);
            // echo $db->last_query();
            // pr($update_status->affected_rows);


            if ($table == 'agent' && ($data['agentStatus'] || $data['crm_uses_type'])) {


                $sql = " select * from agent where agentId =  $table_id ORDER  by agentId ";
                $db->query($sql);
                $agent_detail = $db->fetch_first();


                if ($agent_detail['crm_uses_type'] == 'L') {


                    $sql = " select * from branches where agent_id =  $table_id ORDER  by branch_id ";
                    $db->query($sql);
                    $branch_detail = $db->fetch_first();

                }


                if ($agent_detail['crm_uses_type'] == 'L' && empty($branch_detail['branch_id'])) {

                    $data = array(
                        'agent_id' => $agent_detail['agentId'],
                        'name' => $agent_detail['agencyName']

                    );

                    $db->insert('branches', $data);
                }


            }

        }

        $content = "";


        if ($update_status->affected_rows > 0) {

            $content = 'updated';

        } else {

            $content = "not_updated";

        }


        $msg = array("content" => $content);
        echo json_encode($msg);

    }


        break;


    default:
        echo "Your request goes in default request!";
}


?>
