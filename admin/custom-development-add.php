<?php

require_once("../includes/config.php");
require_once("../includes/function.php");

require_once("../class/classDb.php");
$db = new Database();

require_once("../class/agentClass.php");
$objAgent = new agent();

require_once("../class/admin.php");
$objAdmin = new admin();

require_once("../class/commonClass.php");
$objCommon = new common();

// To check agent is login or not
$objAdmin->check_admin_login();

$crm_uses_type_array = array(

    'F' => 'CRM',
    'L' => 'CRM Lite',

);

$application_types_array = array(

    'W' => 'Website',
    'A' => 'Android',
    'I' => 'iOS',
    'ALL' => 'All',

);

$status_array = array(

    'C' => 'Completed',
    'O' => 'Ongoing',


);

if ($_REQUEST['ref'] && decrypt($_REQUEST['ref']) > 0) {

    $development_id = decrypt($_REQUEST['ref']);
}


if (isset($_POST['submit'])) {

    // pr($_POST);
    // pr($_SESSION);

    extract($_POST);

    if (empty($agency_name)) {


        $_SESSION['error']['msg'] = '<font color="red">Please enter Agency Name!</font>';


    } else if ($crm_uses_type == '') {

        $_SESSION['error']['msg'] = '<font color="red">Please Select Crm User Type!</font>';
    } else if (!count($_POST['application_types'])) {

        $_SESSION['error']['msg'] = '<font color="red">Please enter Project Types!</font>';

    } else if ($_POST['country_id'] == '') {

        $_SESSION['error']['msg'] = '<font color="red">Please select country!</font>';

    } else if ($_POST['description'] == '') {

        $_SESSION['error']['msg'] = '<font color="red">Please enter Description!</font>';

    } else if ($_POST['start_date'] == '') {

        $_SESSION['error']['msg'] = '<font color="red">Please enter Start Date!</font>';
    }


    if (empty($_SESSION['error']['msg'])) {


        $data = array(

            'agent_id' => $_SESSION['login']['id'],
            'agency_name' => trim($agency_name),
            'crm_uses_type' => trim($crm_uses_type),
            'application_types' => addslashes(implode(',', $application_types)),
            'country_id' => trim($country_id),
            'web_url' => $web_url,
            'web_name' => $web_name,
            'web_url_live_date' => $web_url_live_date,
            'web_url_status' => $web_url_status,
            'mobile_app_name_android' => $mobile_app_name_android,
            'mobile_app_name_ios' => $mobile_app_name_ios,
            'android_status' => $android_status,
            'android_live_date' => $android_live_date,
            'ios_status' => $ios_status,
            'ios_live_date' => $ios_live_date,
            'app_store_url' => $app_store_url,
            'play_store_url' => $play_store_url,

            'web_start_date' => $web_start_date,
            'app_start_date' => $app_start_date,
            'description' => $description,

            'update_date' => date('Y-m-d H:i:s'),
            'start_date' => $start_date
        );


        if ($development_id > 0) {


            $db->where(array('id' => $development_id))
                ->update('agent_developed_applications', $data);

            $_SESSION['error']['msg'] = '<font color="green">Custom Development has been updated successfully !</font>';


        } else {

            $data['add_date'] = date('Y-m-d H:i:s');

            $development_id = $db->insert('agent_developed_applications', $data);

            $_SESSION['error']['msg'] = '<font color="green">Custom Development has been added successfully!</font>';

        }


        if ($development_id > 0) {

            $url = "custom-development-add.php?ref=" . urlencode(encrypt($development_id));

            echo "<script>window.location.href='$url';</script>";

            exit;
        }
    }


}

if ($development_id > 0 && $_GET['ref']) {

    $sql = "SELECT * FROM agent_developed_applications WHERE id='$development_id' ";

    $db->query($sql);

    $custom_development_list = $db->fetch_first();

    extract($custom_development_list);
}


?>

<?php include('../includes/admin-header.php'); ?>
<?php include('../includes/banner.php'); ?>
<?php include('../includes/admin-left-panel.php'); ?>

    <!-- For calender -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <!-- For calender -->

    <!-- right-panel -->
    <div class="right-panel column">

        <?php include('../includes/admin_login_section.php'); ?>

        <style type="text/css">
            .addContent {
                width: 100%;
                padding: 15px;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }

            .addContent .form-control {
                display: block;
                width: 100%;
                height: 34px;
                padding: 6px 12px;
                font-size: 13px;
                line-height: 1.42857143;
                color: #666;
                background-color: #fff;
                background-image: none;
                border: 1px solid #c4c8cc;
                border-radius: 4px;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }

            .addContent textarea.form-control {
                height: auto;
            }

            .addContent .lable {
                width: 100%;
                font-size: 13px;
                display: block;
                margin-bottom: 5px;
            }

            .rowNew {
                margin-left: -15px;
                margin-right: -15px;
            }

            .col-1,
            .col-2 {
                float: left;
                padding-left: 15px;
                padding-right: 15px;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }

            .col-1 {
                width: 100%;
            }

            .col-2 {
                width: 50%;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-container fieldset legend {
                background-color: #204c6f !important;
                padding: 6px 15px;
                border-radius: 0px;
                color: #fff;
                border: none;
                font-size: 14px;
            }

            .checkbox {
                position: relative;
                margin-right: 10px;
                margin-bottom: 0px;
                margin-top: 0px;
                cursor: pointer;
                font-weight: normal !important;
                font-size: 13px;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                text-align: center;
                vertical-align: middle;
            }

            .checkbox input {
                opacity: 0;
                cursor: pointer;
                height: 0;
                width: 0;
                position: absolute;
                top: 3px;
            }

            .checkbox .checkmark {
                height: 14px;
                width: 14px;
                background-color: #fff;
                border: 1px solid #666;
                display: inline-block;
                position: relative;
                top: 4px;
                margin-right: 2px;
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
                margin-top: 0px;
                margin-left: 4px;
                width: 4px;
                height: 8px;
                border: solid #017801;
                border-width: 0 3px 3px 0;
                -webkit-transform: rotate(45deg);
                -ms-transform: rotate(45deg);
                transform: rotate(45deg);
            }

            #error_msg {
                font-size: 18px;
                padding-bottom: 5px;
            }

        </style>

        <!-- add branch office form -->
        <form action="" method="post">
            <div class="form-container">


                <div class="form-sub-head"> Add Custom Development <a href="custom-development.php"
                                                                      style="font-size: 14px;"><i class="fa fa-eye"></i>
                        View Custom Development </a></div>

                <?php if (isset($_SESSION[error]['msg'])) { ?>
                    <div id="error_msg">
                        <font color="red"><?php echo $_SESSION[error]['msg']; ?> </font>
                    </div>
                    <?php unset($_SESSION[error]['msg']);
                } ?>


                <fieldset>
                    <legend>Custom Development Information</legend>
                    <div class="addContent">
                        <div class="rowNew">
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable"> Agency Name <font color="red"> * </font></span>
                                    <input name="agency_name" id="name" type="text" value="<?php echo $agency_name; ?>"
                                           class="form-control" required/>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable"> CRM User Type <font color="red"> * </font></span>
                                    <select name="crm_uses_type" id="crm_uses_type" class="form-control">
                                        <option value="">Please Select</option>
                                        <?php foreach ($crm_uses_type_array as $key => $value) { ?>
                                            <option value="<?php echo $key; ?>" <?php if ($key == $crm_uses_type) {
                                                echo "selected";
                                            } ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable"> Project Types <font color="red"> * </font></span>
                                    <div style="border: 1px solid #c4c8cc; padding: 4px 15px 8px 15px; -webkit-box-sizing: border-box; box-sizing: border-box; border-radius: 4px;">
                                        <?php

                                        $selected_project_type = explode(',', $application_types);


                                        foreach ($application_types_array as $key => $value) {

                                            ?>
                                            <label class="checkbox">
                                                <input type="checkbox"
                                                       name="application_types[]" <?php if (in_array($key, $selected_project_type)) {
                                                    echo "checked";
                                                } ?> value="<?php echo $key; ?>">
                                                <span class="checkmark"></span> <?php echo $value; ?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable"> Country <font color="red"> * </font></span>
                                    <?php echo $objAgent->country('country_id', $country_id, ' required class="form-control"'); ?>
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable"> Start Date <font color="red"> * </font></span>
                                    <input name="start_date" id="start_date" type="text"
                                           value="<?php echo $start_date; ?>" <?php if ($start_date && $start_date != '0000-00-00') { ?><?php echo 'readonly';
                                    } ?>
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-1">
                                <span class="lable"> Description <font color="red"> * </font></span>
                                <textarea name="description" id="description" class="form-control"
                                          rows="4" required><?php echo $description; ?></textarea>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Website Information</legend>
                    <div class="addContent">
                        <div class="rowNew">
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">Website Name</span>
                                    <input name="web_name" id="web_name" type="text" value="<?php echo $web_name; ?>"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">Website Status</span>
                                    <select name="web_url_status" id="web_url_status" class="form-control">
                                        <option value="">Please Select</option>
                                        <?php foreach ($status_array as $key => $value) { ?>
                                            <option value="<?php echo $key; ?>" <?php if ($key == $web_url_status) {
                                                echo "selected";
                                            } ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">Website Url </span>
                                    <input name="web_url" id="web_url" type="text" value="<?php echo $web_url; ?>"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">Website Live Date</span>
                                    <input class="datepicker form-control" autocomplete="off" name="web_url_live_date"
                                           id="web_url_live_date" value="<?php echo $web_url_live_date; ?>"
                                           type="text"/>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </fieldset>


                <fieldset>
                    <legend>Android App</legend>

                    <div class="addContent">
                        <div class="rowNew">
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">App Name</span>
                                    <input name="mobile_app_name_android" id="mobile_app_name_android" type="text"
                                           value="<?php echo $mobile_app_name_android; ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">App Status</span>
                                    <select name="android_status" id="android_status" class="form-control">
                                        <option value="">Please Select</option>
                                        <?php foreach ($status_array as $key => $value) { ?>
                                            <option value="<?php echo $key; ?>" <?php if ($key == $android_status) {
                                                echo "selected";
                                            } ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">Live Date</span>
                                    <input class="datepicker form-control" autocomplete="off" name="android_live_date"
                                           id="android_live_date" type="text"
                                           value="<?php echo $android_live_date; ?>"/>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">Play Store Url</span>
                                    <input name="play_store_url" id="play_store_url" type="text"
                                           value="<?php echo $play_store_url; ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>iOS App</legend>

                    <div class="addContent">
                        <div class="rowNew">
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">App Name</span>
                                    <input name="mobile_app_name_ios" id="mobile_app_name_ios" type="text"
                                           value="<?php echo $mobile_app_name_ios; ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">App Status</span>
                                    <select name="ios_status" id="ios_status" class="form-control">
                                        <option value="">Please Select</option>
                                        <?php foreach ($status_array as $key => $value) { ?>
                                            <option value="<?php echo $key; ?>" <?php if ($key == $ios_status) {
                                                echo "selected";
                                            } ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">Live Date</span>
                                    <input class="datepicker form-control" autocomplete="off" name="ios_live_date"
                                           id="ios_live_date" type="text" value="<?php echo $ios_live_date; ?>"/>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <span class="lable">App Store Url</span>
                                    <input name="app_store_url" id="app_store_url" type="text"
                                           value="<?php echo $app_store_url; ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </fieldset>

                <center>
                    <button name="submit">SUBMIT</button>
                </center>

            </div>

        </form>

    </div>
    <script>
        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            changeYear: true,
            changeMonth: true
        });
    </script>

<?php include('../includes/admin-footer.php'); ?>