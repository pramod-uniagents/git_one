<?php

require_once("../includes/config.php");
require_once("../includes/function.php");

require_once("../class/classDb.php");

//require_once("../class/classDbDemo.php");
$db = new Database();

require_once("../class/agentClass.php");
$objAgent = new agent();

require_once("../class/admin.php");
$objAdmin = new admin();

require_once("../class/commonClass.php");
$objCommon = new common();

// To check agent is login or not
$objAdmin->check_admin_login();

if (isset($_GET['id'])) {
    $agent_id = decrypt($_GET['id']);

    // pr($agent_id);

    $sql = "SELECT * FROM agent WHERE agentId='{$agent_id}' ";
    $db->query($sql);
    $record = $db->fetch();


    $sql = "SELECT * FROM agent_settings WHERE agent_id='$agent_id' ORDER by id DESC ";
    $db->query($sql);
    $agent_settings = $db->fetch_first();

}

if (isset($_GET['type']) AND $_GET['type'] == 'invoice_delete') {

    $data = array('admin_invoice' => '');

    $db->where('agentId', $agent_id);
    $db->update('agent', $data);

    @unlink("../admin_invoice/" . $record[0]['admin_invoice']);
    echo "<script>window.location.href='agent-edit-details.php?id=" . urlencode($_GET['id']) . "';</script>";
    exit;
}

// $country_array = $objAgent->country_array();
// pr($country_array);

if (isset($_POST['submit'])) {

    // pr($_FILES);
    // pr($_POST);
    // pr($_SESSION);
    // di();

    extract($_POST);

    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $contact_email)) {
        $_SESSION[error]['msg'] = '<font color="red">Please enter valid email of contact person!</font>';

    } else if ($password == '') {
        $_SESSION[error]['msg'] = '<font color="red">Please enter the password!</font>';

    } else if ($_POST['agencyName'] == '') {
        $_SESSION[error]['msg'] = '<font color="red">Please enter Agency name!</font>';

    } else if ($_POST['country'] == '') {
        $_SESSION[error]['msg'] = '<font color="red">Please select country!</font>';

    } else if ($_POST['contactPerson'] == '') {
        $_SESSION[error]['msg'] = '<font color="red">Please enter contact person!</font>';

    } else if ($_POST['designation'] == '') {
        $_SESSION[error]['msg'] = '<font color="red">Please enter designation!</font>';

    } else if ($_POST['mobile'] == '') {
        $_SESSION[error]['msg'] = '<font color="red">Please enter mobile!</font>';

    } else if ($_POST['certified'] == 'Y' && trim($_POST['certified_url']) == "") {
        $_SESSION[error]['msg'] = '<font color="red">Please enter Certified Url!</font>';

    } else {

        $old_username = trim($old_username);
        $username = trim($username);

        if ($old_username != $username) {

            $db->where(array('username' => $username));
            $db->from('agent');
            $record_user = $db->fetch();

        } else {
            $record_user = array();

        }

        // echo $db->last_query();

        // pr($_FILES);
        $random_no = mt_rand(100, 9999);

        if (isset($_FILES['image']['name']) AND $_FILES['image']['name'] != '') {

            $ext = end((explode(".", $_FILES['image']['name'])));

            $new_name = $random_no . "_agentid_" . $_SESSION[login]['id'] . "_date_" . date("dmYHis") . "." . $ext;
            $upload_dir_main = "../agent_logo/" . $new_name;
            $upload_dir_thumbs = "../agent_logo/" . $new_name;
            chmod("../agent_logo", 0775);
            move_uploaded_file($_FILES["image"]["tmp_name"], $upload_dir_main);


            require_once("../class/ImageResize.php");
            $image = new SimpleImage();
            $image->load($upload_dir_main);
            $image->resizeToWidth(200);
            $image->save($upload_dir_thumbs);

            $logo = $new_name;
            @unlink("../agent_logo/" . $record[0]['agentLogo']);
        } else {

            $logo = $record[0]['agentLogo'];
        }


        if (isset($_FILES['invoice']['name']) AND $_FILES['invoice']['name'] != '') {

            $ext = end((explode(".", $_FILES['invoice']['name'])));

            $new_name = $random_no . "_invoice_agentid_" . $_SESSION[login]['id'] . "_date_" . date("dmYHis") . "." . $ext;
            $upload_dir_main = "../admin_invoice/" . $new_name;
            $upload_dir_thumbs = "../admin_invoice/" . $new_name;
            chmod("../admin_invoice", 0775);
            move_uploaded_file($_FILES["invoice"]["tmp_name"], $upload_dir_main);


            // require_once("../class/ImageResize.php");
            // $image = new SimpleImage();
            // $image->load($upload_dir_main);
            // $image->resizeToWidth(200);
            // $image->save($upload_dir_thumbs);

            $admin_invoice = $new_name;
            @unlink("../admin_invoice/" . $record[0]['admin_invoice']);
        } else {

            $admin_invoice = $record[0]['admin_invoice'];
        }

        if (count($record_user) > 0) {

            $_SESSION[error]['msg'] = '<font color="red">This user is not available!</font>';

        } else {

            if ($certified == 'N') {
                $certified_url = "";
            }

            $data = array(
                'admin_invoice' => $admin_invoice,
                'emailAddress' => trim($emailAddress),
                'username' => $username,
                'password' => encrypt($password),
                'contact_email' => $contact_email,
                'secondryEmail' => $secondryEmail,
                'agencyName' => $agencyName,
                'address' => $address,
                'area' => $area,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'pinCode' => $pinCode,
                'owner' => $owner,
                'website' => $website,
                'contactPerson' => $contactPerson,
                'designation' => $designation,
                'directPhoneNumber' => $directPhoneNumber,
                'phoneNumber' => $phoneNumber,
                'mobile' => $mobile,
                'website' => $website,
                'skypeId' => $skypeId,
                'facebookUrl' => $facebookUrl,
                'linkdinUrl' => $linkdinUrl,
                'twitterUrl' => $twitterUrl,
                'googlePlusUrl' => $googlePlusUrl,
                'file_allowed' => $file_allowed,
                'subscription_date' => $subscription_date,
                'valid_till' => $valid_till,
                'payment' => $payment,
                'details' => $details,
                'agentLogo' => $logo,
                'LastUpdated' => 'now()',
                'certified' => $certified,
                'certified_url' => $certified_url,
                'remarks' => $remarks
            );

            $data = $objAgent->sanitize_data($data);
            $db->where('agentId', $agent_id);
            $db->update('agent', $data);


            $agent_settings_data = array(
                'mobile_app_name' => $_POST['mobile_app_name'],
                'mobile_app_link_ios' => $_POST['mobile_app_link_ios'],
                'mobile_app_link_android' => $_POST['mobile_app_link_android'],
                'updated_by_id' => $_SESSION['login']['id'],
                'updated_by_type' => $_SESSION['login']['user_type'],
                'update_date' => date('Y-m-d H:i:s'),
            );


            if ($agent_settings['id'] > 0) {


                $db->where(array('id' => $agent_settings['id']))
                    ->update('agent_settings', $agent_settings_data);

            } else {

                $agent_settings_data['added_by_id'] = $_SESSION['login']['id'];
                $agent_settings_data['added_by_type'] = $_SESSION['login']['user_type'];
                $agent_settings_data['add_date'] = date('Y-m-d H:i:s');

                $agent_settings_data['agent_id'] = $agent_id;
                $db->insert('agent_settings', $agent_settings_data);

            }


            $_SESSION[error]['msg'] = '<font color="green">Information is successfully updated!</font>';

            // di();
            echo "<script>window.location.href='agent-edit-details.php?id=" . urlencode($_GET['id']) . "';</script>";
            exit;

        }

    }


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

        <!-- add branch office form -->
        <div class="form-container">


            <div class="form-sub-head">Edit Agent Details</div>
            <?php if (isset($_SESSION[error]['msg'])) { ?>
                <div id="error_msg"><font color="red"><?php echo $_SESSION[error]['msg']; ?> <br><br></font></div>
                <?php unset($_SESSION[error]['msg']);
            } ?>
            <form action="" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Personal Information</legend>
                    <ul class="fields border">
                        <li><span class="half half-head">Agency Name </span>
                            <div class="half no-padding"><input
                                        type="text" required="" placeholder="Agency Name"
                                        value="<?php echo $record[0]['agencyName']; ?>" id="agencyName"
                                        name="agencyName">
                            </div>
                        </li>
                        <li><span class="half half-head">Address</span>
                            <div class="half no-padding"><input type="text"
                                                                placeholder="Address"
                                                                value="<?php echo $record[0]['address']; ?>"
                                                                id="address"
                                                                name="address">
                            </div>
                        </li>
                        <li><span class="half half-head">City</span>
                            <div class="half no-padding"><input type="text"
                                                                placeholder="City"
                                                                value="<?php echo $record[0]['city']; ?>"
                                                                id="city"
                                                                name="city">
                            </div>
                        </li>
                        <li><span class="half half-head">State</span>
                            <div class="half no-padding"><input type="text" placeholder="State"
                                                                value="<?php echo $record[0]['state']; ?>"
                                                                id="state"
                                                                name="state">
                            </div>
                        </li>
                        <li><span class="half half-head">Country</span>
                            <div class="half no-padding">

                                <?php echo $objAgent->country('country', $record[0]['country'], ' '); ?>

                            </div>
                        </li>
                        <li><span class="half half-head">Zipcode</span>
                            <div class="half no-padding"><input type="text"
                                                                placeholder="Pincode"
                                                                value="<?php echo $record[0]['pinCode']; ?>"
                                                                id="pinCode"
                                                                name="pinCode">
                            </div>
                        </li>
                        <li><span class="half half-head">Phone Number</span>
                            <div class="half no-padding"><input
                                        type="text" placeholder="Phone Number"
                                        value="<?php echo $record[0]['phoneNumber']; ?>" id="phoneNumber"
                                        name="phoneNumber">
                            </div>
                        </li>
                        <li><span class="half half-head">Email Address</span>
                            <div class="half no-padding"><i
                                        class="fa fa-envelope icon"></i><input type="email" placeholder="Email"
                                                                               value="<?php echo $record[0]['emailAddress']; ?>"
                                                                               id="emailAddress" name="emailAddress"
                                                                               style="padding:4px;">
                            </div>
                        </li>
                        <li><span class="half half-head">Website</span>
                            <div class="half no-padding"><input type="text"
                                                                placeholder="Website"
                                                                value="<?php echo $record[0]['website']; ?>"
                                                                id="website"
                                                                name="website">
                            </div>
                        </li>
                    </ul>
                </fieldset>
                <fieldset>
                    <legend>Point of contact</legend>
                    <ul class="fields border">
                        <li><span class="half half-head">Contact Person</span>
                            <div class="half no-padding"><input
                                        type="text" required="" placeholder="Contact Person"
                                        value="<?php echo $record[0]['contactPerson']; ?>" id="contactPerson"
                                        name="contactPerson"></div>
                        </li>
                        <li><span class="half half-head">Designation</span>
                            <div class="half no-padding"><input
                                        type="text" required="" placeholder="Designation"
                                        value="<?php echo $record[0]['designation']; ?>" id="designation"
                                        name="designation"></div>
                        </li>
                        <li><span class="half half-head">Email</span>
                            <div class="half no-padding"><i
                                        class="fa fa-envelope icon"></i><input type="email" required=""
                                                                               placeholder="Email of Contact Person"
                                                                               value="<?php echo $record[0]['contact_email']; ?>"
                                                                               id="contact_email" name="contact_email"
                                                                               style="padding:4px;"></div>
                        </li>
                        <li><span class="half half-head">Phone Number</span>
                            <div class="half no-padding"><input
                                        type="text" placeholder="Direct Phone Number"
                                        value="<?php echo $record[0]['directPhoneNumber']; ?>" id="directPhoneNumber"
                                        name="directPhoneNumber"></div>
                        </li>
                        <li><span class="half half-head">Mobile Number</span>
                            <div class="half no-padding"><input
                                        type="text" required="" placeholder="Mobile Number"
                                        value="<?php echo $record[0]['mobile']; ?>" id="mobile" name="mobile"></div>
                        </li>
                        <li><span class="half half-head">Skype Id</span>
                            <div class="half no-padding"><input type="text"
                                                                placeholder="Skype Id"
                                                                value="<?php echo $record[0]['skypeId']; ?>"
                                                                id="skypeId"
                                                                name="skypeId"></div>
                        </li>
                    </ul>
                </fieldset>
                <fieldset>
                    <legend>Login Detail</legend>
                    <ul class="fields border">

                        <li><span class="half half-head">Username</span>
                            <div class="half no-padding">
                                <input type="text" required="" placeholder="Username"
                                       value="<?php echo $record[0]['username']; ?>"
                                       readonly="readonly" id="username" name="username">

                                <input type="hidden" name="old_username" value="<?php echo $record[0]['username']; ?>">

                            </div>

                        </li>

                        <li><span class="half half-head">Password</span>
                            <div class="half no-padding"><input type="text"
                                                                required=""
                                                                value="<?php echo decrypt($record[0]['password']); ?>"
                                                                id="password"
                                                                name="password"
                                                                readonly>
                            </div>
                        </li>


                        <li><span class="half half-head">Email Verified</span>
                            <div class="half ecno-padding">

                                <input type="radio" value="Y"
                                       name="email_verified" <?php if ($record[0]['email_verified'] == 'Y') echo 'checked'; ?>>
                                Yes &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" value="N"
                                       name="email_verified" <?php if ($record[0]['email_verified'] == 'N') echo 'checked'; ?> >
                                No

                            </div>
                        </li>

                        <li>
                            <span class="half half-head">Valid From</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Valid From" name="subscription_date"
                                       id="subscription_date"
                                       value="<?php echo $record[0]['subscription_date']; ?>" autocomplete="off"
                                       required>
                            </div>
                        </li>

                        <script>
                            $("#subscription_date").datepicker({dateFormat: "yy-mm-dd", changeYear: true});
                        </script>


                        <li>
                            <span class="half half-head">Valid To</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Valid To" name="valid_till" id="valid_till"
                                       value="<?php echo $record[0]['valid_till']; ?>" autocomplete="off" required>
                            </div>
                        </li>

                        <script>
                            $("#valid_till").datepicker({dateFormat: "yy-mm-dd", changeYear: true});
                        </script>


                        <li><span class="half half-head">Invoice</span>
                            <div class="half no-padding"><input type="file"
                                                                id="invoice"
                                                                name="invoice">
                            </div>
                        </li>

                        <?php

                        if (!empty(trim($record[0]['admin_invoice']))) {
                            ?>
                            <li><a href="../admin_invoice/<?php echo $record[0]['admin_invoice']; ?>" target="_blank">Download
                                    Invoice</a> <a
                                        href="?type=invoice_delete&id=<?php echo urlencode($_REQUEST['id']); ?>"
                                        onclick="return confirm('Are you sure you want to delete invoice?')"
                                        style="color:red;">&nbsp;&nbsp;&nbsp;<i class="fa fa-trash"></i></a></li>

                        <?php } ?>

                        <li>
                            <span class="half half-head">File Allowed (In MB)</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="File Allowed (Im MB)" name="file_allowed"
                                       id="file_allowed"
                                       value="<?php echo $record[0]['file_allowed']; ?>">
                            </div>
                        </li>

                        <li style="vertical-align:top;">
                            <span class="half half-head">Payments</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Payments" name="payment" id="payment"
                                       value="<?php echo $record[0]['payment']; ?>">
                            </div>
                        </li>

                        <li>
                            <span class="half half-head">Payment Description</span>
                            <div class="half no-padding"><textarea
                                        name="details"><?php echo $record[0]['details']; ?></textarea></div>
                        </li>

                    </ul>
                </fieldset>

                <fieldset>
                    <legend>Mobile App Information</legend>
                    <ul class="fields border">
                        <li>
                            <span class="half half-head">Mobile App Name</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Mobile App Name"
                                       value="<?php echo $agent_settings['mobile_app_name']; ?>"
                                       id="mobile_app_name" name="mobile_app_name">
                            </div>
                        </li>

                        <li>
                            <span class="half half-head">Android Download Url</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Android Download Url"
                                       value="<?php echo $agent_settings['mobile_app_link_android']; ?>"
                                       id="mobile_app_link_android" name="mobile_app_link_android">
                            </div>
                        </li>

                        <li>
                            <span class="half half-head">IOS Download Url</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="IOS Download Url"
                                       value="<?php echo $agent_settings['mobile_app_link_ios']; ?>"
                                       id="mobile_app_link_ios" name="mobile_app_link_ios">
                            </div>
                        </li>

                    </ul>
                </fieldset>

                <fieldset>
                    <legend>Additional Information</legend>
                    <ul class="fields border">
                        <li>
                            <span class="half half-head">Facebook Url</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Facebook Url"
                                       value="<?php echo $record[0]['facebookUrl']; ?>"
                                       id="facebookUrl" name="facebookUrl">
                            </div>
                        </li>

                        <li>
                            <span class="half half-head">LinkedIn Url</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="LinkedIn Url"
                                       value="<?php echo $record[0]['linkdinUrl']; ?>"
                                       id="linkdinUrl" name="linkdinUrl">
                            </div>
                        </li>

                        <li>
                            <span class="half half-head">Twitter Url</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Twitter Url"
                                       value="<?php echo $record[0]['twitterUrl']; ?>"
                                       id="twitterUrl" name="twitterUrl">
                            </div>
                        </li>

                        <li>
                            <span class="half half-head">Google Url</span>
                            <div class="half no-padding">
                                <input type="text" placeholder="Google Url"
                                       value="<?php echo $record[0]['googlePlusUrl']; ?>"
                                       id="googlePlusUrl" name="googlePlusUrl"></div>
                        </li>

                        <li><span class="half half-head">Logo</span><span class="half no-padding"><input type="file"
                                                                                                         id="image"
                                                                                                         name="image"></span>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend>Uniagents Marketing Information</legend>
                    <ul class="fields border">
                        <li>
                            <span class="half half-head">Marketing Remarks</span>
                            <div class="half no-padding"><textarea
                                        name="remarks"><?php echo $record[0]['remarks']; ?></textarea>
                            </div>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>

                    <legend>Certified</legend>

                    <ul class="fields border">
                        <input type="radio" id="certified" name="certified" value="Y"
                               onClick="show_certificaiton_url('Y');" <?php if ($record['0']['certified'] == 'Y') { ?> checked<?php } ?>>
                        Yes

                        <input type="radio" id="certified" name="certified" value="N"
                               onClick="show_certificaiton_url('N');" <?php if ($record['0']['certified'] == 'N' || $record['0']['certified'] == '') { ?> checked<?php } ?>>
                        No

                    </ul>

                    <?php
                    $display_status = "display:none;";

                    if ($record['0']['certified'] == 'Y') {
                        $display_status = "display:block;";
                    }
                    ?>

                    <div id="certified_show_hide" style="<?php echo $display_status; ?>">
                        <ul class="fields border">
                            <li><span class="half half-head">Certificate Url</span>
                                <div class="half no-padding">
                                    <input type="text" placeholder="Pleae Enter Certification Url"
                                           value="<?php echo $record['0']['certified_url']; ?>" id="certified_url"
                                           name="certified_url"></div>
                            </li>
                        </ul>
                    </div>

                </fieldset>


                <button name="submit"><i class="fa fa-plus"></i> Update</button>

            </form>
        </div>
        <!-- add branch office form -->
    </div>
    <!-- right-panel -->
    <script>

        set_left_menu('submenu_view_agent', 'submenu_agent', 'button_member');

        function show_certificaiton_url(status) {

            if (status == 'Y') {
                document.getElementById('certified_show_hide').style.display = "block";
            }

            if (status == 'N') {
                document.getElementById('certified_show_hide').style.display = "none";
            }

        }
    </script>
<?php include('../includes/agent-footer.php'); ?>