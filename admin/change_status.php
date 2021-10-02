<?php

require_once("../includes/config.php");
require_once("../includes/function.php");

require_once("../class/classDb.php");
$db = new Database();
require_once("../class/agentClass.php");
$objAgent = new agent();

require_once("../class/branchClass.php");
$objBranch = new branch();

require_once("../class/commonClass.php");
$objCommon = new common();

require_once("../class/admin.php");
$objAdmin = new admin();

// To check admin is login or not
$objAdmin->check_admin_login();


$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);


$mobile_app_users_array = $objAdmin->mobile_app_users_array;


$crm_uses_types_array = $objAdmin->crm_uses_types_array;


$status_array = $objAdmin->status_array;


if ($requestMethod == 'post' && $_POST['change_status'] == 'Y' && decrypt($_REQUEST['agentId']) > 0) {


    $agentId = decrypt($_REQUEST['agentId']);


    if ($agentId > 0) {


        $data = array(
            'agentStatus' => $_POST['agentStatus'],
        );


        $app_is_match_title = 'N';
        $app_is_featured_title = 'N';

        if ($_POST['app_is_match_title'] == 'Y') {
            $app_is_match_title = 'Y';
        }

        if ($_POST['app_is_featured_title'] == 'Y') {
            $app_is_featured_title = 'Y';
        }


        $data['app_is_match_title'] = $app_is_match_title;
        $data['app_is_featured_title'] = $app_is_featured_title;


        if ($_POST['app_is_match_title'] == 'Y') {
            $app_is_match_title = 'Y';
        }

        if ($_POST['app_is_featured_title'] == 'Y') {
            $app_is_featured_title = 'Y';
        }


        if ($_POST['crm_uses_type']) {


            $data['crm_uses_type'] = $_POST['crm_uses_type'];
        }


        if ($_POST['mobile_app_owner']) {


            $data['mobile_app_owner'] = $_POST['mobile_app_owner'];
        }


        if ($_POST['unica_application_enabled']) {

            $data['unica_application_enabled'] = $_POST['unica_application_enabled'];
        }


        $db->where(array('agentId' => $agentId))
            ->update('agent', $data);


        $sql = " select * from agent where agentId =  $agentId ORDER  by agentId ";
        $db->query($sql);
        $agent_details = $db->fetch_first();


        if ($agent_details['crm_uses_type'] == 'L') {


            $sql = " select * from branches where agent_id = $agentId ORDER  by branch_id ";
            $db->query($sql);
            $branch_detail = $db->fetch_first();

        }


        if ($agent_details['crm_uses_type'] == 'L' && empty($branch_detail['branch_id'])) {


            $data = array(
                'agent_id' => $agent_details['agentId'],
                'name' => addslashes($agent_details['agencyName'])

            );

            $db->insert('branches', $data);
        }


        $sql = "SELECT id, unica_lead_assign_source_id FROM agent_settings WHERE agent_id='$agentId' ORDER by id DESC ";
        $db->query($sql);
        $agent_settings = $db->fetch_first();


        if ($_POST['mobile_app_owner']=='Y' && (empty($agent_settings['unica_lead_assign_source_id']) || $agent_settings['unica_lead_assign_source_id'] == 0)) {


            $data = array(
                'agent_id' => $agentId,
                'updated_by_user_id' => $_SESSION['login']['id'],
                'updated_by_user_type' => $_SESSION['login']['user_type'],
                'created_by_id' => $_SESSION['login']['id'],
                'created_user_type' => $_SESSION['login']['user_type'],
                'source_name' => trim('App Lead'),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'status' => 'Y',
                'add_date' => 'now()',
                'update_date' => 'now()'
            );

            $data = $objAgent->sanitize_data($data);
            $lead_source_id = $db->insert('lead_source', $data);


            $setting_data = array(
                'unica_lead_assign_source_id' => $lead_source_id,
                'updated_by_id' => $_SESSION['login']['id'],
                'updated_by_type' => $_SESSION['login']['user_type'],
                'update_date' => date('Y-m-d H:i:s'),
            );


            $setting_data['added_by_id'] = $_SESSION['login']['id'];
            $setting_data['added_by_type'] = $_SESSION['login']['user_type'];
            $setting_data['add_date'] = date('Y-m-d H:i:s');
            $setting_data['agent_id'] = $agentId;


        }

        if ($agent_settings['id'] > 0 && $lead_source_id > 0 && count($setting_data)) {


            $db->where(array('agent_id' => $agentId))
                ->update('agent_settings', $setting_data);

        } else if ($lead_source_id > 0 && count($setting_data)) {

            $db->insert('agent_settings', $setting_data);

        }


        $_SESSION['error']['msg'] = "<font color='green'>Status has been changed successfully!!</font>";

    }


}


if (isset($_REQUEST['agentId'])) {

    $agent_id = decrypt($_REQUEST['agentId']);

    $sql = "SELECT * FROM agent WHERE agentId='{$agent_id}' ";
    $db->query($sql);
    $agent_details = $db->fetch_first();

}


?>

<?php include('../includes/facebox_header.php'); ?>

<script>

    // parent.refresh('view-representing-country.php?contry_id=<?php echo $record_country[0]['country_id']; ?>');

    $(document).ready(function () {
        $('.lable strong').css({'font-size': '16px', 'color': '#50a732'});
        $('.spacer').css({'height': '20px'});
        var h = $('.popup-container').height();
        if (h > 350) {
            $('.popup-container').css({'max-height': '350px', 'overflow-y': 'scroll'});
        }
        else {
            $('.popup-container').removeAttr('style');
        }


    });

    $(function () {


        $('.btn-action .expand').hide();
    });

    //resize function

    $('.popup-container').resize(function () {
        var h = $('.popup-container').height();
        if (h > 350) {
        }
        else {
            $('.popup-container').removeAttr('style');
        }
    });


</script>
<style>
    .new-popup {
        width: 97%;
        margin: 10px auto;
        padding: 15px;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    .new-popup .lable {
        margin-bottom: 10px;
    }

    .radiobutton {
        width: 100%;
        display: block;
        position: relative;
        padding-left: 25px;
        margin-bottom: 10px;
        cursor: pointer;
        font-weight: normal !important;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .radiobutton input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .radiobutton .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 14px;
        width: 14px;
        background-color: #fff;
        border-radius: 50%;
        border: 2px solid #ccc;
    }

    .radiobutton:hover input ~ .checkmark {
        background-color: #fff;
    }

    .radiobutton input:checked ~ .checkmark {
        background-color: #fff;
        border: 2px solid #017801;
    }

    .radiobutton .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .radiobutton input:checked ~ .checkmark:after {
        display: block;
    }

    .radiobutton .checkmark:after {
        top: 2px;
        left: 2px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #017801;
    }

    .checkbox {
        width: 100%;
        position: relative;
        padding-left: 25px;
        margin-top: 0px;
        cursor: pointer;
        display: inline-block;
        font-weight: normal !important;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkbox .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 14px;
        width: 14px;
        background-color: #fff;
        border: 2px solid #ccc;
    }

    .checkbox:hover input ~ .checkmark {
        background-color: #fff;
    }

    .checkbox input:checked ~ .checkmark {
        background-color: #fff;
    }

    .checkbox .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .checkbox input:checked ~ .checkmark:after {
        display: block;
    }

    .checkbox .checkmark:after {
        top: 1px;
        left: 4px;
        width: 4px;
        height: 7px;
        border: solid #017801;
        border-width: 0 2px 2px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .rowNew {
        margin-left: -15px;
        margin-right: -15px;
    }

    .colNew {
        padding-left: 15px;
        padding-right: 15px;
        float: left;
        width: 33.333333%;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    .sendBtn {
        font-size: 15px;
        background-color: #3fb6cb !important;
        border: none;
        padding: 10px 20px;
        color: #fff;
        font-weight: bold;
        border-radius: 4px;
    }


</style>
<form action="change_status.php" enctype="multipart/form-data" method="post">
    <div class="head-blue">Change Status</div>
    <div class="popup-container new-popup">


        <?php

        if (isset($_SESSION['error']['msg'])) {
            echo $_SESSION['error']['msg'];
            echo '<div class="spacer"></div>';
            unset($_SESSION['error']['msg']);

        }
        ?>

        <div class="rowNew">
            <div class="colNew">

                <div class="lable"><strong>Status</strong></div>

                <?php foreach ($status_array as $key => $status_name) { ?>


                    <label class="radiobutton">
                        <input type="radio" name="agentStatus" required
                               value="<?php echo $key; ?>" <?php echo $agent_details['agentStatus'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $status_name; ?>
                    </label>

                <?php } ?>


            </div>
            <div class="colNew">
                <div class="lable"><strong>CRM Version</strong></div>


                <?php

                unset($crm_uses_types_array['N']);

                foreach ($crm_uses_types_array as $key => $crm_uses_type_name) { ?>


                    <label class="radiobutton">
                        <input type="radio" name="crm_uses_type" required
                               value="<?php echo $key; ?>" <?php if ($agent_details['crm_uses_type'] == 'F') {
                            echo 'disabled';
                        } ?>  <?php echo $agent_details['crm_uses_type'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $crm_uses_type_name; ?>
                    </label>
                <?php } ?>


            </div>
            <div class="colNew">
                <div class="lable"><strong>CRM Mobile</strong></div>

                <?php foreach ($mobile_app_users_array as $key => $mobile_app_use) { ?>


                    <label class="radiobutton">
                        <input type="radio" name="mobile_app_owner"
                               value="<?php echo $key; ?>" <?php echo $agent_details['mobile_app_owner'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $mobile_app_use; ?>
                    </label>
                <?php } ?>

            </div>

            <div class="clearfix"></div>

            <input type="hidden" name="agentId" value="<?php echo encrypt($agent_details['agentId']); ?>">
            <input type="hidden" name="change_status" value="Y">
        </div>


        <div class="rowNew" style="border-top: 1px solid #f1f1f1; padding-top: 15px; margin-top: 10px;">

            <div class="clearfix"></div>

            <div class="colNew">

                <div class="lable"><strong>UNICA Application</strong></div>

                <?php foreach ($mobile_app_users_array as $key => $status_name) { ?>


                    <label class="radiobutton">
                        <input type="radio" name="unica_application_enabled" required
                               value="<?php echo $key; ?>" <?php echo $agent_details['unica_application_enabled'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $status_name; ?>
                    </label>

                <?php } ?>


            </div>


            <div class="clearfix"></div>


        </div>


        <div style="border-top: 1px solid #f1f1f1; padding-top: 15px; margin-top: 10px;">
            <div class="rowNew">
                <div class="colNew">
                    <label class="checkbox">
                        <input type="checkbox" name="app_is_match_title"
                               value="Y" <?php if ($agent_details['app_is_match_title'] == 'Y') {
                            echo 'checked';
                        } ?> >
                        <span class="checkmark"></span> Perfect Match
                    </label>
                </div>
                <div class="colNew">
                    <label class="checkbox">
                        <input type="checkbox" name="app_is_featured_title"
                               value="Y" <?php if ($agent_details['app_is_featured_title'] == 'Y') {
                            echo 'checked';
                        } ?> >
                        <span class="checkmark"></span> Featured Match
                    </label>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="text-align: center; border-top: 1px solid #f1f1f1; padding-top: 15px; margin-top: 15px;">
            <input type="submit" value="Submit" name="submit" class="sendBtn">
        </div>

        <div class="clearfix"></div>
    </div>
</form>

		
		






