<?php
ob_start();
session_start();
?>
<?php
$finished=true;
$inTest=true;
$inReview=true;
include "./includes/timerhead.php";
include "./includes/conn.php";
include "./includes/includes.php";
include "./includes/nocache.php";
include "./includes/validation.php";
require("./includes/html2text.php");





?>
<html><!-- InstanceBegin template="/Templates/Test%20Layout.dwt.php" codeOutsideHTMLIsLocked="false" -->


<!-- InstanceEnd --></html>
<?php ob_end_flush() ?>