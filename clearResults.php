<?php
include "./includes/timerhead.php";
include "./includes/conn.php";
include "./includes/includes.php";
include "./includes/nocache.php";
// include "./includes/validation.php";



		if (IPSESSIONS) {
			$sessionsSQL="SELECT * FROM Sessions WHERE IP='" . $ip . "'";
		} else {
			$sessionsSQL="SELECT * FROM Sessions WHERE ID='" . $sessID . "'";
		}
		$mySessionsRes=mysql_query($sessionsSQL, $conn);
		$mySessions=mysql_fetch_assoc($mySessionsRes);
		if($_POST['autoReset']=="true") {
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
			if($_POST['Retry']==RETRY_BUTTON) {
				redirect_to("go.php?testID=" . $_POST['TestID']);
				exit;
			}
			if(CLOSE_WINDOW) {
				redirect_to("closewindow.php");
				exit;
			} else {
				redirect_to("index.php");
				exit;
			}
		}
		$usersSQL="SELECT * FROM Users WHERE Username='" . $_POST['userName'] . "'";
		$myRsRes=mysql_query($usersSQL, $conn)
			or die(mysql_error());
		$myRs=mysql_fetch_assoc($myRsRes);
		if (mysql_num_rows($myRsRes) != 0) {
			if ($myRs['Password']==md5($_POST['password'])) {
			  if ($myRs['Admin']) {
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
			  }
			  redirect_to("index.php");
			  exit;
			}
		}
	  	redirect_to("index.php");
?>