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


$status_array = $status_array = array(

    'Y' => 'Active',
    'N' => 'InActive',
);;


if ($requestMethod == 'post' && $_POST['change_settings'] == 'Y' && decrypt($_REQUEST['agentId']) > 0) {


    $agent_id = decrypt($_REQUEST['agentId']);


    $sql = "SELECT * FROM agent_settings WHERE agent_id='$agent_id' ORDER by id DESC ";
    $db->query($sql);
    $agent_settings = $db->fetch_first();


    $data = array(
        'whatsapp_link_enabled' => $_POST['whatsapp_link_enabled'],
        'google_calendar_link_enabled' => $_POST['google_calendar_link_enabled'],
        'agent_logo_enabled' => $_POST['agent_logo_enabled'],
        'course_compare_enabled' => $_POST['course_compare_enabled'],

        'updated_by_id' => $_SESSION['login']['id'],
        'updated_by_type' => $_SESSION['login']['user_type'],
        'update_date' => date('Y-m-d H:i:s'),
    );


    if ($agent_settings['id'] > 0) {


        $db->where(array('agent_id' => $agent_id))
            ->update('agent_settings', $data);

    } else {


        $data['added_by_id'] = $_SESSION['login']['id'];
        $data['added_by_type'] = $_SESSION['login']['user_type'];
        $data['add_date'] = date('Y-m-d H:i:s');

        $data['agent_id'] = $agent_id;


        $db->insert('agent_settings', $data);

    }


    $_SESSION['error']['msg'] = "<font color='green'>Settings has been updated successfully!!</font>";

}


if (isset($_REQUEST['agentId'])) {

    $agent_id = decrypt($_REQUEST['agentId']);

    $sql = "SELECT * FROM agent WHERE agentId='{$agent_id}' ";
    $db->query($sql);
    $agent_details = $db->fetch_first();


    $sql = "SELECT * FROM agent_settings WHERE agent_id='{$agent_id}' ORDER by id DESC ";
    $db->query($sql);
    $agent_settings = $db->fetch_first();

}


?>

<?php include('../includes/facebox_header.php'); ?>

<script>


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
<form action="change_settings.php" enctype="multipart/form-data" method="post">
    <div class="head-blue">Account Settings / <span title="Agency Name"><?php echo $agent_details['agencyName']; ?></span></div>
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

                <div class="lable"><strong>Whatsapp Link </strong></div>

                <?php foreach ($status_array as $key => $status_name) { ?>


                    <label class="radiobutton">
                        <input type="radio" name="whatsapp_link_enabled" required
                               value="<?php echo $key; ?>" <?php echo $agent_settings['whatsapp_link_enabled'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $status_name; ?>
                    </label>

                <?php } ?>


            </div>
            <div class="colNew">
                <div class="lable"><strong>Google Calendar </strong></div>

                <?php foreach ($status_array as $key => $status_name) { ?>

                    <label class="radiobutton">
                        <input type="radio" name="google_calendar_link_enabled" required
                               value="<?php echo $key; ?>" <?php echo $agent_settings['google_calendar_link_enabled'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $status_name; ?>
                    </label>

                <?php } ?>

            </div>

            <div class="colNew">
                <div class="lable"><strong>Display Agent Logo</strong></div>

                <?php foreach ($status_array as $key => $status_name) { ?>

                    <label class="radiobutton">
                        <input type="radio" name="agent_logo_enabled" required
                               value="<?php echo $key; ?>" <?php echo $agent_settings['agent_logo_enabled'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $status_name; ?>
                    </label>

                <?php } ?>

            </div>


            <div class="colNew">
                <div class="lable"><strong>Course Compare</strong></div>

                <?php foreach ($status_array as $key => $status_name) { ?>

                    <label class="radiobutton">
                        <input type="radio" name="course_compare_enabled" required
                               value="<?php echo $key; ?>" <?php echo $agent_settings['course_compare_enabled'] == $key ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <?php echo $status_name; ?>
                    </label>

                <?php } ?>

            </div>

            <div class="clearfix"></div>

            <input type="hidden" name="agentId" value="<?php echo encrypt($agent_details['agentId']); ?>">
            <input type="hidden" name="change_settings" value="Y">
        </div>


        <div style="text-align: center; border-top: 1px solid #f1f1f1; padding-top: 15px; margin-top: 15px;">
            <input type="submit" value="Submit" name="submit" class="sendBtn">
        </div>

        <div class="clearfix"></div>
    </div>
</form>

		
		






