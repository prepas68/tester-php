<?php
include "../includes/conn.php";
include "../includes/includes.php";

session_start();
$usersSQL="SELECT * FROM Users WHERE Username='" . $_POST['txtName'] . "'";
$myRsRes=mysql_query($usersSQL, $conn)
	or redirect_to("index.php");
$myRs=mysql_fetch_assoc($myRsRes)
	or redirect_to("index.php");
if (mysql_num_rows($myRsRes) != 0) {
	if ($myRs['Password']==md5($_POST['txtPassword'])) {
		if ($myRs['Admin']) {
			$_SESSION['loggedIn']="1";
			$_SESSION['loggedInName']=$myRs['Username'];
			$_SESSION['userID']=$myRs['ID'];
		}
	}
}
if ($_POST['referrer']!="") {
	redirect_to($_POST['referrer']);
} else {
	redirect_to("index.php");
}
?>