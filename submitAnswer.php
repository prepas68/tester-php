<?php
$inTest=true;
include "./includes/timerhead.php";
include "./includes/conn.php";
include "./includes/includes.php";
include "./includes/nocache.php";
include "./includes/validation.php";

	  if ($_POST['SessionID'] == "") {
	  	redirect_to("index.php");
		exit;
	  }
	  if (IPSESSIONS) {
	  	$sessionsSQL="SELECT * FROM Sessions WHERE IP='" . $ip . "'";
	  } else {
	  	$sessionsSQL="SELECT * FROM Sessions WHERE ID='" . $sessID . "'";
	  }
	  $answersSQL="SELECT * FROM Answers WHERE SessionID=" . $_POST['SessionID'] . " AND QuesID=" . $_POST['QuesID'];
	  $myRsRes=mysql_query($answersSQL, $conn);
	  $myRs=mysql_fetch_assoc($myRsRes);
	  $update = "UPDATE Answers SET " .
	  "SessionID=" . $_POST['SessionID'] . ", " .
	  "TestID=" . $_POST['TestID'] . ", " .
	  "QuesID=" . $_POST['QuesID'] . ", " .
	  "A1=0, " .
	  "A2=0, " .
	  "A3=0, " .
	  "A4=0, " .
	  "A5=0, " .
	  "A6=0, " .
	  "AnswerText=\"\", " .
	  "SortOrder='" . intval($_POST['Order']) . "' " .
	  "WHERE SessionID=" . $_POST['SessionID'] .
	  " AND QuesID=" . $_POST['QuesID'];
	  $result=mysql_query($update, $conn)
	  	or die("Could not update - " . $update . " - " . mysql_error());
	  if (isset($_POST['AnswerText'])) {
		  if ($_POST['AnswerText'] != "") {
		  	$update="UPDATE Answers SET " .
			"AnswerText=\"" . $_POST['AnswerText'] . "\" " .
			"WHERE SessionID=" . $_POST['SessionID'] .
			" AND QuesID=" . $_POST['QuesID'];
			$result=mysql_query($update, $conn)
				or die("Could not update - " . $update . " - " . mysql_error());
		  }
	  }
	  if (isset($_POST['Answer'])) {
	  	$answer=$_POST['Answer'];
	  foreach($answer as $val) {
	  	$update = "UPDATE Answers SET " .
		$val . "=1 " .
		"WHERE SessionID=" . $_POST['SessionID'] .
		" AND QuesID=" . $_POST['QuesID'];
		$result=mysql_query($update, $conn)
			or die("Could not update - " . $update . " - " . mysql_error());
		// echo $update . "<br>";
	  }
	  }
	  $questionID = $_POST['QuesID'];
	  $questionsSQL = "SELECT * FROM Questions WHERE TestID='" . $_POST['TestID'] . "'";
	  $sessionsSQL = "SELECT * FROM Sessions WHERE ID='" . $_POST['SessionID'] . "'";
	  $myRsRes=mysql_query($questionsSQL, $conn);
	  $mySessionsRes=mysql_query($sessionsSQL, $conn);
	  $myRs=mysql_fetch_assoc($myRsRes);
	  $mySessions=mysql_fetch_assoc($mySessionsRes);
	  $questionOrder=$mySessions['QuestionOrder'];
	  $currentQuestion=$mySessions['currentQuestion'];
	  // echo $questionOrder;
	  $i=0;
	  $question=split(",",$questionOrder);
	  foreach($question as $q) {
	  	// echo $i . ":" . $q . "<br>";
		if ($q == $currentQuestion) {
			// echo "Match";
			//echo $_POST['Next'];
			//exit;
			if ($_POST['button'] == "Previous" or $_POST['Previous']==PREV_BUTTON) {
				$i--;
				$currentQuestion=$question[$i];
				$questionNumber=$mySessions['questionNumber'];
				// echo $currentQuestion;
				if ($currentQuestion == "") redirect_to("index.php");
				$update="UPDATE Sessions SET " .
				"currentQuestion=" . $currentQuestion . ", " .
				"questionNumber=" . ($questionNumber - 1) . " " .
				"WHERE ID=" . $_POST['SessionID'];
				// echo $update;
				$result=mysql_query($update, $conn)
					or die("Could not update - " . $update . " - " . mysql_error());
				redirect_to("test.php");
				exit;
			}
			if ($_POST['button'] == "Review" or $_POST['Review']==REVIEW_BUTTON) { 
				$update="UPDATE Sessions SET " .
				"review=1 " .
				"WHERE ID=" . $_POST['SessionID'];
				$result=mysql_query($update, $conn)
					or die("Could not update - " . $update . " - " . mysql_error());
				redirect_to("reviewTest.php");
				exit;
			}
			if ($_POST['button'] == "Next" or $_POST['Next']=="hidden") {
				$i++;
				$currentQuestion=$question[$i];
				$questionNumber=$mySessions['questionNumber'];
				// echo $currentQuestion;
				if ($currentQuestion == "") redirect_to("index.php");				
				$update="UPDATE Sessions SET " .
				"currentQuestion=" . $currentQuestion . ", " .
				"questionNumber=" . ($questionNumber + 1) . " " .
				"WHERE ID=" . $_POST['SessionID'];
				// echo $update;
				$result=mysql_query($update, $conn)
					or die("Could not update - " . $update . " - " . mysql_error());
				redirect_to("test.php");
				exit;
			}
		}
		$i++;
	  }
	  redirect_to("index.php");
?>