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

// To check admin is login or not
//$objAdmin->check_admin_login();

$total_consumed_space = $objAgent->check_consumed_data($_SESSION['login']['agent_id']);



if (isset($_GET['country_id']) && $_GET['country_id'] != '') {

    $country_id = $_GET['country_id'];

    $sql = "select * from country where country_id='$country_id'";
    $db->query($sql);
    $country_details = $db->fetch_first();

    $short_name_turkish = $country_details['short_name_turkish'];
}

if (isset($_POST['submit'])) {



    extract($_POST);

    $country_id = $country_details['country_id'];

    if ($short_name_turkish != '' && $country_id > 0) {


        $data = array('short_name_turkish' => $short_name_turkish);


        $data = $objAgent->sanitize_data($data);

        $db->where(array("country_id" => $country_id))
            ->update('country', $data);


        $_SESSION[error]['msg'] = '<font color="green">Turkish Country Name Save Successfully</font>';


        echo "<script>window.location.href='turkish_country_name_change.php?country_id=" . $country_id . "';</script>";
        exit;

    }
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="../js/jquery-ui.js"></script>

<?php include('../includes/admin-header.php'); ?>
<?php //include('../includes/banner.php'); ?>
<?php //include('../includes/admin-left-panel.php'); ?>
<script type="application/javascript">
    function search_inst() {
        //alert("dsfsdf");
        //var country = $("#country").val();
        document.getElementById("myForm").submit();
    }
</script>
<style type="text/css">
    .form-container {
        border: 1px solid #ccc;
    }

    .form-container .sub-head {
        padding: 10px;
        background: #333333;
        box-shadow: 0 0px 20px inset #333;
        -moz-box-shadow: 0 0px 20px inset #333;
        -webkit-box-shadow: 0 0px 20px inset #333;
        -o-box-shadow: 0 0px 20px inset #333;
        border-bottom: 2px solid #ccc;
        font-size: 14px;
        color: #fff;
    }

    .col4 {
        width: 32.333333%;
        float: left;
        padding: 5px;
    }

    .form-container .form {
        padding: 10px;
        margin-bottom:;
    }

    .form-container .form label {
        padding: 10px;
        background: #f1f1f1;
        display: inline-block;
        color: #666;
        float: left;
        min-width: 100%;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    ul {
        display: block;
        list-style-type: disc;
        margin-block-start: 1em;
        margin-block-end: 1em;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
        padding-inline-start: 40px;
    }

    .select {
        text-rendering: auto;
        color: initial;
        letter-spacing: normal;
        word-spacing: normal;
        text-transform: none;
        text-indent: 0px;
        text-shadow: none;
        display: inline-block;
        text-align: start;
        margin: 0em;
        font: 400 13.3333px Arial;
    }

    /* Search Container */
    .search-container {
        background: #f8f8f8;
        border: 1px solid #e7e7e7;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -o-border-radius: 5px;
        padding: 10px;
        color: #666;
        margin-bottom: 15px;
    }

    .search-container ul {
        list-style: none;
        display: block;
        margin: 0;
        padding: 0;
    }

    .search-container ul li {
        display: inline-block;
        float: left;
        padding: 8px 3px;
        font-size: 95%;
        vertical-align: text-top;
    }

    .search-container ul li.head {
        font-size: 115% !important;
        padding: 6px 5px;
        color: #999;
    }

    .search-container ul li.search-box {
        padding: 3px 5px;
        color: #999;
    }

    .search-container ul li input[type="text"],
    .search-container ul li input[type="number"],
    .search-container ul li input[type="email"],
    .search-container ul li input[type="date"],
    .search-container ul li input[type="url"] {
        padding: 5px;
        border: 1px solid #ccc;
        color: #666;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        -o-border-radius: 3px;
        width: 90px;
    }

    .search-container ul li.search-button {
        float: right;
        padding: 2px 0;
    }

    .search-container ul li input[type="button"],
    .search-container ul li input[type="submit"],
    .search-container ul li input[type="reset"] {
        box-shadow: none;
        background: transparent;
        font-size: 14px;
        padding: 5px !important;
        border: 1px solid #ccc !important;
        color: #666 !important;
        border-radius: 3px !important;
        -moz-border-radius: 3px !important;
        -webkit-border-radius: 3px !important;
        -o-border-radius: 3px !important;
        cursor: pointer !important;
    }

    .search-container ul li select {
        padding: 5px;
        border: 1px solid #ccc;
        color: #666;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        -o-border-radius: 3px;
        min-width: 85px;
    }

    .search-container ul li select option {
        padding: 0px;
        font-size: 85%;
    }

    .reset {
        padding: 5px !important;
        border: 1px solid #ccc !important;
        color: #666 !important;
        border-radius: 3px !important;
        -moz-border-radius: 3px !important;
        -webkit-border-radius: 3px !important;
        -o-border-radius: 3px !important;
        cursor: pointer !important;
        background: #fff !important;
        box-shadow: none !important;
        font-size: 14px !important;
    }

    /* Search Container */

    .form-container fieldset ul.fields li {
        width: 32%;
    }

    .form-container fieldset ul.fields li .select {
        display: block;
    }

    .rowflex {
        display: flex;
        flex-wrap: wrap;
    }

    .stretch-card {
        display: -webkit-flex;
        display: flex;
        -webkit-align-items: stretch;
        align-items: stretch;
        -webkit-justify-content: stretch;
        justify-content: stretch;
    }
</style>
<!-- right-panel -->
<div class="right-panel column">

    <?php include('../includes/admin_login_section.php'); ?>

    <!-- add branch office form -->

    <div class="form-container">

        <?php if (isset($_SESSION[error]['msg'])) { ?>
            <div id="error_msg"><?php echo $_SESSION[error]['msg']; ?></div>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
            <?php unset($_SESSION[error]['msg']);
        } ?>

        <div class="form-sub-head">
            <div class="pull-left">Add Turkish Country Name</div>
            <div class="clearfix"></div>
        </div>

        <div class="clearfix"></div>
        <fieldset>
            <form action="" method="post" name="myForm" id="myForm">
                <ul class="fields">
                    <li>
                        <span class="lable">Select Country <span class="required" style="color:#F00;">*</span></span>
                        <label class="select"><?php echo $objAgent->country('country_id', $country_id, ' onchange="search_inst()" '); ?></label>
                    </li>

                    <li>
                        <span class="lable"> Enter Country Name In Turkish <span class="required"
                                                                                 style="color:#F00;">*</span></span>
                        <input name="short_name_turkish" id="short_name_turkish"
                               value="<?php echo $short_name_turkish; ?>" type="text"
                               placeholder="Enter Country Name In Turkish"/>
                    </li>

                    <li>
                        <button name="submit">SUBMIT</button>
                    </li>
                </ul>

            </form>
        </fieldset>
    </div>

    <div class="form-container">
        <!-- add panel -->
        <?php
        if (isset($_REQUEST['reset'])) {
            unset($_SESSION);
            unset($_SESSION['POST']['keyword']);
        }


        ?>


        <div class="add-panel-container">
            <?php if (@$_REQUEST['msg'] != '') { ?>
                <div align="center" style="color:#F00; font-size:16px; font-weight:bold;">
                    <?php
                    echo $_REQUEST['msg'];
                    //========= Value or Record Blank =======//
                    $region_countries = "";
                    //======= End  ====//
                    ?>
                </div>
                <div class="clear">&nbsp;</div>
            <?php } ?>
        </div>
        <!-- search container -->
        <div class="clear"></div>

        <div class="spacer"></div>
        <div class="details-panel">


            <?php

            $sql = "select * from country ";
            $db->query($sql);
            $region_countries = $db->fetch();

            //echo $db->last_query();

            //$region_countries = $db->fetch();
            ?>


            <div>
                <div class="form-container" style="padding:0px;">
                    <div class="sub-head"></div>
                    <div class="form rowflex">
                        <?php foreach ($region_countries as $country) {


                            ?>
                            <div class="col4 stretch-card">
                                <label>
                                    <input type="text" name="dfdfdf" id="dddddd"
                                           value="<?php echo $country['short_name']; ?>"
                                           style="width:230px; border:0px; background-color:#f1f1f1;" readonly>
                                    <br/> Country Name In Turkish :

                                    <?php if (!empty($country['short_name_turkish'])) {
                                        echo $country['short_name_turkish'];
                                    } else { ?><span style="background-color:#F00;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><?php } ?>

                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="country_id"
                                               value="<?php echo $country['country_id']; ?>">
                                        <input type="hidden" name="remove_country" value="1">
                                        <span class="button-container">
                                    <a href="turkish_country_name_change.php?country_id=<?php echo $country['country_id']; ?>"><i
                                                class="fa fa-edit"></i></a>
                                </span>
                                    </form>
                                </label>
                                <div class="clearfix"></div>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php if (empty($region_countries)) { ?>
                            <font color="red" style="margin: 5px 10px 0px; font-weight:bold;">No Records</font>
                        <?php } ?>
                    </div>
                </div>

                <div class="clear"></div>

            </div>
        </div>
    </div>

    <!-- add branch office form -->

</div>

<!-- right-panel -->


<?php //include('../includes/agent-footer.php'); ?>
<?php include_once('toolbox.php'); ?>
<?php include_once('common_file.php'); ?>

<script type="application/javascript">
    function orderbydate_short() {
        $("form:second").submit();
    }
</script>

</body>
</html>