<?php

require_once("../includes/config.php");
require_once("../includes/function.php");

require_once("../class/classDb.php");
$db = new Database();

require_once("../class/agentClass.php");
$objAgent = new agent();

require_once("../class/commonClass.php");
$objCommon = new common();

require_once("../class/admin.php");
$objAdmin = new admin();

require_once("../class/classUniagentDb.php");
$uniagentDb = new UniagentDatabase();

require_once("../class/globalClass.php");
$globalDB = new globalClass();

// To check agent is login or not
$objAdmin->check_admin_login();


$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);


$mobile_app_users_array = $objAdmin->mobile_app_users_array;


$crm_uses_types_array = $objAdmin->crm_uses_types_array;


$status_array = $objAdmin->status_array;


$arr_heading = array('Sr. no',
    'Agency Name',
    'Country Name',
    'CRM Version',
    'Active/Expired',
    'Total Counsellor',
    'Active Counsellor',
    'InActive Counsellor'
);

$csv_content = '';

foreach ($arr_heading as $heading) {
    $csv_content .= '"' . $heading . '",';
}

$csv_content .= "\n";


// pr($studentList);
$db->debug = true;

$sql = $_SESSION['download_agents_csv_query'];
$sql .= ' limit 2000';
$db->query($sql);

//echo $sql;


$record = $db->fetch();

/*pr($sql);
pr($record);

exit;*/

foreach ($record as $key => $value) {



    $agentId = $value['agentId'];


   
    $sql = "SELECT * FROM `counselor` where agent_id = $agentId";
    $db->query($sql);
    $record = $db->execute();
    $totalUserCount = $db->affected_rows;





    $sql = "SELECT * FROM `counselor` where agent_id = $agentId and status = 'Y'";
    $db->query($sql);
    $record = $db->execute();
    $activeUserCount = $db->affected_rows;





    $sql = "SELECT * FROM `counselor` where agent_id = $agentId and status = 'N'";
    $db->query($sql);
    $record = $db->execute();
    $inActiveUserCount = $db->affected_rows;




    $country = $value['country'];


    $sql = "SELECT * FROM `country` where country_id  = $country";
    $db->query($sql);
    $countryRecord = $db->fetch_first();
    $countryName = $countryRecord['short_name'];










    
    $activeExpired = 'Expired';

   if ($value['valid_till'] >= date('Y-m-d')) {
    
            $activeExpired = 'Active';
    }
    


    
    $Version = 'Lite';
    
       if ($value['crm_uses_type'] == 'F') {
        
                $Version = 'Full';
   }



    
    $counter  =  $key + 1;

    $csv_content .= '"' . $counter . '",';
    $csv_content .= '"' . $value['agencyName'] . '",';
    $csv_content .= '"' . $countryName . '",';
    $csv_content .= '"' . $Version . '",';
    $csv_content .= '"' . $activeExpired . '",';
    $csv_content .= '"' . $totalUserCount . '",';
    $csv_content .= '"' . $activeUserCount . '",';
    $csv_content .= '"' . $inActiveUserCount . '",';

    $csv_content .= "\n";

}

// di();

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="agents.csv"');

echo $csv_content;