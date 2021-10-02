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

// To check agent is login or not
$objAdmin->check_admin_login();


$crm_uses_type_array = array(
    'L' => 'CRM',
    'F' => 'CRM Lite'
);


$crm_project_type_array = array(
    'W' => 'Website',
    'A' => 'Android',
    'I' => 'iOS',
    'ALL' => 'All',

);


extract($_POST);

if (isset($_REQUEST['reset'])) {

    unset($_SESSION['search']);
}

if (isset($search)) {

    $_SESSION['search'] = $_POST;

    $search_params = $_SESSION['search'];
}


$sql = "SELECT * FROM agent_developed_applications WHERE 1 ";

if ($search_params['country']) {

    $country_id = addslashes($search_params['country']);

    $where .= " AND country_id = '{$country_id}'  ";
}

if ($search_params['user_type']) {

    $crm_uses_type = addslashes($search_params['user_type']);

    $where .= " AND crm_uses_type = '{$crm_uses_type}'  ";
}


if ($search_params['project_type']) {

    $project_type = addslashes($search_params['project_type']);

    $where .= "   AND FIND_IN_SET('$project_type', application_types)  ";
}


if ($search_params['keyword']) {

    $keywords = addslashes($search_params['keyword']);

    $where .= " AND (
	 
				
				agency_name LIKE '%{$keywords}%' 
				OR web_url LIKE '%{$keywords}%' 
				OR web_name LIKE '%{$keywords}%' 
				OR mobile_app_name_android LIKE '%{$keywords}%' 
				OR mobile_app_name_ios LIKE '%{$keywords}%' 
				OR play_store_url LIKE '%{$keywords}%' 
				OR app_store_url LIKE '%{$keywords}%' 
				OR description LIKE '%{$keywords}%' 
				
				)
				";
}


$sql .= $where;

$db->query($sql);

$result_count = $db->execute($sql);
$total_leads = $result_count->affected_rows;

$limit_per_page = 50;
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $page_no = $_GET['page'];
} else {
    $page_no = 1;
}
$start_limit = ($page_no - 1) * $limit_per_page;

$sql .= " order by id desc ";

$sql .= " LIMIT $start_limit,  $limit_per_page";

$db->query($sql);

$custom_development_list = $db->fetch();


$country_array = $objAgent->country_array();


?>

<?php include('../includes/admin-header.php'); ?>
<?php include('../includes/banner.php'); ?>
<?php include('../includes/admin-left-panel.php'); ?>


    <style type="text/css">
        .noText {
            color: #ff3b53;
            display: block;
            text-align: center;
            font-size: 40px;
            margin-top: 30px;
        }

        .sep-li,
        .sep-li ul {
            margin: 0px;
            padding: 0px;
        }

        .sep-li li {
            width: 100%;
            display: block;
            margin: 0px;
            padding: 5px 0px;
            line-height: 18px;
            border-bottom: 1px solid #eee;
        }

        .ongoing {
            color: #fcb62e;
        }

        .completed {
            color: #40a000;
        }
    </style>

    <!-- right-panel -->
    <div class="right-panel column">

        <?php include('../includes/admin_login_section.php'); ?>


        <!-- view branch office form -->
        <div class="form-container">
            <div class="form-sub-head">Custom Development<a href="custom-development-add.php"
                                                            style="font-size: 14px;"><i class="fa fa-plus"></i> Add
                    Custom Development </a></div>

            <form method="post" action="">
                <fieldset class="search-box">
                    <ul class="fields">
                        <li>
                            <span class="lable">Keyword</span>
                            <input type="text" name="keyword" placeholder="Keyword"
                                   value="<?php echo $search_params['keyword']; ?>">
                        </li>
                        <li>
                            <span class="lable">Country</span>
                            <?php echo $objAgent->country('country', $search_params['country'], ' '); ?>
                        </li>
                        <li>
                            <span class="lable">User Type</span>
                            <select name="user_type" id="user_type">
                                <option value="">Select</option>
                                <?php foreach ($crm_uses_type_array as $crm_uses_type => $crm_uses_type_name) { ?>

                                    <option value="<?php echo $crm_uses_type; ?>" <?php if ($search_params['user_type'] == $crm_uses_type) {
                                        echo "selected";
                                    } ?>>
                                        <?php echo $crm_uses_type_name; ?>
                                    </option>

                                <?php } ?>
                            </select>
                        </li>
                        <li>
                            <span class="lable">Project Type</span>
                            <select name="project_type" id="project_type">
                                <option value="">Select</option>

                                <?php foreach ($crm_project_type_array as $crm_project_type => $crm_project_type_name) { ?>

                                    <option value="<?php echo $crm_project_type; ?>" <?php if ($search_params['project_type'] == $crm_project_type) {
                                        echo "selected";
                                    } ?>>
                                        <?php echo $crm_project_type_name; ?>
                                    </option>

                                <?php } ?>

                            </select>
                        </li>
                        <li>
                            <button type="submit" name="search"><i class="fa fa-search"></i> Search</button>
                            <button type="submit" name="reset"><i class="fa fa-refresh"></i> Reset</button>
                        </li>
                    </ul>
                </fieldset>
            </form>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="5%"><strong>S. No.</strong></th>
                    <th width="21%"><strong>Client Details</strong></th>
                    <th width="22%"><strong>Website</strong></th>
                    <th width="16%"><strong>Android</strong></th>
                    <th width="16%"><strong>iOS</strong></th>
                    <th width="20%"><strong>Description</strong></th>
                    <!--  <th width="20%"><strong>Action</strong></th>-->
                </tr>

                <?php

                $sn = $start_limit + 1;

                foreach ($custom_development_list as $key => $value) {

                    $application_types_array = explode(',', $value['application_types']);


                    ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td>
                            <ul class="sep-li">
                                <li title="Agency Name"><strong><?php echo $value['agency_name']; ?></strong></li>
                                <li title="User Type"><?php if ($value['crm_uses_type'] == 'F') {
                                        echo "CRM";
                                    } else if ($value['crm_uses_type'] == 'L') {
                                        echo "CRM Lite";
                                    } ?></li>
                                <li title="Country">
                                    <?php echo $country_array[$value['country_id']]['short_name']; ?></li>
                                <li title="Add Date"><?php echo $value['add_date'] == '0000-00-00' ? '' : date("dS M, Y", strtotime($value['add_date'])); ?></li>
                            </ul>
                        </td>
                        <td>

                            <?php if (in_array('W', $application_types_array)) { ?>
                                <ul class="sep-li">
                                    <li title="Website Name"><strong><?php echo $value['web_name']; ?></strong></li>
                                    <li title="Website Status"><?php if ($value['web_url_status'] == 'C') {
                                            echo "<span class='completed'>Completed</span>";
                                        } else if ($value['web_url_status'] == 'O') {
                                            echo "<span class='ongoing'>Ongoing</span>";
                                        } ?></li>

                                    <?php if ($value['web_url_live_date'] != '0000-00-00') { ?>

                                        <li title="Live Date"><?php echo $value['web_url_live_date'] == '0000-00-00' ? '' : date("dS M, Y", strtotime($value['web_url_live_date'])); ?></li>

                                    <?php } ?>
                                    <?php if ($value['web_url']) { ?>

                                        <li title="Web URL"><a href="<?php echo $value['web_url']; ?>"
                                                               target="_blank"><?php echo $value['web_url']; ?></a></li>
                                    <?php } ?>


                                </ul>

                            <?php } else { ?>
                                <span class="noText">&times;</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (in_array('A', $application_types_array)) { ?>
                                <ul class="sep-li">
                                    <li title="App Name">
                                        <strong><?php echo $value['mobile_app_name_android']; ?></strong></li>
                                    <li title="App Status"><?php if ($value['android_status'] == 'C') {
                                            echo "<span class='completed'>Completed</span>";
                                        } else if ($value['android_status'] == 'O') {
                                            echo "<span class='ongoing'>Ongoing</span>";
                                        } ?></li>

                                    <?php if ($value['android_live_date'] != '0000-00-00') { ?>
                                        <li title="Live Date"><?php echo $value['android_live_date'] == '0000-00-00' ? '' : date("dS M, Y", strtotime($value['android_live_date'])); ?></li>

                                    <?php } ?>
                                    <?php if ($value['play_store_url']) { ?>


                                        <li title="Play Store URL"><a href="<?php echo $value['play_store_url']; ?>"
                                                                      target="_blank"><img
                                                        src="../images/play-store-logo.png"
                                                        style="max-width: 100px;"></a></li>

                                    <?php } ?>
                                </ul>

                            <?php } else { ?>
                                <span class="noText">&times;</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (in_array('I', $application_types_array)) { ?>
                                <ul class="sep-li">
                                    <li title="App Name"><strong><?php echo $value['mobile_app_name_ios']; ?></strong>
                                    </li>
                                    <li title="App Status">
                                        <?php if ($value['ios_status'] == 'C') {
                                            echo "<span class='completed'>Completed</span>";
                                        } else if ($value['ios_status'] == 'O') {
                                            echo "<span class='ongoing'>Ongoing</span>";
                                        } ?></li>

                                    <?php if ($value['ios_live_date'] != '0000-00-00') { ?>
                                        <li title="Live Date"><?php echo $value['ios_live_date'] == '0000-00-00' ? '' : date("dS M, Y", strtotime($value['ios_live_date'])); ?></li>

                                    <?php } ?>
                                    <?php if ($value['app_store_url']) { ?>

                                        <li title="App Store URL"><a href="<?php echo $value['app_store_url']; ?>"
                                                                     target="_blank"><img
                                                        src="../images/app-store-logo.png"
                                                        style="max-width: 100px;"></a></li>
                                    <?php } ?>
                                </ul>

                            <?php } else { ?>
                                <span class="noText">&times;</span>
                            <?php } ?>
                        </td>
                        <td>
                            <span style="text-align: justify"><?php echo $value['description']; ?></span>
                        </td>

                        <!--  <td>
                            <a target="_blank"
                               href="custom-development-add.php?ref=<?php /*echo urlencode(encrypt($value['id'])); */ ?>"
                               style="font-size: 14px;"><i class="fa fa-edit"></i></a>

                        </td> -->


                    </tr>


                <?php } ?>

                <?php if (!count($custom_development_list)) {
                    echo '<tr><td colspan="6"><font color="red">No record is present</font></td></tr>';
                } ?>

            </table>
            <div class="pagination-container" style="padding-left:22px;width:100%;box-sizing:border-box;">
                <?php

                echo $objAgent->getPaginationLinks($page_no, $total_leads, $limit_per_page, $page_name);

                ?>
            </div>
        </div>
        <!-- view branch office form -->

    </div>
    <!-- right-panel -->


<?php include('../includes/agent-footer.php'); ?>