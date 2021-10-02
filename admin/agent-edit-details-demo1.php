<?php


require_once("../includes/config.php");
require_once("../includes/function.php");

require_once("../class/classDbDemo.php");
$db = new Database();

require_once("../class/agentClass.php");
$objAgent = new agent();

require_once("../class/admin.php");
$objAdmin = new admin();

require_once("../class/commonClass.php");
$objCommon = new common();

// To check admin is login or not
$objAdmin->check_admin_login();

if (isset($_GET['id'])) {
    $agent_id = decrypt($_GET['id']);


    $sql = "SELECT * FROM agent WHERE agentId='{$agent_id}' ";
    $db->query($sql);
    $record = $db->fetch();

    pr($agent_id);



}


if (isset($_POST['submit'])) {


    extract($_POST);


    if ($password == '') {
        $_SESSION['error']['msg'] = '<font color="red">Please enter the password!</font>';

    } else if ($_POST['agencyName'] == '') {
        $_SESSION['error']['msg'] = '<font color="red">Please enter Agency name!</font>';

    } else if ($_POST['country'] == '') {
        $_SESSION['error']['msg'] = '<font color="red">Please select country!</font>';


    }

    if (empty($_SESSION['error'])) {

        $old_username = trim($old_username);
        $username = trim($username);

        if ($old_username != $username) {

            $db->where(array('username' => $username))
                ->from('agent');

            $record_user = $db->fetch();

        } else {
            $record_user = array();

        }

        $random_no = mt_rand(100, 9999);


        if (isset($_FILES['image']['name']) AND $_FILES['image']['name'] != '') {

            $ext = end((explode(".", $_FILES['image']['name'])));

            $new_name = $random_no . "_agentid_" . $agent_id . "_date_" . date("d_m_Y") . "." . $ext;
            $upload_dir_main = "../agent_logo/" . $new_name;
            $upload_dir_thumbs = "../agent_logo/" . $new_name;
            chmod("../agent_logo", 0777);
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


        if (count($record_user) > 0) {

            $_SESSION['error']['msg'] = '<font color="red">This user is not available!</font>';

        }


        if ($agent_id > 0 && empty($_SESSION['error'])) {


            $data = array(
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
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'agentStatus' => $agentStatus,
                'payment' => $payment,
                'details' => $details,
                'agentLogo' => $logo,
                'LastUpdated' => 'now()',
                'remarks' => $remarks
            );

            $db->where(array('agentId' => $agent_id))
                ->update('agent', $data);


            $_SESSION['error']['msg'] = '<font color="green">Information is successfully updated!</font>';


            echo "<script>window.location.href='agent-edit-details-demo.php?id=" . urlencode($_GET['id']) . "';</script>";
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

            <?php if (isset($_SESSION['error']['msg'])) { ?>
                <div id="error_msg" style="display: block; height: 40px;"><font
                            color="red"><?php echo $_SESSION['error']['msg']; ?> </font></div>
                <?php unset($_SESSION['error']['msg']);
            } ?>

            <div class="form-sub-head">Edit Agent Details</div>
            <form action="" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Personal Information</legend>
                    <ul class="fields border">
                        <li><span class="half half-head">Agency Name </span><span class="half no-padding"><input
                                        type="text" required="" placeholder="Agency Name"
                                        value="<?php echo $record[0]['agencyName']; ?>" id="agencyName"
                                        name="agencyName"></span></li>
                        <li><span class="half half-head">Address</span><span class="half no-padding"><input type="text"
                                                                                                            placeholder="Address"
                                                                                                            value="<?php echo $record[0]['address']; ?>"
                                                                                                            id="address"
                                                                                                            name="address"></span>
                        </li>
                        <li><span class="half half-head">City</span><span class="half no-padding"><input type="text"
                                                                                                         placeholder="City"
                                                                                                         value="<?php echo $record[0]['city']; ?>"
                                                                                                         id="city"
                                                                                                         name="city"></span>
                        </li>
                        <li><span class="half half-head">State</span><span class="half no-padding"><input type="text"
                                                                                                          placeholder="State"
                                                                                                          value="<?php echo $record[0]['state']; ?>"
                                                                                                          id="state"
                                                                                                          name="state"></span>
                        </li>
                        <li><span class="half half-head">Country</span><span class="half no-padding">
		
		                        <?php echo $objAgent->country('country', $record[0]['country'], ' '); ?>
					
					        </span>
                        </li>
                        <li><span class="half half-head">Zipcode</span><span class="half no-padding"><input type="text"
                                                                                                            placeholder="Pincode"
                                                                                                            value="<?php echo $record[0]['pinCode']; ?>"
                                                                                                            id="pinCode"
                                                                                                            name="pinCode"></span>
                        </li>
                        <li><span class="half half-head">Phone Number</span><span class="half no-padding"><input
                                        type="text" placeholder="Phone Number"
                                        value="<?php echo $record[0]['phoneNumber']; ?>" id="phoneNumber"
                                        name="phoneNumber"></span></li>
                        <li><span class="half half-head">Email Address</span><span class="half no-padding"><i
                                        class="fa fa-envelope icon"></i><input type="email" placeholder="Email"
                                                                               value="<?php echo $record[0]['emailAddress']; ?>"
                                                                               id="emailAddress" name="emailAddress"
                                                                               style="padding:4px;"></span></li>
                        <li><span class="half half-head">Website</span><span class="half no-padding"><input type="text"
                                                                                                            placeholder="Website"
                                                                                                            value="<?php echo $record[0]['website']; ?>"
                                                                                                            id="website"
                                                                                                            name="website"></span>
                        </li>
                    </ul>
                </fieldset>
                <fieldset>
                    <legend>Point of contact</legend>
                    <ul class="fields border">
                        <li><span class="half half-head">Contact Person</span><span class="half no-padding"><input
                                        type="text" required="" placeholder="Contact Person"
                                        value="<?php echo $record[0]['contactPerson']; ?>" id="contactPerson"
                                        name="contactPerson"></span></li>
                        <li><span class="half half-head">Designation</span><span class="half no-padding"><input
                                        type="text" placeholder="Designation"
                                        value="<?php echo $record[0]['designation']; ?>" id="designation"
                                        name="designation"></span></li>
                        <li><span class="half half-head">Email</span><span class="half no-padding"><i
                                        class="fa fa-envelope icon"></i><input type="email"
                                                                               placeholder="Email of Contact Person"
                                                                               value="<?php echo $record[0]['contact_email']; ?>"
                                                                               id="contact_email" name="contact_email"
                                                                               style="padding:4px;"></span></li>
                        <li><span class="half half-head">Phone Number</span><span class="half no-padding"><input
                                        type="text" placeholder="Direct Phone Number"
                                        value="<?php echo $record[0]['directPhoneNumber']; ?>" id="directPhoneNumber"
                                        name="directPhoneNumber"></span></li>
                        <li><span class="half half-head">Mobile Number</span><span class="half no-padding"><input
                                        type="text" placeholder="Mobile Number"
                                        value="<?php echo $record[0]['mobile']; ?>" id="mobile" name="mobile"></span>
                        </li>
                        <li><span class="half half-head">Skype Id</span><span class="half no-padding"><input type="text"
                                                                                                             placeholder="Skype Id"
                                                                                                             value="<?php echo $record[0]['skypeId']; ?>"
                                                                                                             id="skypeId"
                                                                                                             name="skypeId"></span>
                        </li>
                    </ul>
                </fieldset>
                <fieldset>
                    <legend>Login Detail</legend>
                    <ul class="fields border">

                        <li><span class="half half-head">Username</span><span class="half no-padding">
                                <input type="text" required="" placeholder="Username"
                                       value="<?php echo $record[0]['username']; ?>"
                                       readonly="readonly" id="username" name="username">
		
		                        <input type="hidden" name="old_username" value="<?php echo $record[0]['username']; ?>">
		
		                </span>

                        </li>

                        <li><span class="half half-head">Password</span><span class="half no-padding"><input type="text"
                                                                                                             required=""
                                                                                                             value="<?php echo decrypt($record[0]['password']); ?>"
                                                                                                             id="password"
                                                                                                             name="password"
                                                                                                             readonly></span>
                        </li>

                        <li><span class="half half-head">Agent Status</span><span class="half no-padding">
		
                                <input type="radio" value="A"
                                       name="agentStatus" <?php if ($record[0]['agentStatus'] == 'A') echo 'checked'; ?>> Active &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" value="D"
                                       name="agentStatus" <?php if ($record[0]['agentStatus'] == 'D') echo 'checked'; ?>> Deactive
                                </span>
                        </li>
                        <li><span class="half half-head">Email Verified</span><span class="half ecno-padding">
		
                            <input type="radio" value="Y"
                                   name="email_verified" <?php if ($record[0]['email_verified'] == 'Y') echo 'checked'; ?>> Yes &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="N"
                                   name="email_verified" <?php if ($record[0]['email_verified'] == 'N') echo 'checked'; ?> > No

                            </span>
                        </li>
                        <li><span class="half half-head">Valid From</span><span class="half no-padding">
		

                                <input type="text" placeholder="Valid From" name="fromDate" id="fromDate"
                                       value="<?php echo $record[0]['fromDate']; ?>" autocomplete="off">

                            </span>
                        </li>

                        <script>
                            $("#fromDate").datepicker({dateFormat: "yy-mm-dd", changeYear: true});
                        </script>


                        <li>
                            <span class="half half-head">Valid To</span><span class="half no-padding">
                                <input type="text" placeholder="Valid To" name="toDate" id="toDate"
                                       value="<?php echo $record[0]['toDate']; ?>"
                                       autocomplete="off">
                                </span>
                        </li>
                        <script>
                            $("#toDate").datepicker({dateFormat: "yy-mm-dd", changeYear: true});
                        </script>


                        <li>

                            <span class="half half-head">File Allowed (In MB) </span><span class="half no-padding">
                                <input type="text" placeholder="File Allowed (Im MB)" name="file_allowed"
                                       id="file_allowed"
                                       value="<?php echo $record[0]['file_allowed']; ?>">
                                </span>

                        </li>

                        <li>
                            <span class="half half-head">Payments</span>
                            <span class="half no-padding">
                            <input type="text" placeholder="Payments" name="payment" id="payment"
                                   value="<?php echo $record[0]['payment']; ?>">
                            </span>

                        </li>

                        <li><span class="half half-head">Payment Description</span><span
                                    class="half no-padding"><textarea
                                        name="details"><?php echo $record[0]['details']; ?></textarea></span></li>
                    </ul>
                </fieldset>
                <fieldset>
                    <legend>Additional Information</legend>
                    <ul class="fields border">
                        <li><span class="half half-head">Facebook Url</span><span class="half no-padding"><input
                                        type="text" placeholder="Facebook Url"
                                        value="<?php echo $record[0]['facebookUrl']; ?>" id="facebookUrl"
                                        name="facebookUrl"></span></li>
                        <li><span class="half half-head">LinkedIn Url</span><span class="half no-padding"><input
                                        type="text" placeholder="LinkedIn Url"
                                        value="<?php echo $record[0]['linkdinUrl']; ?>" id="linkdinUrl"
                                        name="linkdinUrl"></span></li>
                        <li><span class="half half-head">Twitter Url</span><span class="half no-padding"><input
                                        type="text" placeholder="Twitter Url"
                                        value="<?php echo $record[0]['twitterUrl']; ?>" id="twitterUrl"
                                        name="twitterUrl"></span></li>
                        <li><span class="half half-head">Google Url</span><span class="half no-padding"><input
                                        type="text" placeholder="Google Url"
                                        value="<?php echo $record[0]['googlePlusUrl']; ?>" id="googlePlusUrl"
                                        name="googlePlusUrl"></span></li>
                        <li><span class="half half-head">Logo</span><span class="half no-padding"><input type="file"
                                                                                                         id="image"
                                                                                                         name="image"></span>
                        </li>
                    </ul>

                </fieldset>
                <fieldset>
                    <legend>Uniagents Marketing Information</legend>
                    <ul class="fields border">
                        <li><span class="half half-head">Marketing Remarks</span><span class="half no-padding"><textarea
                                        name="remarks"><?php echo $record[0]['remarks']; ?></textarea></span></li>
                    </ul>

                </fieldset>
                <fieldset>
                    <button name="submit"><i class="fa fa-plus"></i> Update</button>

            </form>
        </div>
        <!-- add branch office form -->
    </div>
    <!-- right-panel -->
    <script>
        set_left_menu('submenu_view_agent_demo', 'submenu_agent', 'button_member');
    </script>
<?php include('../includes/agent-footer.php'); ?>