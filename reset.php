<?php
// This file resets a session and then redirects to the URL specified by the
// link parameter
include "./includes/timerhead.php";
include "./includes/conn.php";
include "./includes/includes.php";
include "./includes/nocache.php";


		if (IPSESSIONS) {
			$sessionsSQL="SELECT * FROM Sessions WHERE IP='" . $ip . "'";
		} else {
			$sessionsSQL="SELECT * FROM Sessions WHERE ID='" . $sessID . "'";
		}
		$mySessionsRes=mysql_query($sessionsSQL, $conn);
		$mySessions=mysql_fetch_assoc($mySessionsRes);
		$answersSQL="DELETE FROM Answers WHERE SessionID='" . $mySessions['ID'] . "'";
		$result=mysql_query($answersSQL)
			or die("Invalid Query: " . $answersSQL . " - " . mysql_error());
		if (IPSESSIONS) {
			$sessionsSQL="DELETE FROM Sessions WHERE IP='" . $ip . "'";
		} else {
			$sessionsSQL="DELETE FROM Sessions WHERE ID='" . $sessID . "'";
		}
		$result=mysql_query($sessionsSQL)
			or die("Invalid Query: " . $sessionsSQL . " - " . mysql_error());
		session_destroy();
		if(isset($_REQUEST['link'])) {
			header("Location: " . $_REQUEST['link']);
			exit;
		} else {
			redirect_to("index.php");
		}
	  	redirect_to("index.php");
?>