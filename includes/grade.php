<?php
ob_start();
session_start();
include "./includes/timerhead.php";
include "./includes/conn.php"; // Ensure this uses mysqli and is updated for PHP 8
include "./includes/includes.php";
include "./includes/nocache.php";
include "./includes/validation.php";
require("./includes/html2text.php");

$finished = true;
$inTest = true;
$inReview = true;

?>
<!DOCTYPE html>
<html>
<head>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <title><?php echo TITLE; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        .style8 { color: #FF0000; font-weight: bold; }
        input.next { margin-top: 1.6em; float: right; }
        .t { background: url(images/dot.gif) 0 0 repeat-x; width: 20em; }
        .b { background: url(images/dot.gif) 0 100% repeat-x; }
        .l { background: url(images/dot.gif) 0 0 repeat-y; }
        .r { background: url(images/dot.gif) 100% 0 repeat-y; }
        .bl { background: url(images/bl.gif) 0 100% no-repeat; }
        .br { background: url(images/br.gif) 100% 100% no-repeat; }
        .tl { background: url(images/tl.gif) 0 0 no-repeat; }
        .tr { background: url(images/tr.gif) 100% 0 no-repeat; padding: 10px; }
        p { font-family: sans-serif; text-align: left; }
        @media print { body { display: none; } }
    </style>
</head>
<body>
<?php include "./includes/top.php"; ?>

<div align="center"> 
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr> 
            <td height="47" align="left" valign="middle">
                <img src="images/webtestertop.gif" width="<?php echo LOGOW; ?>" height="<?php echo LOGOH; ?>"><br>        
            </td>
            <td align="center" valign="middle">
                <!-- User interaction or session status -->
            </td>
        </tr>
        <tr>
            <td colspan="2" align="left" valign="top">
                <div class="hr"><hr /></div>
                <!-- Content Area Start -->
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $dbhost = $_POST['dbhost'] ?? '';
                    $dbname = $_POST['db'] ?? '';
                    $dbusername = $_POST['dbusername'] ?? '';
                    $dbpassword = $_POST['dbpassword'] ?? '';
                    $createdb = $_POST['createdb'] ?? 'no';
                    $cpanel = $_POST['cpanel'] ?? 'no';
                    $cpusername = $_POST['cpusername'] ?? '';
                    $cppassword = $_POST['cppassword'] ?? '';
                    $cpdomain = $_POST['cpdomain'] ?? '';

                    // Initialize connection
                    $conn = new mysqli($dbhost, $dbusername, $dbpassword);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    if ($createdb === 'yes') {
                        // Additional logic for creating a database or handling cPanel integration
                    }
                }
                ?>
                <!-- Content Area End -->
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" valign="top">
                <div align="center">
                    <div class="hr"><hr /></div>
                    <?php include "./includes/copyright.php"; ?>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
