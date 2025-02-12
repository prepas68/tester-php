<?php
ob_start();
session_start();

$finished = true;
$inTest = true;
$inReview = true;

include "./includes/timerhead.php";
include "./includes/conn.php"; // Uistite sa, že tento súbor inicializuje $conn ako mysqli objekt
include "./includes/includes.php";
include "./includes/nocache.php";
include "./includes/validation.php";
require("./includes/html2text.php");
?>
<html>
<head>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <title><?= TITLE ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <style type="text/css">
        .style8 {
            color: #FF0000;
            font-weight: bold;
        }
    </style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?= BGCOLOR ?>">
<?php
include "./includes/top.php";
?>
<div align="center">
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
            <td height="47" align="left" valign="middle">
                <img src="images/webtestertop.gif" width="<?= LOGOW ?>" height="<?= LOGOH ?>">
                <br>
            </td>
            <td align="center" valign="middle">
                <?php
                if (IPSESSIONS) {
                    $strSQL = "SELECT * FROM Sessions WHERE IP = ?";
                    $stmt = $conn->prepare($strSQL);
                    $stmt->bind_param("s", $ip);
                } else {
                    $strSQL = "SELECT * FROM Sessions WHERE ID = ?";
                    $stmt = $conn->prepare($strSQL);
                    $stmt->bind_param("i", $sessID);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($result->num_rows == 0) {
                    $myVar = true;
                } else {
                    $myVar = false;
                }

                if (!$myVar) {
                    echo "<div class='t'><div class='b'><div class='l'><div class='r'><div class='bl'><div class='br'><div class='tl'><div class='tr'><font face='Arial, Helvetica, sans-serif'>" . $row['FirstName'] . $myVar . " " . $row['LastName'] . "<br>";
                }

                if ($myVar != 1 && $row['TestName'] != "") {
                    echo $row['TestName'] . "<br>";
                    echo "<div id='countdowncontainer'></div>";
                    if ($row['AllowQuit'] && !$row['review'] && !$quit) {
                        echo "<div id='quit'><a href='./quitTest.php'>Quit</a></div>";
                    }
                    echo "</font></div></div></div></div></div></div></div></div>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="left" valign="top">
                <div class="hr">
                    <hr />
                </div>
                <?php
                if (IPSESSIONS) {
                    $checkSQL = "SELECT * FROM Sessions WHERE IP = ?";
                    $stmt = $conn->prepare($checkSQL);
                    $stmt->bind_param("s", $ip);
                } else {
                    $checkSQL = "SELECT * FROM Sessions WHERE ID = ?";
                    $stmt = $conn->prepare($checkSQL);
                    $stmt->bind_param("i", $sessID);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $record = $result->fetch_assoc();

                if (!isset($_REQUEST['TimeLimit']) && ($record['finished'] != 1) && (!isset($_REQUEST['Grade']))) {
                    redirect_to("index.php");
                    exit;
                }

                if (IPSESSIONS) {
                    $updateFin = "UPDATE Sessions SET finished=1 WHERE IP = ?";
                    $updateTest = "UPDATE Sessions SET takingTest=0 WHERE IP = ?";
                    $stmtFin = $conn->prepare($updateFin);
                    $stmtFin->bind_param("s", $ip);
                    $stmtTest = $conn->prepare($updateTest);
                    $stmtTest->bind_param("s", $ip);
                } else {
                    $updateFin = "UPDATE Sessions SET finished=1 WHERE ID = ?";
                    $updateTest = "UPDATE Sessions SET takingTest=0 WHERE ID = ?";
                    $stmtFin = $conn->prepare($updateFin);
                    $stmtFin->bind_param("i", $sessID);
                    $stmtTest = $conn->prepare($updateTest);
                    $stmtTest->bind_param("i", $sessID);
                }
                $stmtFin->execute();
                $stmtTest->execute();

                if (IPSESSIONS) {
                    $sessionsSQL = "SELECT * FROM Sessions WHERE IP = ?";
                    $stmtSessions = $conn->prepare($sessionsSQL);
                    $stmtSessions->bind_param("s", $ip);
                } else {
                    $sessionsSQL = "SELECT * FROM Sessions WHERE ID = ?";
                    $stmtSessions = $conn->prepare($sessionsSQL);
                    $stmtSessions->bind_param("i", $sessID);
                }
                $stmtSessions->execute();
                $mySessionsRes = $stmtSessions->get_result();
                $mySessions = $mySessionsRes->fetch_assoc();
                $resetID = $mySessions['ID'];

                $answersSQL = "SELECT * FROM Answers WHERE SessionID = ? ORDER BY ID";
                $stmtAnswers = $conn->prepare($answersSQL);
                $stmtAnswers->bind_param("i", $mySessions['ID']);
                $stmtAnswers->execute();
                $myAnswersRes = $stmtAnswers->get_result();
                $myAnswersRows = $myAnswersRes->num_rows;

                $rightAnswers = 0;
                $wrongAnswers = 0;
                $totalAnswers = 0;

                if ($myAnswersRows != 0) {
                    if (DISABLE_GRADE) {
                        echo "<!-- ";
                    }
                    ?>
                    <script language="javascript" type="text/javascript" src="includes/tableH.js"></script>
                    <table class="style1 style5" width="100%" border="0" cellspacing="2" cellpadding="0" onMouseOut="javascript:highlightTableRowVersionA(0);">
                        <tr bgcolor="#C8D8FF">
                            <td width="30">&nbsp;</td>
                            <td>Question</td>
                            <td>Your Answer</td>
                            <?php if (!DISABLE_ANSWERS) { ?>
                                <td>Correct Answer</td>
                            <?php } ?>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                        $emailOutput = "Answers:\r\n--------\r\n";
                        while ($myAnswers = $myAnswersRes->fetch_assoc()) {
                            $i++;
                            $questionsSQL = "SELECT * FROM Questions WHERE ID = ?";
                            $stmtQuestions = $conn->prepare($questionsSQL);
                            $stmtQuestions->bind_param("i", $myAnswers['QuesID']);
                            $stmtQuestions->execute();
                            $myRsRes = $stmtQuestions->get_result();
                            $myRs = $myRsRes->fetch_assoc();
                            $emailOutput .= $i . "\t" . strip_tags($myRs['QuestionText']) . "\r\n";
                            ?>
                            <tr class="d<?= $i & 1 ?>" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
                                <td align="left"><?= $i ?></td>
                                <td><?= strip_tags($myRs['QuestionText']) ?></td>
                                <?php
                                $expText = "";
                                if ($myRs['AnswerText'] != "") {
                                    if (strtoupper($myRs['AnswerText']) == strtoupper($myAnswers['AnswerText'])) {
                                        $rightAnswers += $myRs['Points'];
                                        if (!$mySessions['Stored']) {
                                            $correct = $myRs['Correct'] + 1;
                                            $updateSQL = "UPDATE Questions SET Correct = ? WHERE ID = ?";
                                            $stmtUpdate = $conn->prepare($updateSQL);
                                            $stmtUpdate->bind_param("ii", $correct, $myRs['ID']);
                                            $stmtUpdate->execute();

                                            $subjectSQL = "SELECT * FROM Subjects WHERE ID = ?";
                                            $stmtSubject = $conn->prepare($subjectSQL);
                                            $stmtSubject->bind_param("i", $myRs['Subject']);
                                            $stmtSubject->execute();
                                            $subjectRs = $stmtSubject->get_result()->fetch_assoc();
                                            $subCorrect = $subjectRs['Correct'] + 1;
                                            $updateSub = "UPDATE Subjects SET Correct = ? WHERE ID = ?";
                                            $stmtUpdateSub = $conn->prepare($updateSub);
                                            $stmtUpdateSub->bind_param("ii", $subCorrect, $myRs['Subject']);
                                            $stmtUpdateSub->execute();
                                        }
                                        $thePic = "check.gif";
                                        $correctText = "Yes";
                                    } else {
                                        $wrongAnswers += $myRs['Points'];
                                        if (!$mySessions['Stored']) {
                                            $incorrect = $myRs['Incorrect'] + 1;
                                            $updateSQL = "UPDATE Questions SET Incorrect = ? WHERE ID = ?";
                                            $stmtUpdate = $conn->prepare($updateSQL);
                                            $stmtUpdate->bind_param("ii", $incorrect, $myRs['ID']);
                                            $stmtUpdate->execute();

                                            $subjectSQL = "SELECT * FROM Subjects WHERE ID = ?";
                                            $stmtSubject = $conn->prepare($subjectSQL);
                                            $stmtSubject->bind_param("i", $myRs['Subject']);
                                            $stmtSubject->execute();
                                            $subjectRs = $stmtSubject->get_result()->fetch_assoc();
                                            $subIncorrect = $subjectRs['Incorrect'] + 1;
                                            $updateSub = "UPDATE Subjects SET Incorrect = ? WHERE ID = ?";
                                            $stmtUpdateSub = $conn->prepare($updateSub);
                                            $stmtUpdateSub->bind_param("ii", $subIncorrect, $myRs['Subject']);
                                            $stmtUpdateSub->execute();
                                        }
                                        $thePic = "cross.gif";
                                        $correctText = "No";
                                        $expText = $myRs['Explanation'];
                                    }
                                    if (EXPLAIN_ALL) {
                                        $expText = $myRs['Explanation'];
                                    }
                                    $emailOutput .= "\tAnswered:\t" . $myAnswers['AnswerText'] . "\r\n";
                                    $emailOutput .= "\tCorrect Answer:\t" . $myRs['AnswerText'] . "\r\n";
                                    $emailOutput .= "\tCorrect:\t" . $correctText . "\r\n";
                                    echo "<td>" . $myAnswers['AnswerText'] . "</td>";
                                    if (!DISABLE_ANSWERS) {
                                        echo "<td>" . $myRs['AnswerText'] . "</td>";
                                    }
                                    if ($expText != "") {
                                        echo "<td align='left'><img src='./images/" . $thePic . "'><br />" . $expText . "</td>";
                                    } else {
                                        echo "<td align='left'><img src='./images/" . $thePic . "'><br /></td>";
                                    }
                                } else {
                                    ?>
                                    <td>
                                        <?php
                                        $emailOutput .= "\t\tAnswered\r\n";
                                        for ($j = 1; $j <= 6; $j++) {
                                            $record = "A" . $j;
                                            $recordText = "Answer" . $j;
                                            $recordClicks = "A" . $j . "Clicks";
                                            if ($myRs[$recordText] != "") {
                                                $emailOutput .= "\t\t\t";
                                                ?>
                                                <input type="checkbox" disabled
                                                    <?php
                                                    $prefix = "( ) ";
                                                    if ($myAnswers[$record]) {
                                                        $prefix = "(x) ";
                                                        echo " checked";
                                                        if (!$mySessions['Stored']) {
                                                            $clicks = $myRs[$recordClicks] + 1;
                                                            $updateSQL = "UPDATE Questions SET " . $recordClicks . " = ? WHERE ID = ?";
                                                            $stmtUpdate = $conn->prepare($updateSQL);
                                                            $stmtUpdate->bind_param("ii", $clicks, $myRs['ID']);
                                                            $stmtUpdate->execute();
                                                        }
                                                    }
                                                    $emailOutput .= $prefix . $myRs[$recordText] . "\r\n";
                                                    ?>
                                                >
                                                <?= $myRs[$recordText] ?><br>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if (DISABLE_ANSWERS) {
                                        echo "<!---";
                                    }
                                    ?>
                                    <td>
                                        <?php
                                        $emailOutput .= "\t\tCorrect Answer:\r\n";
                                        for ($j = 1; $j <= 6; $j++) {
                                            $record = "A" . $j;
                                            $recordText = "Answer" . $j;
                                            if ($myRs[$recordText] != "") {
                                                $emailOutput .= "\t\t\t";
                                                ?>
                                                <input type="checkbox" disabled
                                                    <?php
                                                    $prefix = "( ) ";
                                                    if ($myRs[$record]) {
                                                        $prefix = "(x) ";
                                                        echo " checked";
                                                    }
                                                    $emailOutput .= $prefix . $myRs[$recordText] . "\r\n";
                                                    ?>
                                                >
                                                <?= $myRs[$recordText] ?><br>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if (DISABLE_ANSWERS) {
                                        echo "-->";
                                    }
                                    ?>
                                    <td align="left">
                                        <?php
                                        $a = 1;
                                        $thePic = "check.gif";
                                        $correctText = "Yes";
                                        for ($j = 1; $j <= 6; $j++) {
                                            $record = "A" . $j;
                                            $expText = "";
                                            if ($myRs[$record] != $myAnswers[$record]) {
                                                $a = 0;
                                                $thePic = "cross.gif";
                                                $correctText = "No";
                                                $expText = $myRs['Explanation'];
                                            }
                                        }
                                        $emailOutput .= "\t\tCorrect:\t" . $correctText . "\r\n";
                                        if (EXPLAIN_ALL) {
                                            $expText = $myRs['Explanation'];
                                        }
                                        ?>
                                        <img src="./images/<?= $thePic ?>"><br>
                                        <?= $expText ?>
                                    </td>
                                    <?php
                                    if ($a == 1) {
                                        $rightAnswers += $myRs['Points'];
                                        if (!$mySessions['Stored']) {
                                            $correct = $myRs['Correct'] + 1;
                                            $updateSQL = "UPDATE Questions SET Correct = ? WHERE ID = ?";
                                            $stmtUpdate = $conn->prepare($updateSQL);
                                            $stmtUpdate->bind_param("ii", $correct, $myRs['ID']);
                                            $stmtUpdate->execute();

                                            $subjectSQL = "SELECT * FROM Subjects WHERE ID = ?";
                                            $stmtSubject = $conn->prepare($subjectSQL);
                                            $stmtSubject->bind_param("i", $myRs['Subject']);
                                            $stmtSubject->execute();
                                            $subjectRs = $stmtSubject->get_result()->fetch_assoc();
                                            $subCorrect = $subjectRs['Correct'] + 1;
                                            $updateSub = "UPDATE Subjects SET Correct = ? WHERE ID = ?";
                                            $stmtUpdateSub = $conn->prepare($updateSub);
                                            $stmtUpdateSub->bind_param("ii", $subCorrect, $myRs['Subject']);
                                            $stmtUpdateSub->execute();
                                        }
                                    } else {
                                        $wrongAnswers += $myRs['Points'];
                                        if (!$mySessions['Stored']) {
                                            $incorrect = $myRs['Incorrect'] + 1;
                                            $updateSQL = "UPDATE Questions SET Incorrect = ? WHERE ID = ?";
                                            $stmtUpdate = $conn->prepare($updateSQL);
                                            $stmtUpdate->bind_param("ii", $incorrect, $myRs['ID']);
                                            $stmtUpdate->execute();

                                            $subjectSQL = "SELECT * FROM Subjects WHERE ID = ?";
                                            $stmtSubject = $conn->prepare($subjectSQL);
                                            $stmtSubject->bind_param("i", $myRs['Subject']);
                                            $stmtSubject->execute();
                                            $subjectRs = $stmtSubject->get_result()->fetch_assoc();
                                            $subIncorrect = $subjectRs['Incorrect'] + 1;
                                            $updateSub = "UPDATE Subjects SET Incorrect = ? WHERE ID = ?";
                                            $stmtUpdateSub = $conn->prepare($updateSub);
                                            $stmtUpdateSub->bind_param("ii", $subIncorrect, $myRs['Subject']);
                                            $stmtUpdateSub->execute();
                                        }
                                    }
                                }
                                $emailOutput .= "\r\n";
                                ?>
                            </tr>
                            <?php
                            $totalAnswers += $myRs['Points'];
                        }
                        $emailOutput .= "\r\n";
                        ?>
                    </table>
                   








				   <?php
                    if (DISABLE_GRADE) {
                        echo " -->";
                    }
                    $score = $rightAnswers / $totalAnswers * 100;
                    $score = intval($score);

                    $testsSQL = "SELECT * FROM Tests WHERE ID = ?";
                    $stmtTests = $conn->prepare($testsSQL);
                    $stmtTests->bind_param("i", $mySessions['TestID']);
                    $stmtTests->
					
					
						  <?php
	  if (DISABLE_GRADE) {
	  	echo (" -->");
	  }
	  $score=$rightAnswers / $totalAnswers;
	  $score=$score * 100;
	  $score=intval($score);
	  $testsSQL="SELECT * FROM Tests WHERE ID=" . $mySessions['TestID'];
	  $myRsRes=mysqli_query($testsSQL, $conn);
	  $myRs=mysqli_fetch_assoc($myRsRes);
	  if($myRs['AutoSession']) {
	  	$autoReset=1;
	  } else {
	  	$autoReset=0;
	  }
	  $testName=$myRs['TestName'];
	  if ($score >= $myRs['PassingScore']) {
	  	$pass = 1;
		$passText="vyhovel";
	  } else {
	  	$pass = 0;
		$passText="nevyhovel";
	  }
	  
	  $customMessageSQL = "SELECT * FROM CustomMessages WHERE ReportID='" . $myRs['ReportTemplate'] . "' AND $rightAnswers >= MinPoints AND $rightAnswers <= MaxPoints";
	  $customMessageResult = mysqli_query($customMessageSQL, $conn)
	  	or die("Invalid query: " . $customMessageSQL . mysql_error);
	  $customMessageRs = mysqli_fetch_assoc($customMessageResult);
	  $customMessage = $customMessageRs['Message'];
	  
	  $templateSQL="SELECT * FROM ReportTemplates WHERE ID='" . $myRs['ReportTemplate'] . "'";
	  $templateResult=mysqli_query($templateSQL, $conn)
	  	or die("Invalid query: " . $templateSQL . mysqli_error);
	  $templateRs=mysqli_fetch_assoc($templateResult);
	  $template=$templateRs['Text'];
	  $search=array("%GRADE_TABLE%", "%FIRST_NAME%", "%LAST_NAME%", "%TEST_NAME%", "%TEST_DATE%", "%TEST_TIME%", "%NUMBER_CORRECT%", "%NUMBER_POSSIBLE%", "%PERCENTAGE%", "%PASSFAIL%", "%NOTES%", "%TESTID%", "%EMAIL%", "%CUSTOMMESSAGE%", "%STREET%", "%STREET2%", "%CITY%", "%STATE%", "%ZIP%");
	  $replace=array("<pre>" . $emailOutput . "</pre>", $mySessions['FirstName'], $mySessions['LastName'], $myRs['TestName'], date("n/j/Y",time()), date("g:i:s A",time()), $rightAnswers, $totalAnswers, $score . "%", $passText, $mySessions['Notes'], $mySessions['TestID'], $mySessions['Email'], $customMessage, $_SESSION['street'], $_SESSION['street2'], $_SESSION['city'], $_SESSION['state'], $_SESSION['zip']);
	  $report=str_replace($search, $replace, $template);
	  echo $report;
	  }
	  ?>
	<?php
if (!$mySessions['Stored']) {
	  //$endTime=date("n/j/Y g:i:s A");
	  $endTime=time();
	  $totalTimeSS=datediff("s", $mySessions['StartTime'], $endTime, true);
	  $totalTimeSM=$totalTimeSS / 60;
	  $totalTimeS=$totalTimeSS - (intval($totalTimeSM)*60);
	  $totalTimeM=datediff("n", $mySessions['StartTime'], $endTime, true);
	  $totalTime=$totalTimeM . "m" . $totalTimeS . "s";

	  $resultsSQL="INSERT INTO Results
	  		(TestID,
			TestName,
			NumCorrect,
			NumPossible,
			Score,
			Pass,
			IPAddress,
			StartTime,
			EndTime,
			TotalTime,
			LastName,
			FirstName,
			Notes)
			VALUES
			('" . $mySessions['TestID'] . "',
			'" . addslashes($testName) . "',
			'" . $rightAnswers . "',
			'" . $totalAnswers . "',
			'" . $score . "',
			'" . $pass . "',
			'" . $ip . "',
			'" . $mySessions['StartTime'] . "',
			'" . $endTime . "',
			'" . $totalTime . "',
			'" . addslashes($mySessions['LastName']) . "',
			'" . addslashes($mySessions['FirstName']) . "',
			'" . addslashes($mySessions['Notes']) . "')";
	
	  $result=mysqli_query($resultsSQL, $conn)
	  	or die("Invalid Query: " . $resultsSQL . " - " . mysqli_error());
	  
	  if (IPSESSIONS) {
	  	$sessionsSQL="UPDATE Sessions SET stored=1 WHERE IP='" . $ip . "'";
	  } else {
	  	$sessionsSQL="UPDATE Sessions SET stored=1 WHERE ID='" . $sessID . "'";
      }
	  $result=mysqli_query($sessionsSQL, $conn)
	  	or die("Invalid Query: " . $sessionsSQL . " - " . mysqli_error());
		
	  $emailSQL="SELECT * FROM EmailTemplates WHERE ID='" . $myRs['EmailTemplate'] . "'";
	  $emailResult=mysqli_query($emailSQL, $conn)
	  	or die("Invalid Query: " . $emailSQL . " - " . mysqli_error());
	  $emailRs=mysqli_fetch_assoc($emailResult);
	  require("./includes/class.phpmailer.php");
	  
	  $usersSQL="SELECT * FROM Users WHERE Username='" . $myRs['Creator'] . "'";
	  $usersResult=mysqli_query($usersSQL, $conn)
	  	or die("Invalid Query: " . $usersSQL . " - " . mysqli_error());
	  $usersRs=mysqli_fetch_assoc($usersResult);
	  
	  $subject=$emailRs['Subject'];
	  $body=$emailRs['Text'];
	  $search=array("%GRADE_TABLE%", "%FIRST_NAME%", "%LAST_NAME%", "%TEST_NAME%", "%TEST_DATE%", "%TEST_TIME%", "%NUMBER_CORRECT%", "%NUMBER_POSSIBLE%", "%PERCENTAGE%", "%PASSFAIL%", "%NOTES%", "%TESTID%", "%EMAIL%", "%CUSTOMMESSAGE%", "%STREET%", "%STREET2%", "%CITY%", "%STATE%", "%ZIP%");
	  $replace=array($emailOutput, $mySessions['FirstName'], $mySessions['LastName'], $myRs['TestName'], date("n/j/Y",time()), date("g:i:s A",time()), $rightAnswers, $totalAnswers, $score . "%", $passText, $mySessions['Notes'], $mySessions['TestID'], $mySessions['Email'], $customMessage, $_SESSION['street'], $_SESSION['street2'], $_SESSION['city'], $_SESSION['state'], $_SESSION['zip']);
	  $bodySend=str_replace($search, $replace, $body);
	  $subjectSend=str_replace($search, $replace, $subject);
	  
	  $mail = new PHPMailer();
	  
	  $mail->From = $emailRs['FromEmail'];
	  $mail->FromName = $emailRs['FromEmail'];
	  if($myRs['EmailInstructor']) {
	  	$mail->AddBCC($usersRs['Email'], $usersRs['FirstName'] . " " . $usersRs['LastName']);
		$send=true;
	  }
	  if($myRs['EmailUsers']) {
	  	$mail->AddAddress($mySessions['Email'], $mySessions['FirstName'] . " " . $mySessions['LastName']);
		$send=true;
	  }
	  if($myRs['AltEmail']) {
	  	$mail->AddBCC($mySessions['AltEmail']);
		$send=true;
	  }
	  $mail->Subject = $subjectSend;
	  $mail->Body = $bodySend;
	  $mail->AltBody = html2text($bodySend);
	  $mail->IsHTML(true);
	  
	  if($send) {
	  	if(!$mail->Send())
	  	{
	  			echo "Email Send Failed <p>";
				echo "Mailer Erorr: " . $mail->ErrorInfo;
	  	}
	  }
				  
		
} else {

}
	  ?>
	  <BR>
	  <form action="clearResults.php" method="post">
	  <?php
	  	if(!$autoReset) {
	  ?>
          <p>Username:<br>
            <input name="userName" type="text" id="userName">
            <br>
            Password:<br>
            <input name="password" type="password" id="password">
          </p>
	  <?php
	  	} else {
	  ?>
	  	<input type="hidden" name="autoReset" value="true">
		<?php
		}
		if(RETRY) {
		?>
          <p>
            <input name="Retry" type="submit" value="<?=RETRY_BUTTON?>">
            <input name="TestID" type="hidden" value="<?=$mySessions['TestID']?>">
          </p>
        <?php
		}
		if(NODONE) {
			echo ("<!-- ");
		}
		?>
          <p>
            <input name="Clear" type="submit" value="<?=DONE_BUTTON?>">
          </p>
        <?
        if(NODONE) {
        	echo (" -->");
        }
        ?>
        </form>
	  <!-- InstanceEndEditable -->	  </td>
    </tr>
    <tr>
      <td colspan="2" align="center" valign="top">
        <div align="center"> 
		<div class="hr"><hr /></div>
          <?php include "./includes/copyright.php" ?></div></td>
    </tr>
  </table>
</div>
</body>
<!-- InstanceEnd --></html>
<?php ob_end_flush() ?>
					
					
					
					
					