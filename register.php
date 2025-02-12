<?php
include "./includes/conn.php";
include "./includes/includes.php";

if ($_REQUEST['password'] != $_REQUEST['confirm']) {
	die("Password doesn't match");
}

if ($_REQUEST['username'] == "") {
	die("No username");
}

$registerCheckSQL="SELECT * FROM Users WHERE Username='" . $_REQUEST['username'] . "'";

$checkResult=mysql_query($registerCheckSQL, $conn);

if (mysql_num_rows($checkResult) > 0) {
	die("User already exists...");
}

$registerSQL="INSERT INTO Users
			(Username,
			Password,
			FirstName,
			LastName,
			Email,
			street,
			street2,
			city,
			state,
			zip)
			VALUES
			('" . mysql_escape_string($_REQUEST['username']) . "',
			'" . md5(mysql_escape_string($_REQUEST['password'])) . "',
			'" . mysql_escape_string($_REQUEST['firstname']) . "',
			'" . mysql_escape_string($_REQUEST['lastname']) . "',
			'" . mysql_escape_string($_REQUEST['email']) . "',
			'" . mysql_escape_string($_REQUEST['street']) . "',
			'" . mysql_escape_string($_REQUEST['street2']) . "',
			'" . mysql_escape_string($_REQUEST['city']) . "',
			'" . mysql_escape_string($_REQUEST['state']) . "',
			'" . mysql_escape_string($_REQUEST['zip']) . "')";
						
$result=mysql_query($registerSQL, $conn)
	or die("Invalid Query: " . $registerSQL . " - " . mysql_error());
	
redirect_to("userLogin.php");



?>