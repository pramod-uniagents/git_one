<?php

//echo "<script>window.location.href='index.html';</script>";
//exit;


require_once("includes/config.php");

require_once("includes/function.php");

require_once("class/classDb.php");
//require_once("class/classDbDemo.php");

require_once("class/agentClass.php");

if (!isset($_SESSION['welcome']['popup']))

    $_SESSION['welcome']['popup'] = 1;

$db = new Database();

$objAgent = new agent();

// To check agent is login or not

$objAgent->check_login();


if (isset($_POST['submit'])) {

    if ($_POST['user'] == '') {

        $_SESSION['error']['msg'] = '<font>Please enter the user!</font>';

    } else if ($_POST['password'] == '') {

        $_SESSION['error']['msg'] = '<font>Please enter the password!</font>';

    } else if ($_POST['user_type'] == '') {

        $_SESSION['error']['msg'] = '<font>Please select user type!</font>';

    }


    if (empty($_SESSION['error']['msg'])) {

        extract($_POST);

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $user_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $user_ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $user_ip = null;
        }

        // pr($_POST);
        // user type 1 means user is agent login and 7 sub user of super admin

        if ($user_type == 1 || $user_type == 7) {

            if ($user_type == 7) {

                $sql = "SELECT * FROM agent  WHERE sub_user ='" . $user . "' AND sub_password ='" . encrypt($password) . "' ";
            } else {

                $sql = "SELECT * FROM agent  WHERE username ='" . $user . "' AND password ='" . encrypt($password) . "' ";
            }

            $db->query($sql);
            $user = $db->fetch();

            // echo $db->last_query();


            if (empty($user[0]['agentId'])) {


                $_SESSION['error']['msg'] = '<font>User and Password seems incorrect! Please try again!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your email not verified! Please try after email verification!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['agentStatus'] != 'A') {


                $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }


            $fromDate = date("Y-m-d H:i:s");

            $valid_till = date("Y-m-d", strtotime($user[0]['valid_till'] . " +15 day"));


            if (empty($_SESSION['error']['msg']) && $valid_till <= $fromDate) {


                $rdr_content = urlencode(encrypt("UCRM00" . $user[0]['agentId']));

                $_SESSION['error']['msg'] = "<font>Your Account has been expired on " . date("Y-m-d", strtotime($user[0]['valid_till'])) . ". Please contact our administrator or write to us at support@uniagents.com!</font>";

                echo "<script>window.location.href='agent-account-expired.php?rdr=" . $rdr_content . "';</script>";
                exit;
            }


            if (empty($_SESSION['error']['msg']) && count($user) > 0) {


                if ($user[0]['email_verified'] == 'Y' && $user[0]['agentStatus'] == 'A' && $valid_till > $fromDate) {

                    $_SESSION['login']['username'] = $user[0]['username'];

                    $_SESSION['login']['email'] = $user[0]['emailAddress'];

                    $_SESSION['login']['primaryEmail'] = $user[0]['primaryEmail'];

                    $_SESSION['login']['id'] = $user[0]['agentId'];

                    $_SESSION['login']['agent_id'] = $user[0]['agentId'];

                    $_SESSION['login']['user_type'] = $user_type;

                    $_SESSION['login']['last_login'] = $user[0]['lastLogin'];

                    $_SESSION['login']['last_login_ip'] = $user[0]['lastLoginIP'];

                    $_SESSION['login']['agencyName'] = $user[0]['agencyName'];

                    $_SESSION['login']['logo'] = $user[0]['agentLogo'];

                    $_SESSION['login']['valid_till'] = $user[0]['valid_till'];

                    $_SESSION['login']['valid_till'] = $user[0]['valid_till'];
                    $_SESSION['login']['crm_uses_type'] = $user[0]['crm_uses_type'];

                    if (!empty($user[0]['file_allowed'])) {

                        $_SESSION['login']['file_allowed'] = $user[0]['file_allowed'];

                    } else {

                        $_SESSION['login']['file_allowed'] = 2048;
                    }


                    $agent_account_settings = $db->select('*')
                        ->from('agent_settings')
                        ->where(array('agent_id' => $_SESSION['login']['id']))
                        ->fetch_first();


                    $_SESSION['login']['whatsapp_link_enabled'] = $agent_account_settings['whatsapp_link_enabled'];
                    $_SESSION['login']['google_calendar_link_enabled'] = $agent_account_settings['google_calendar_link_enabled'];
                    $_SESSION['login']['agent_logo_enabled'] = $agent_account_settings['agent_logo_enabled'];
                    $_SESSION['login']['course_compare_enabled'] = $agent_account_settings['course_compare_enabled'];




                    $_SESSION['login']['data_consumed'] = $objAgent->agent_consumed_data($_SESSION['login']['id']);


                    if ($user[0]['lastLoginIP'] == '') {
                        $_SESSION['login']['USER'] = 'NEW';
                    }


                    $db->where(array('agentId' => $_SESSION['login']['id']))
                        ->update('agent', array('lastLogin' => 'now()', 'lastLoginIP' => $_SERVER['REMOTE_ADDR']));


                    if ($_SESSION['login']['USER'] == 'NEW') {
                        echo "<script>window.location.href='welcome_screen.php';</script>";
                        exit;


                    }

                    if ($_SESSION['login']['user_type'] == 7) {

                        echo "<script>window.location.href='agents/add-representing-country.php';</script>";
                        exit;

                    } else {

                        echo "<script>window.location.href='agents/agents-dashboard.php';</script>";
                        exit;
                    }


                }

            }
        }

        // End of agent login

        // user type 2 means user is Branch office login


        if ($user_type == 2) {

            $db->from('branches')
                ->select()
                ->where(array('email' => $user, 'password' => encrypt($password)));

            $user = $db->fetch();

            if (empty($user[0]['branch_id'])) {


                $_SESSION['error']['msg'] = '<font color="#fff">User and Password seems incorrect! Please try again!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your email not verified! Please try after email verification!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['status'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }







            if (empty($_SESSION['error']['msg']) && count($user) > 0) {

                $db->select(array('agent.agencyName', 'agent.crm_uses_type', 'agent.check_login_ip', 'agent.agentLogo', 'agent.agentId', 'branches.email',
                    'agent.file_allowed', 'agent.valid_till', 'agent.valid_till','agent.agentStatus'))
                    ->from('agent')
                    ->join('branches', 'branches.agent_id=agent.agentId', 'INNER')
                    ->where(array('branches.branch_id' => $user[0]['branch_id']));

                $agent = $db->fetch();


                if (in_array($agent[0]['crm_uses_type'], array('N', 'L'))) {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login</font>";

                }


                // Account Expire Code
                $fromDate = date("Y-m-d");

                $valid_till = date("Y-m-d", strtotime($agent[0]['valid_till'] . " +15 day"));


                if (empty($_SESSION['error']['msg']) && $valid_till < $fromDate) {

                    $rdr_content = urlencode(encrypt("UCRM00" . $agent[0]['agentId']));

                    $_SESSION['error']['msg'] = "<font>Your Account has been expired on " . date("Y-m-d", strtotime($agent[0]['valid_till'])) . " . Please contact our administrator or write to us at support@uniagents.com!</font>";

                  //  echo "<script>window.location.href='agent-account-expired.php?rdr=" . $rdr_content . "';</script>";
                   // exit;
                }


                $allowed_ip_address = $objAgent->allowed_ip_address($agent[0]['agentId']);

                $ip_address_checked = 'N';

                if ($user[0]['login_ip_check'] == 'N' || !count($allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                } else if ((count($allowed_ip_address) && $user[0]['login_ip_check'] == 'Y') && in_array($user_ip, $allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                }





                if (empty($_SESSION['error']['msg']) && $ip_address_checked == 'N') {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login from this IP( $user_ip )</font>";

                }


                if (empty($_SESSION['error']['msg']) && $agent[0]['agentStatus'] != 'A') {


                    $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
                }


                if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] == 'Y' && $ip_address_checked == 'Y') {


                    $_SESSION['login']['branch_name'] = $user[0]['name'];

                    $_SESSION['login']['email'] = $user[0]['email'];

                    $_SESSION['login']['id'] = $user[0]['branch_id'];

                    $_SESSION['login']['agent_id'] = $user[0]['agent_id'];

                    $_SESSION['login']['user_type'] = $user_type;

                    $_SESSION['login']['last_login'] = $user[0]['last_login'];

                    $_SESSION['login']['last_login_ip'] = $user[0]['last_login_ip'];


                    $db->select()
                        ->from('agent')
                        ->where(array('agentId' => $user[0]['agent_id']));

                    $agent = $db->fetch();


                    $_SESSION['login']['agencyName'] = $agent[0]['agencyName'];

                    $_SESSION['login']['logo'] = $agent[0]['agentLogo'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['crm_uses_type'] = $agent[0]['crm_uses_type'];


                    if (!empty($agent[0]['file_allowed'])) {

                        $_SESSION['login']['file_allowed'] = $agent[0]['file_allowed'];
                    } else {

                        $_SESSION['login']['file_allowed'] = 2048;

                    }


                    $agent_account_settings = $db->select('*')
                        ->from('agent_settings')
                        ->where(array('agent_id' => $_SESSION['login']['agent_id']))
                        ->fetch_first();


                    $_SESSION['login']['whatsapp_link_enabled'] = $agent_account_settings['whatsapp_link_enabled'];
                    $_SESSION['login']['google_calendar_link_enabled'] = $agent_account_settings['google_calendar_link_enabled'];
                    $_SESSION['login']['agent_logo_enabled'] = $agent_account_settings['agent_logo_enabled'];
                    $_SESSION['login']['course_compare_enabled'] = $agent_account_settings['course_compare_enabled'];


                    $_SESSION['login']['data_consumed'] = $objAgent->agent_consumed_data($_SESSION['login']['agent_id']);

                    $db->where(array('branch_id' => $_SESSION['login']['id']))
                        ->update('branches', array('last_login' => 'now()', 'last_login_ip' => $_SERVER['REMOTE_ADDR']));

                    echo "<script>window.location.href='branch-office/branch-dashboard.php';</script>";
                    exit;

                }

            }

        }

        // End of Branch office login

        // user type 3 means user is Counsellor login


        if ($user_type == 3) {

            $db->from('counselor')
                ->select()
                ->where(array('email' => $user, 'password' => encrypt($password)));

            $user = $db->fetch();


            if (empty($user[0]['id'])) {

                $_SESSION['error']['msg'] = '<font>User and Password seems incorrect! Please try again!</font>';

            }


            if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your email not verified! Please try after email verification!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['status'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }




            if (empty($_SESSION['error']['msg']) && count($user) > 0) {

                $db->select(array('agent.agencyName', 'agent.crm_uses_type', 'agent.agentLogo', 'agent.agentId', 'branches.email', 'agent.file_allowed',
                    'agent.valid_till', 'agent.valid_till','agent.agentStatus'))
                    ->from('agent')
                    ->join('branches', 'branches.agent_id=agent.agentId', 'INNER')
                    ->where(array('branches.branch_id' => $user[0]['branch_id']));


                $agent = $db->fetch();

                // Account Expire Code

                $fromDate = date("Y-m-d");

                $valid_till = date("Y-m-d", strtotime($agent[0]['valid_till'] . " +15 day"));


                if (empty($_SESSION['error']['msg']) && $valid_till < $fromDate) {
                    $rdr_content = urlencode(encrypt("UCRM00" . $agent[0]['agentId']));

                    $_SESSION['error']['msg'] = "<font>Your Account has been expired on " . date("Y-m-d", strtotime($agent[0]['valid_till'])) . " . Please contact our administrator or write to us at support@uniagents.com!</font>";

                  //  echo "<script>window.location.href='agent-account-expired.php?rdr=" . $rdr_content . "';</script>";
                  //  exit;
                }

                //========= Check IP Address In Array =============//

                $allowed_ip_address = $objAgent->allowed_ip_address($agent[0]['agentId']);

                $ip_address_checked = 'N';

                if ($user[0]['login_ip_check'] == 'N' || !count($allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                } else if ((count($allowed_ip_address) && $user[0]['login_ip_check'] == 'Y') && in_array($user_ip, $allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                }


                if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] != 'Y') {

                    $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
                }


                if (empty($_SESSION['error']['msg']) && $ip_address_checked == 'N') {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login from this IP( $user_ip )</font>";

                }


                if (empty($_SESSION['error']['msg']) && $agent[0]['agentStatus'] != 'A') {


                    $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
                }


                if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] == 'Y' && $ip_address_checked == 'Y') {

                    $_SESSION['login']['counsellor_name'] = $user[0]['name'];

                    $_SESSION['login']['email'] = $user[0]['email'];

                    $_SESSION['login']['id'] = $user[0]['id'];

                    $_SESSION['login']['branch_id'] = $user[0]['branch_id'];

                    $_SESSION['login']['user_type'] = $user_type;

                    $_SESSION['login']['last_login'] = $user[0]['last_login'];

                    $_SESSION['login']['last_login_ip'] = $user[0]['last_login_ip'];

                    $_SESSION['login']['agent_id'] = $agent[0]['agentId'];

                    $_SESSION['login']['agencyName'] = $agent[0]['agencyName'];

                    $_SESSION['login']['logo'] = $agent[0]['agentLogo'];

                    $_SESSION['login']['branch_email'] = $agent[0]['email'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['crm_uses_type'] = $agent[0]['crm_uses_type'];

                    if (!empty($agent[0]['file_allowed'])) {

                        $_SESSION['login']['file_allowed'] = $agent[0]['file_allowed'];

                    } else {

                        $_SESSION['login']['file_allowed'] = 2048;

                    }


                    $agent_account_settings = $db->select('*')
                        ->from('agent_settings')
                        ->where(array('agent_id' => $_SESSION['login']['agent_id']))
                        ->fetch_first();


                    $_SESSION['login']['whatsapp_link_enabled'] = $agent_account_settings['whatsapp_link_enabled'];
                    $_SESSION['login']['google_calendar_link_enabled'] = $agent_account_settings['google_calendar_link_enabled'];
                    $_SESSION['login']['agent_logo_enabled'] = $agent_account_settings['agent_logo_enabled'];
                    $_SESSION['login']['course_compare_enabled'] = $agent_account_settings['course_compare_enabled'];


                    $_SESSION['login']['data_consumed'] = $objAgent->agent_consumed_data($_SESSION['login']['agent_id']);

                    $db->where(array('id' => $_SESSION['login']['id']))
                        ->update('counselor', array('last_login' => 'now()', 'last_login_ip' => $_SERVER['REMOTE_ADDR']));

                    echo "<script>window.location.href='counsellor-panel/counsellor-dashboard.php';</script>";
                    exit;

                }

            }

        }

        // End of Counsellor login

        // user type 4 means user is Processing Office login

        if ($user_type == 4) {

            $db->from('processing_office')
                ->select()
                ->where(array('email' => $user, 'password' => encrypt($password)));

            $user = $db->fetch();


            if (empty($user[0]['id'])) {

                $_SESSION['error']['msg'] = '<font color="#fff">User and Password seems incorrect! Please try again!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your email not verified! Please try after email verification!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['status'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }



            if (empty($_SESSION['error']['msg']) && count($user) > 0) {

                $db->from('agent')
                    ->select()
                    ->where(array('agentId' => $user[0]['agent_id']));

                $agent = $db->fetch();


                if (in_array($agent[0]['crm_uses_type'], array('N', 'L'))) {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login</font>";

                }


                // Account Expire Code
                $fromDate = date("Y-m-d");
                $valid_till = date("Y-m-d", strtotime($agent[0]['valid_till'] . " +15 day"));

                if (empty($_SESSION['error']['msg']) && $valid_till < $fromDate) {
                    $rdr_content = urlencode(encrypt("UCRM00" . $agent[0]['agentId']));

                    $_SESSION['error']['msg'] = "<font>Your Account has been expired on " . date("Y-m-d", strtotime($agent[0]['valid_till'])) . " . Please contact our administrator or write to us at support@uniagents.com!</font>";

                   // echo "<script>window.location.href='agent-account-expired.php?rdr=" . $rdr_content . "';</script>";
                  //  exit;
                }


                //========= Check IP Address In Array =============//

                $allowed_ip_address = $objAgent->allowed_ip_address($agent[0]['agentId']);

                $ip_address_checked = 'N';

                if ($user[0]['login_ip_check'] == 'N' || !count($allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                } else if ((count($allowed_ip_address) && $user[0]['login_ip_check'] == 'Y') && in_array($user_ip, $allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                }





                if (empty($_SESSION['error']['msg']) && $ip_address_checked == 'N') {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login from this IP( $user_ip )</font>";

                }


                if (empty($_SESSION['error']['msg']) && $agent[0]['agentStatus'] != 'A') {


                    $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
                }


                if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] == 'Y' && $ip_address_checked == 'Y') {

                    $_SESSION['login']['processing_office'] = $user[0]['name'];

                    $_SESSION['login']['email'] = $user[0]['email'];

                    $_SESSION['login']['id'] = $user[0]['id'];

                    $_SESSION['login']['agent_id'] = $user[0]['agent_id'];

                    $_SESSION['login']['user_type'] = $user_type;

                    $_SESSION['login']['last_login'] = $user[0]['last_login'];

                    $_SESSION['login']['last_login_ip'] = $user[0]['last_login_ip'];

                    $_SESSION['login']['agencyName'] = $agent[0]['agencyName'];

                    $_SESSION['login']['logo'] = $agent[0]['agentLogo'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['crm_uses_type'] = $agent[0]['crm_uses_type'];

                    if (!empty($agent[0]['file_allowed'])) {

                        $_SESSION['login']['file_allowed'] = $agent[0]['file_allowed'];


                    } else {

                        $_SESSION['login']['file_allowed'] = 2048;
                    }


                    $agent_account_settings = $db->select('*')
                        ->from('agent_settings')
                        ->where(array('agent_id' => $_SESSION['login']['agent_id']))
                        ->fetch_first();


                    $_SESSION['login']['whatsapp_link_enabled'] = $agent_account_settings['whatsapp_link_enabled'];
                    $_SESSION['login']['google_calendar_link_enabled'] = $agent_account_settings['google_calendar_link_enabled'];
                    $_SESSION['login']['agent_logo_enabled'] = $agent_account_settings['agent_logo_enabled'];
                    $_SESSION['login']['course_compare_enabled'] = $agent_account_settings['course_compare_enabled'];


                    $_SESSION['login']['data_consumed'] = $objAgent->agent_consumed_data($_SESSION['login']['agent_id']);


                    $db->where(array('id' => $_SESSION['login']['id']))
                        ->update('processing_office', array('last_login' => 'now()', 'last_login_ip' => $_SERVER['REMOTE_ADDR']));


                    echo "<script>window.location.href='processing_office/processing-dashboard.php';</script>";
                    exit;

                }

            }

        }

        // End of Processing Office login

        // user type 5 means user is Front Office login

        if ($user_type == 5) {

            $db->from('front_office')
                ->select()
                ->where(array('email' => $user, 'password' => encrypt($password)));

            $user = $db->fetch();


            if (empty($user[0]['id'])) {


                $_SESSION['error']['msg'] = '<font>User and Password seems incorrect! Please try again!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your user acount is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }



            if (empty($_SESSION['error']['msg']) && $user[0]['status'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }








            if (empty($_SESSION['error']['msg']) && count($user) > 0) {

                $db->from('agent')
                    ->join('front_office', 'front_office.agent_id=agent.agentId', 'INNER')
                    ->select(array('agent.agencyName', 'agent.crm_uses_type', 'agent.agentLogo', 'agent.agentId',
                        'front_office.email', 'agent.file_allowed', 'agent.valid_till',
                        'agent.valid_till','agent.agentStatus'))
                    ->where(array('front_office.branch_id' => $user[0]['branch_id']));

                $agent = $db->fetch();


                if (in_array($agent[0]['crm_uses_type'], array('N', 'L'))) {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login</font>";

                }

                $fromDate = date("Y-m-d");
                $valid_till = date("Y-m-d", strtotime($agent[0]['valid_till'] . " +15 day"));

                if (empty($_SESSION['error']['msg']) && $valid_till < $fromDate) {
                    $rdr_content = urlencode(encrypt("UCRM00" . $agent[0]['agentId']));

                    $_SESSION['error']['msg'] = "<font>Your Account has been expired on " . date("Y-m-d", strtotime($agent[0]['valid_till'])) . " . Please contact our administrator or write to us at support@uniagents.com!</font>";

                  //  echo "<script>window.location.href='agent-account-expired.php?rdr=" . $rdr_content . "';</script>";
                  //  exit;
                }

                //========= Check IP Address In Array =============//

                $allowed_ip_address = $objAgent->allowed_ip_address($agent[0]['agentId']);

                $ip_address_checked = 'N';

                if ($user[0]['login_ip_check'] == 'N' || !count($allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                } else if ((count($allowed_ip_address) && $user[0]['login_ip_check'] == 'Y') && in_array($user_ip, $allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                }




                if (empty($_SESSION['error']['msg']) && $ip_address_checked == 'N') {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login from this IP( $user_ip )</font>";

                }


                if (empty($_SESSION['error']['msg']) && $agent[0]['agentStatus'] != 'A') {


                    $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
                }


                if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] == 'Y' && $ip_address_checked == 'Y') {

                    $_SESSION['login']['front_office_name'] = $user[0]['name'];

                    $_SESSION['login']['email'] = $user[0]['email'];

                    $_SESSION['login']['id'] = $user[0]['id'];

                    $_SESSION['login']['branch_id'] = $user[0]['branch_id'];

                    $_SESSION['login']['user_type'] = $user_type;

                    $_SESSION['login']['last_login'] = $user[0]['last_login'];

                    $_SESSION['login']['last_login_ip'] = $user[0]['last_login_ip'];

                    $_SESSION['login']['agent_id'] = $agent[0]['agentId'];

                    $_SESSION['login']['agencyName'] = $agent[0]['agencyName'];

                    $_SESSION['login']['logo'] = $agent[0]['agentLogo'];

                    $_SESSION['login']['branch_email'] = $agent[0]['email'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['crm_uses_type'] = $agent[0]['crm_uses_type'];

                    if (!empty($agent[0]['file_allowed'])) {

                        $_SESSION['login']['file_allowed'] = $agent[0]['file_allowed'];

                    } else {

                        $_SESSION['login']['file_allowed'] = 2048;

                    }


                    $agent_account_settings = $db->select('*')
                        ->from('agent_settings')
                        ->where(array('agent_id' => $_SESSION['login']['agent_id']))
                        ->fetch_first();


                    $_SESSION['login']['whatsapp_link_enabled'] = $agent_account_settings['whatsapp_link_enabled'];
                    $_SESSION['login']['google_calendar_link_enabled'] = $agent_account_settings['google_calendar_link_enabled'];
                    $_SESSION['login']['agent_logo_enabled'] = $agent_account_settings['agent_logo_enabled'];
                    $_SESSION['login']['course_compare_enabled'] = $agent_account_settings['course_compare_enabled'];




                    $_SESSION['login']['data_consumed'] = $objAgent->agent_consumed_data($_SESSION['login']['agent_id']);


                    $db->where(array('id' => $_SESSION['login']['id']))
                        ->update('front_office', array('last_login' => 'now()', 'last_login_ip' => $_SERVER['REMOTE_ADDR']));


                    echo "<script>window.location.href='front_office/front-dashboard.php';</script>";
                    exit;

                }
            }
        }


        // End of Front Office login

        // user type 6 means user is Commission Manager login

        if ($user_type == 6) {

            $db->from('commission_manager')
                ->select()
                ->where(array('email' => $user, 'password' => encrypt($password)));

            $user = $db->fetch();


            if (empty($user[0]['id'])) {


                $_SESSION['error']['msg'] = '<font color="#fff">User and Password seems incorrect! Please try again!</font>';
            }


            if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }




            if (empty($_SESSION['error']['msg']) && $user[0]['status'] != 'Y') {


                $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
            }




            if (empty($_SESSION['error']['msg']) && count($user) > 0) {

                $db->from('agent')
                    ->select()
                    ->where(array('agentId' => $user[0]['agent_id']));

                $agent = $db->fetch();


                if (in_array($agent[0]['crm_uses_type'], array('N', 'L'))) {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login</font>";

                }


                // Account Expire
                $fromDate = date("Y-m-d");
                $valid_till = date("Y-m-d", strtotime($agent[0]['valid_till'] . " +15 day"));

                if (empty($_SESSION['error']['msg']) && $valid_till < $fromDate) {
                    $rdr_content = urlencode(encrypt("UCRM00" . $agent[0]['agentId']));

                    $_SESSION['error']['msg'] = "<font>Your Account has been expired on " . date("Y-m-d", strtotime($agent[0]['valid_till'])) . " . Please contact our administrator or write to us at support@uniagents.com!</font>";

                   // echo "<script>window.location.href='agent-account-expired.php?rdr=" . $rdr_content . "';</script>";
                  //  exit;
                }
                // Code End




                //========= Check IP Address In Array =============//

                $allowed_ip_address = $objAgent->allowed_ip_address($agent[0]['agentId']);

                $ip_address_checked = 'N';

                if ($user[0]['login_ip_check'] == 'N' || !count($allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                } else if ((count($allowed_ip_address) && $user[0]['login_ip_check'] == 'Y') && in_array($user_ip, $allowed_ip_address)) {

                    $ip_address_checked = 'Y';

                }


                if (empty($_SESSION['error']['msg']) && $ip_address_checked == 'N') {

                    $_SESSION['error']['msg'] = "<font>You are not allowed to login from this IP( $user_ip )</font>";

                }


                if (empty($_SESSION['error']['msg']) && $agent[0]['agentStatus'] != 'A') {


                    $_SESSION['error']['msg'] = '<font>Your user account is not activated. Please contact your administrator or write to us at support@uniagents.com!</font>';
                }


                if (empty($_SESSION['error']['msg']) && $user[0]['email_verified'] == 'Y' && $ip_address_checked == 'Y') {

                    $_SESSION['login']['manager_name'] = $user[0]['name'];

                    $_SESSION['login']['email'] = $user[0]['email'];

                    $_SESSION['login']['id'] = $user[0]['id'];

                    $_SESSION['login']['agent_id'] = $user[0]['agent_id'];

                    $_SESSION['login']['user_type'] = $user_type;

                    $_SESSION['login']['last_login'] = $user[0]['last_login'];

                    $_SESSION['login']['last_login_ip'] = $user[0]['last_login_ip'];

                    $_SESSION['login']['agencyName'] = $agent[0]['agencyName'];

                    $_SESSION['login']['logo'] = $agent[0]['agentLogo'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['valid_till'] = $agent[0]['valid_till'];

                    $_SESSION['login']['crm_uses_type'] = $agent[0]['crm_uses_type'];

                    if (!empty($agent[0]['file_allowed']))

                        $_SESSION['login']['file_allowed'] = $agent[0]['file_allowed'];

                    else {
                        $_SESSION['login']['file_allowed'] = 2048;
                    }


                    $agent_account_settings = $db->select('*')
                        ->from('agent_settings')
                        ->where(array('agent_id' => $_SESSION['login']['agent_id']))
                        ->fetch_first();


                    $_SESSION['login']['whatsapp_link_enabled'] = $agent_account_settings['whatsapp_link_enabled'];
                    $_SESSION['login']['google_calendar_link_enabled'] = $agent_account_settings['google_calendar_link_enabled'];
                    $_SESSION['login']['agent_logo_enabled'] = $agent_account_settings['agent_logo_enabled'];
                    $_SESSION['login']['course_compare_enabled'] = $agent_account_settings['course_compare_enabled'];



                    $_SESSION['login']['data_consumed'] = $objAgent->agent_consumed_data($_SESSION['login']['agent_id']);


                    $db->where(array('id' => $_SESSION['login']['id']))
                        ->update('commission_manager', array('last_login' => 'now()', 'last_login_ip' => $_SERVER['REMOTE_ADDR']));


                    echo "<script>window.location.href='manager/manager-dashboard.php';</script>";

                }
            }

        }
        // End of Commission Manager
    }

}

?>


<!DOCTYPE HTML>


<html>


<head>


    <meta charset="UTF-8">


    <meta http-equiv="X-UA-Compatible" content="IE=Edge">


    <meta name="viewport" content="width=device-width, initial-scale=1">


    <title>Agents Panel</title>

    <link rel="stylesheet" type="text/css" href="style/style.css">


    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <!--

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    -->


    <link rel="stylesheet" type="text/css" href="css/style.css"/>


    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>


</head>


<body style="background:#fff;">


<?php include('includes/main-header.php'); ?>

<!--
<p style="color:#bb0622;text-align:center;font-size:18px;font-weight:bold;max-width:80%;margin:30px auto 0;line-height:30px;">Note: Agent crm is upgrading its technologies and for that purpose our systems will be down on 1st JULY (Sunday).</p>

-->
<style type="text/css">
    .blinking {
        animation: blinkingText 0.5s infinite;
    }

    @keyframes blinkingText {
        0% {
            color: red;
        }
        49% {
            color: #770000;
        }
        50% {
            color: #770000;
        }
        99% {
            color: #770000;
        }
        100% {
            color: red;
        }
    }

    @media (min-width: 200px) and (max-width: 767px) {
        .signin-container {
            margin: 0 auto 40px auto;
        }
    }
</style>


<style type="text/css">
    .signin-container .container{
        padding: 10px 20px;
    }
    .enquiry-right{
        float: right;
        width: 40%;
        color: #4f76a3;
        font-weight: bold;
    }
    .enquiry-right-inner{
        width: 100%;
        padding-left: 20px;
        text-align: center;
        margin: 0px 0 0 0;
    }
    .signin-container .signin-right .label{
        margin-top: 0px;
    }
    
    @media (min-width: 200px) and (max-width: 767px) {
        .enquiry-right{
            width: 100%;
        }
        .enquiry-right-inner{
            padding-left:0px;
        }
    }
</style>

 <!--<p class="blinking" style="font-size:20px;padding:20px;text-align:center;font-weight:bold;">
   The latest version 5.0 is going to make a huge difference to your experience in 2019. So on 30th Dec, Sunday the CRM will be down for upgrading to ver 5.0
    
    &nbsp;
</p>-->


<div class="signin-container">

    <div class="container">


        <!-- right -->


        <div class="signin-right">


            <div class="login-box">


                <div class="login-head"><i class="fa fa-user"></i> Member Login</div>


                <form action="" method="post" autocomplete="off">


                    <?php


                    if (isset($_SESSION[error]['msg']))


                        echo '<div class="label" style="color:#fff;text-shadow:1px 1px 0 #333;text-align:center;margin-top:0;">' . $_SESSION[error]['msg'] . '</div>';


                    unset($_SESSION[error]['msg']);


                    ?>


                    <div class="form">


                        <span class="label">User Name <span class="required">*</span></span>


                        <input name="user" id="user" type="text" required/>


                    </div>


                    <div class="form">


                        <span class="label">Password <span class="required">*</span></span>


                        <input name="password" id="password" type="password" required/>


                    </div>


                    <div class="form">


                        <span class="label">Login Type <span class="required">*</span></span>


                        <?php echo $objAgent->user_type('user_type', '', 'required'); ?>


                    </div>


                    <div class="form">


                        <div class="pull-left"><a href="forgot_password.php" class="forgot"
                                                  title="Forget Password Of Super Admin" rel="[facebox]"
                                                  rev="iframe|520|320">Forget Username or Password?</a></div>


                        <div class="pull-right">
                            <button type="submit" name="submit" class="btn_signin"><i class="fa fa-key"></i> Sign In
                            </button>
                        </div>


                        <div class="clearfix"></div>
                        <span class="label"><i class="fa fa-check-circle"></i> BY clicking on register you accept the <a
                                    href="terms.php" rel="[facebox]" rev="iframe|520|320" class="terms">Terms and Conditions</a></span>

                    </div>


                </form>


            </div>


        </div>


        <!-- right -->


        <!-- left -->


        <div class="signin-left">
            <div class="head">Agent CRM</div>


            <p>Uniagents offers educational consultants an opportunity to experience flawless and streamlined office
                operations. By using our Agent CRM the consultants will be able to manage and control all their branch
                offices and counsellors working in respective offices. You can manage all your institutions which you
                represent, define your own country specific application process, track all your applications and enjoy
                the flexibility, transparency and control of office operations as never before.</p>


            <p class="highlights"><img src="images/tracking.png" width="30" height="30" align="absmiddle"> Experience
                better Conversion Rate by Live tracking</p>


            <p class="highlights"><img src="images/application.png" width="30" height="30" align="absmiddle"> Allow
                students to track application status from your website</p>


            <p class="highlights"><img src="images/lead-management.png" width="30" height="30" align="absmiddle">
                Application and Lead Management</p>


            <p class="highlights"><img src="images/crm.png" width="30" height="30" align="absmiddle"> Integrate your
                website enquiries with the CRM for better management and follow ups</p>


            <p class="highlights"><a href="https://www.youtube.com/watch?v=E7KG7nsWY6A&feature=youtu.be"
                                     target="_blank"><img src="images/info.png" width="30" height="30"
                                                          align="absmiddle"> Easy understanding of features and usage of
                    the system Watch sample video</a></p>


            <p class="highlights"><a href="https://www.youtube.com/watch?v=WBftBa9E3nM" target="_blank"> <img
                            src="images/youtube.png" width="30" height="30" align="absmiddle">Watch how our Agent CRM
                    can help your business</a></p>



            <a class="learn-more" href="https://www.uniagents.com/agents-crm.php" target="_blank"><strong>Learn More
                benefits of Agent CRM</strong> <i class="fa fa-arrow-right"></i></a>


        </div>

        <div class="clearfix"></div>
        <!-- left -->
        <div class="enquiry-right">
            <div class="enquiry-right-inner">
                For any feedback or concerns please write to us at <a href="mailto:grievance@uniagents.com">grievance@uniagents.com</a>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>


</div>


<div class="spacer"></div>


<div class="powered-by">&copy; Copyright UniAgents.com</div>


<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>


<script type="text/javascript" src="js/jquery_cookie_plugin.js"></script>


<!-- facebox -->


<link href="facebox/facebox.css" media="screen" rel="stylesheet" type="text/css"/>


<script src="facebox/facebox.js" type="text/javascript"></script>


<script type="text/javascript">


    $(document).ready(function () {


        $('a[rel*=facebox]').facebox();


    });


</script>


<!-- facebox -->


<?php
unset($_SESSION['welcome']['popup']);
if (!isset($_SESSION['welcome']['popup']))
    $_SESSION['welcome']['popup'] = 1;
if ($_SESSION['welcome']['popup'] == 1) {
    ?>


    <script>
        /*




         $(document).ready(function(){


         // $.facebox({ ajax: 'welcome.php' })


         // $.facebox('sanjay');


         alert("CRM version has been upgraded to 5.2, Please press Ctrl+F5 for Microsoft Window user, For Mac User please press Command + Shift+ R from your Keyboard for better UI");


         });


         */


    </script>


    <?php

    $_SESSION['welcome']['popup'] = 2;
}
?>


</body>


</html>


