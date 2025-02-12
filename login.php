<?php
include "./includes/conn.php";
include "./includes/includes.php";

session_start();
$usersSQL="SELECT * FROM Users WHERE Username='" . $_REQUEST['username'] . "'";
$myRsRes=mysql_query($usersSQL, $conn)
	or redirect_to("userLogin.php?id");
$myRs=mysql_fetch_assoc($myRsRes)
	or redirect_to("userLogin.php?iu");
if (mysql_num_rows($myRsRes) != 0) {
	if ($myRs['Password']==md5($_REQUEST['password'])) {
			$_SESSION['loggedInTest']="1";
			$_SESSION['username']=$myRs['Username'];
			$_SESSION['password']=$myRs['Password'];
			$_SESSION['firstname']=$myRs['FirstName'];
			$_SESSION['lastname']=$myRs['LastName'];
			$_SESSION['userID']=$myRs['ID'];
			$_SESSION['limited']=$myRs['Limited'];
			$_SESSION['limitedsubjects']=$myRs['LimitedSubjects'];
	} else {
		redirect_to("userLogin.php?ip");
		exit;
	}
}
redirect_to("userArea.php");
?>