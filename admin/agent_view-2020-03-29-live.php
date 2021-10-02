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

if (isset($_REQUEST['reset'])) {

    unset($_SESSION['search']);
    echo '<script>window.location.href="agent_view.php"</script>';
    exit();

}

extract($_POST);

if (isset($search)) {

    $_SESSION['search'] = $_POST;

}


$search_params = $_SESSION['search'];

$sql = "select memberType,agent_crm_id,agent_crm_link_date,username from agent where agent_crm_id>0 and memberType>0";
$uniagentDb->query($sql);
$ga_agent_list = $uniagentDb->fetch();

$certified_user_details = $globalDB->resultByKey($ga_agent_list, 'agent_crm_id');

// $certified_user_details['17'] = array('memberType'=>'3','agent_crm_id'=>'17','agent_crm_link_date'=>date('Y-m-d'));
// pr($certified_user_details);
// pr($_SESSION['search']);

?>

<?php include('../includes/admin-header.php'); ?>
<?php include('../includes/banner.php'); ?>
<?php include('../includes/admin-left-panel.php'); ?>


    <!-- right-panel -->
    <div class="right-panel column">

        <?php include('../includes/admin_login_section.php'); ?>


        <!-- view branch office form -->
        <div class="form-container">
            <div class="form-sub-head">View Agent

                <!--
                <a href="add_agent.php"  ><i class="fa  fa-hand-o-up"></i> Add Agent </a>
                -->
            </div>
            <form method="post">

                <fieldset class="search-box">
                    <ul class="fields">
                        <li>
                            <span class="lable"> Country </span>
                            <?php echo $objAgent->country('country', $search_params['country'], ' '); ?>
                        </li>

                        <li>
                            <span class="lable"> Keyword </span><input type="text" name="keyword" placeholder="Keyword"
                                                                       value="<?php echo $search_params['keyword']; ?>">
                        </li>

                        <li>
                            <span class="lable"> Status </span>
                            <select name="status" id="status">
                                <option value="">Select</option>
                                <option value="A" <?php if ($search_params['status'] == 'A') echo 'selected'; ?>>
                                    Active
                                </option>
                                <option value="D" <?php if ($search_params['status'] == 'D') echo 'selected'; ?> >
                                    Inactive
                                </option>
                            </select>
                        </li>

                        <li>
                            <span class="lable"> linked CRM Member </span>
                            <select name="agent_crm_link" id="agent_crm_link">
                                <option value="">Select</option>
                                <option value="Y" <?php if ($search_params['agent_crm_link'] == 'Y') echo 'selected'; ?>>
                                    Yes
                                </option>
                                <option value="N" <?php if ($search_params['agent_crm_link'] == 'N') echo 'selected'; ?> >
                                    No
                                </option>
                            </select>
                        </li>


                        <?php

                        $crm_uses_types = array(
                            'L' => 'Light',
                            'F' => 'Full'
                        );


                        ?>

                        <li>
                            <span class="lable"> CRM Uses Type </span>
                            <select name="crm_uses_type" id="crm_uses_type">
                                <option value="">Select</option>

                                <?php foreach ($crm_uses_types as $crm_uses_type => $crm_uses_type_name) { ?>
                                    <option value="<?php echo $crm_uses_type; ?>" <?php if ($search_params['crm_uses_type'] == $crm_uses_type) echo 'selected'; ?>>
                                        <?php echo $crm_uses_type_name; ?>
                                    </option>

                                <?php } ?>
                            </select>
                        </li>

                        <li>
                            <input type="radio" value="A"
                                   name="active_expire" <?php if ($search_params['active_expire'] == 'A') echo 'checked'; ?>>
                            All Active
                            <input type="radio" value="D"
                                   name="active_expire" <?php if ($search_params['active_expire'] == 'D') echo 'checked'; ?>>
                            All Expired
                        </li>

                        <li style="text-align:right;">
                            <button name="search"><i class="fa fa-search"></i> View</button>
                            <button type="button"
                                    onclick="if(confirm('Are You sure you want to reset your search preference?'))window.location.href='?reset=reset_search';">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </li>

                    </ul>
                </fieldset>

            </form>

            <!-- listing -->

            <?php

            // condition for pagination  count record

            //$where['agentId >'] = '0';

            $or_like = " agentId >0 ";


            if (isset($search_params['country']) AND $search_params['country'] != '') {

                $where['country'] = $search_params['country'];

            }

            if (isset($_SESSION['search']['status']) AND $_SESSION['search']['status'] != '') {

                $where['agentStatus'] = $search_params['status'];

            }

            if (isset($_SESSION['search']['keyword']) AND $_SESSION['search']['keyword'] != '') {

                $or_like .= " and ( 
                
                username LIKE '%{$search_params['keyword']}%' 
                OR contactPerson LIKE '%{$search_params['keyword']}%' 
                OR city LIKE '%{$search_params['keyword']}%' 
                OR agencyName LIKE '%{$search_params['keyword']}%' 
                OR emailAddress LIKE '%{$search_params['keyword']}%' 
                OR designation LIKE '%{$search_params['keyword']}%' 
                OR skypeId LIKE '%{$search_params['keyword']}%' 
                OR state LIKE '%{$search_params['keyword']}%' )";

            }

            if (isset($search_params['active_expire']) AND $search_params['active_expire'] != '') {

                if ($search_params['active_expire'] == 'A') {

                    $or_like .= " and  DATE(valid_till) >= '" . date('Y-m-d') . "'";

                }

                if ($search_params['active_expire'] == 'D') {

                    $or_like .= " and  DATE(valid_till) < '" . date('Y-m-d') . "'";

                }

            }


            if (isset($search_params['crm_uses_type']) AND $search_params['crm_uses_type'] != '') {

                $crm_uses_type = $search_params['crm_uses_type'];

                $where['crm_uses_type'] = $search_params['crm_uses_type'];

            }


            if (isset($search_params['agent_crm_link']) && $search_params['agent_crm_link'] != '') {

                $certified_agentId_list = array_keys($certified_user_details);
                $certified_agentId_implode_value = implode(',', $certified_agentId_list);

                $agent_crm_link_query = " and agentId IN (" . $certified_agentId_implode_value . ") ";

                if ($_SESSION['search']['agent_crm_link'] == 'N') {
                    $agent_crm_link_query = " and agentId NOT IN (" . $certified_agentId_implode_value . ") ";
                }

                $or_like .= $agent_crm_link_query;

            }

            if ($or_like != '')
                $db->where($or_like);

            if (count($where) > 0)
                $db->where($where);
            $db->select();
            $db->from('agent');
            $record = $db->fetch();


            // echo $db->last_query();

            $total_record = count($record);

            $limit_per_page = 1;
            $limit_per_page = 50;
            if (isset($_GET['page']) && !empty($_GET['page']))
                $page_no = $_GET['page'];
            else
                $page_no = 1;

            $start_limit = ($page_no - 1) * $limit_per_page;

            if ($or_like != '')
                $db->where($or_like);

            if (count($where) > 0)
                $db->where($where);

            $db->order_by('agentId', 'desc');

            $db->select();
            $db->from('agent');
            $db->limit($limit_per_page, $start_limit);
            $record = $db->fetch();

            // echo $db->last_query();
            // pr($record);
            ?>

            <div class="table-head-left"><i class="fa fa-institution"></i> Total Representing Institution :
                <span><?php echo $total_record; ?></span></div>

            <?php if ($total_record != 0) { ?>
                <div class="table-head-left" align="right">Displaying From <span><?php echo $start_limit + 1; ?></span>
                </div>
            <?php } ?>
            <?php

            // pr($record);

            if (count($record) > 0) {
                foreach ($record as $key => $value) {

                    $contry_detail = $objAgent->country_detail('country_id', $value['country']);

                    // pr($contry_detail);
                    ?>

                    <div class="blue-head">

                        <?php

                        // echo $_SESSION['login']['agentLogo'];
                        if (isset($value['agentLogo']) AND $value['agentLogo'] != '') {

                            if (file_exists('../agent_logo/' . $value['agentLogo'])) {
                                echo '<img src="../agent_logo/' . $value['agentLogo'] . '" >';

                            } else {
                                echo '<img src="../images/no_image_available.png" >';
                            }
                        } else {
                            echo '<img src="../images/no_image_available.png" >';
                        }

                        ?>

                        <?php echo $value['agencyName']; ?>

                        <?php
                        // pr($value['email_verified']);
                        if ($value['email_verified'] == 'N')
                            echo ' | <a href="send_verify_link_live.php?id=' . $objCommon->url_encrypt($value['agentId']) . '" style="color:red;" rel="[facebox]" rev="iframe|650|400" > Send Email Verify Link </a>';

                        ?>
                        <?php
                        if (!empty($certified_user_details[$value['agentId']])) {
                            if ($certified_user_details[$value['agentId']]['memberType'] == '3') {
                                echo "| <span style='color:yellow; font-weight:bold;'> Silver (" . $certified_user_details[$value['agentId']]['username'] . ")</span>";
                            } else if ($certified_user_details[$value['agentId']]['memberType'] == '4') {
                                echo "| <span style='color:yellow; font-weight:bold;'> Gold (" . $certified_user_details[$value['agentId']]['username'] . ")</span>";
                            } else if ($certified_user_details[$value['agentId']]['memberType'] == '5') {
                                echo "";
                            } else {
                                echo "";
                            }
                        }
                        ?>
                    </div>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <th>Contact Details</th>
                            <th width="100">Data Consumed</th>

                            <th width="100">Country</th>

                            <th width="100">User Name</th>
                            <th width="50">Status</th>

                           <th width="50">CRM Light  Uses</th>
                            <th width="130"><i class="fa fa-calendar-o"></i> Add Date</th>
                            <th width="100"><i class="fa fa-calendar-o"></i> Active From</th>
                            <th width="100"><i class="fa fa-calendar-o"></i> Active Till Date</th>
                            <th width="110" class="center">Action</th>
                        </tr>
                        <tr>
                            <td><font title="Email"><?php echo $value['emailAddress']; ?> </font> | <font
                                        title="Contact Number"><?php echo $value['contact_no']; ?></font> | <font
                                        title="Contact Person"> <?php echo $value['contactPerson']; ?> </font>| <font
                                        title="Designation"><?php echo $value['designation']; ?> </font></td>

                            <td>
                                <font title="User Name"> <?php echo $objAgent->agent_consumed_data($value['agentId']); ?>
                                    MB </font></td>


                            <td> <?php if ($contry_detail[0]['short_name'] == '') {
                                    echo '-';
                                } else {
                                    echo $contry_detail[0]['short_name'];
                                } ?> </td>

                            <td><font title="User Name"> <?php echo $value['username']; ?> </font></td>
                            <td>


                                <div class="chk_box_click" style="display:inline-block;"
                                     data-table_id="<?php echo $value['agentId']; ?>"
                                     data-field_value="<?php echo $value['agentStatus']; ?>"
                                     data-field_name='agentStatus'
                                >

                                    <input type="checkbox" class="js-switch" <?php if ($value['agentStatus'] == 'A') {
                                        echo 'checked="checked"';
                                    } ?> id="chk_box_id_<?php echo $value['agentId']; ?>">

                                </div>


                            </td>


                            <td  title="<?php echo $crm_uses_types[$value['crm_uses_type']];?>">


                                <div class="chk_box_click" style="display:inline-block;" data-table_id="<?php echo $value['agentId']; ?>"
                                     data-field_value="<?php echo $value['crm_uses_type']; ?>"
                                     data-field_name='crm_uses_type'
                                >

                                    <input type="checkbox" class="js-switch" <?php if ($value['crm_uses_type'] == 'L') {
                                        echo 'checked="checked"';
                                    } ?>>

                                </div>


                            </td>

                            <td><?php echo date("dS F Y", strtotime($value['addDate'])); ?> </td>
                            <td><?php echo $value['subscription_date']; ?></td>
                            <td><?php echo $value['valid_till']; ?></td>


                            <td class="center">

                                <a href="agent-details.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>"
                                   title="View Agent Details"><i class="fa fa-eye"></i></a>


                                &nbsp; <a href="agent-edit-details.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>"
                                        title="Edit Agent Details"><i class="fa fa-edit"></i></a>

                                &nbsp;
                                <a href="dynamic_payment_link.php?id=<?php echo urlencode(encrypt($value['agentId'])); ?>"
                                   title="Make Payment Link"><i class="fa fa-money"></i></a>


                            </td>

                        </tr>
                    </table>

                <?php }
            } else {
                echo '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td colspan="5"><font color="red">No record is present</font></td></tr></table>';
            } ?>


            <!-- listing -->
            <?php echo $objCommon->getPaginationLinks($page_no, $total_record, $limit_per_page); ?>
        </div>
        <!-- view branch office form -->

    </div>
    <!-- right-panel -->


    <script>

        $(document).ready(function () {
            $('.chk_box_click').click(function (event) {


                if (confirm('Are you sure to you change status !')) {

                    // alert($(this).attr('table_id'));
                    var obj_div = $(this);
                    var table_id = obj_div.attr('data-table_id');
                    var field_name = obj_div.attr('data-field_name');
                    var field_value = obj_div.attr('data-field_value');



                    var data = {};

                    if(field_name=='crm_uses_type'){

                        field_value = field_value=='L'?'F':'L';

                        data = {"crm_uses_type" : field_value, "LastUpdated" : "now()"};
                    }


                    if(field_name=='agentStatus'){


                        field_value = field_value=='D'?'A':'D';

                        data = {"agentStatus" : field_value, "LastUpdated" : "now()"};

                    }


                    var values = {
                        "type": "change_common_status",
                        "table_id": table_id,
                        "table": "agent",
                        "field": "agentId",
                        "data": data
                    };



                    $.ajax({
                        dataType: "json",
                        url: "ajax.php",
                        type: "post",
                        data: values,
                        success: function (data) {
                            // alert(data.content);
                            if (data.content == 'updated') {
                                obj_div.attr('status', status);
                                alert('Your status is changed!');

                            }
                        },

                    });


                } else {



                    event.preventDefault();

                }


            });
        });


        set_left_menu('submenu_view_agent', 'submenu_agent', 'button_member');


    </script>

<?php include('../includes/agent-footer.php'); ?>