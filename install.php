<?php
ob_start();
session_start();

// Include necessary files
include "./includes/timerhead.php";
include "./includes/includes.php";
include "./includes/nocache.php";
include "config.php";

// Handle form submission
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

    // Create a new mysqli instance
    $conn = new mysqli($dbhost, $dbusername, $dbpassword);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // If the user wants to create the database
    if ($createdb === 'yes') {
        if ($cpanel === 'yes') {
            $dbusername = substr($dbusername, 0, 7);
            echo "Attempting to create database with cPanel...<br>";
            exec("/usr/bin/curl 'http://$cpusername:$cppassword@$cpdomain:2082/frontend/x/sql/adddb.html?db=$dbname'");
            exec("/usr/bin/curl 'http://$cpusername:$cppassword@$cpdomain:2082/frontend/x/sql/adduser.html?user=$dbusername&pass=$dbpassword'");
            $dbusername = $cpusername . "_" . $dbusername;
            $dbname = $cpusername . "_" . $dbname;
            exec("/usr/bin/curl 'http://$cpusername:$cppassword@$cpdomain:2082/frontend/x/sql/addusertodb.html?user=$dbusername&db=$dbname&ALL=ALL'");

            $conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);
            if ($conn->connect_error) {
                die("Failed to connect to database server. The error is: " . $conn->connect_error);
            }
        } else {
            echo "Connected to database server.<br>";
            $query = "CREATE DATABASE $dbname";
            if ($conn->query($query) === TRUE) {
                echo "Database creation successful.<br>";
            } else {
                die("Failed to create a new database. The error is: " . $conn->error);
            }

            $conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);
            if ($conn->connect_error) {
                die("Failed to select database. The error is: " . $conn->connect_error);
            }
        }
    } else {
        $conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);
        if ($conn->connect_error) {
            die("Failed to connect to database server. The error is: " . $conn->connect_error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebTester Online Testing</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <script src="includes/tableH.js"></script>
    <script src="editor/scripts/innovaeditor.js"></script>
    <script>
        function checkIt(string) {
            var detect = navigator.userAgent.toLowerCase();
            var place = detect.indexOf(string) + 1;
            return place;
        }
    </script>
</head>
<body>
<div align="center">
    <table width="100%" height="50" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td width="338" align="center" valign="top">
                <div align="left"><a href="./index.php"><img src="images/webtestertop.gif" width="337" height="75" border="0" alt="WebTester"></a></div>
            </td>
            <td align="center" valign="middle">
                <p class="style4">Install</p>
            </td>
        </tr>
    </table>
    <div class="hr"><hr /></div>
    <div align="left">
        <p><span class="style7">WebTester Install</span></p>
        <p>Thank you for choosing WebTester as your online test and quiz management software. This installation wizard will walk you through setup on your server.</p>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form name="form1" method="post" action="install2.php">
            <p>Database host (usually localhost):<br>
                <input name="dbhost" type="text" id="dbhost" value="localhost" required>
            </p>
            <p>Database name (such as webtester):<br>
                <input name="db" type="text" id="db" required>
            </p>
            <p>Database username:<br>
                <input name="dbusername" type="text" id="dbusername" required>
            </p>
            <p>Database password:<br>
                <input name="dbpassword" type="password" id="dbpassword">
            </p>
            <p>Would you like me to attempt to create the database for you?<br>
                <select name="createdb" id="createdb">
                    <option value="no" selected>No, I've already created it</option>
                    <option value="yes">Yes, try it</option>
                </select>
            </p>
            <p>Are you on a cPanel host?<br>
                <select name="cpanel" id="cpanel">
                    <option value="yes">Yes</option>
                    <option value="no" selected>No</option>
                </select>
            </p>
            <p>cPanel Username: (only if using cPanel) <br>
                <input name="cpusername" type="text" id="cpusername">
            </p>
            <p>cPanel Password:<br>
                <input name="cppassword" type="password" id="cppassword">
            </p>
            <p>Domain: (www.domain.com, no http://, and only if using cPanel)<br>
                <input name="cpdomain" type="text" id="cpdomain">
            </p>
            <p>
                <input type="submit" name="Submit" value="Go">
            </p>
        </form>
        <p>PHP Information (for technical support):</p>
        <p align="center">
            <iframe width="750" height="600" src="./phpinfo.php"></iframe>
        </p>
    </div>
    <div class="hr"><hr /></div>
    <p><span class="style1 style5">Copyright &copy; 2025 <a href="http://www.epplersoft.com">KAI</a></span><br>
        <font size="-2">Page created in <?php include "./includes/timerfoot.php"; ?> seconds.</font>
    </p>
</div>
</body>
</html>
<?php ob_end_flush(); ?>