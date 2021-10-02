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
    'Username',
    'Country Name',
    'Subscription Date',
    'Valid Till',
    'Last Login',
    'Users',
    'Active/Expired'
);

$csv_content = '';

foreach ($arr_heading as $heading) {
    $csv_content .= '"' . $heading . '",';
}

$csv_content .= "\n";


// pr($studentList);
$db->debug = true;

$sql = $_SESSION['download_agents_csv_query'];
$sql .= ' limit 1000';
$db->query($sql);

//echo $sql;


$record = $db->fetch();

/*pr($sql);
pr($record);

exit;*/

foreach ($record as $key => $value) {



    $agentId = $value['agentId'];


    $userCount =  0 ;

    $sql = "SELECT * FROM `branches` where agent_id = $agentId";
    $db->query($sql);
    $record = $db->execute();
    $userCount += $db->affected_rows;




    $sql = "SELECT * FROM `counselor` where agent_id = $agentId";
    $db->query($sql);
    $record = $db->execute();
    $userCount += $db->affected_rows;




    $sql = "SELECT * FROM `front_office` where agent_id = $agentId";
    $db->query($sql);
    $record = $db->execute();
    $userCount += $db->affected_rows;




    $sql = "SELECT * FROM `processing_office` where agent_id = $agentId";
    $db->query($sql);
    $record = $db->execute();
    $userCount += $db->affected_rows;




    $sql = "SELECT * FROM `commission_manager` where agent_id = $agentId";
    $db->query($sql);
    $record = $db->execute();
    $userCount += $db->affected_rows;




    $country = $value['country'];


    $sql = "SELECT * FROM `country` where country_id  = $country";
    $db->query($sql);
    $countryRecord = $db->fetch_first();
    $countryName = $countryRecord['short_name'];






    $activeExpired = 'Expired';


    if ($value['valid_till'] >= date('Y-m-d')) {

        $activeExpired = 'Active';
    }
$counter  =  $key + 1;

    $csv_content .= '"' . $counter . '",';
    $csv_content .= '"' . $value['agencyName'] . '",';
    $csv_content .= '"' . $value['username'] . '",';
    $csv_content .= '"' . $countryName . '",';
    $csv_content .= '"' . $value['subscription_date'] . '",';
    $csv_content .= '"' . $value['valid_till'] . '",';
    $csv_content .= '"' . $value['lastLogin'] . '",';
    $csv_content .= '"' . $userCount . '",';
    $csv_content .= '"' . $activeExpired . '",';

    $csv_content .= "\n";

}

// di();

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="agents.csv"');

echo $csv_content;