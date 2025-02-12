<?php
	  include "./includes/conn.php";
	  include "./includes/nocache.php";
	  include "./includes/includes.php";
	  ob_start();
	  session_start();
	  if (isset($_SESSION['TestID'])) {
	  	$_POST['TestID']=$_SESSION['TestID'];
		$_POST['LastName']="";
		$_POST['FirstName']="";
		$_POST['username']="";
	  }
	  if (!isset($_REQUEST['TestID'])) {
	  	redirect_to("index.php");
		exit;
	  }
	  if (!isset($_REQUEST['username'])) {
	  	if ($_REQUEST['LastName']==NULL) {
	  		redirect_to("userInfo.php?testID=" . $_REQUEST['TestID'] . "&noname=1");
			exit;
	  	}
	  	if ($_REQUEST['FirstName']==NULL) {
	  		redirect_to("userInfo.php?testID=" . $_REQUEST['TestID'] . "&noname=1");
			exit;
	  	}
		if (isset($_REQUEST['Email'])) {
			if ($_REQUEST['Email']==NULL) {
				redirect_to("userInfo.php?testID=" . $_REQUEST['TestID'] . "&noemail=1");
				exit;
			}
		}
		if (VERIFY_EMAIL) {
			if ($_REQUEST['Email']!=$_REQUEST['VerifyEmail']) {
				redirect_to("userInfo.php?testID=". $_REQUEST['TestID'] . "&emailerror=1");
				exit;
			}
			if ($_REQUEST['AltEmail']!=$_REQUEST['VerifyAltEmail']) {
				redirect_to("userInfo.php?testID=". $_REQUEST['TestID'] . "&emailerror=1");
				exit;
			}
		}
	  } else {
	  	$prefsSQL="SELECT * FROM Preferences";
		$prefsResult=mysql_query($prefsSQL, $conn);
		$prefs=mysql_fetch_assoc($prefsResult);
		if (LDAP_ENABLED) {
			// $ldapBase=$prefs['ldapBase'];
			// $ldapServer=$prefs['ldapServer'];
			// $ldapOU=$prefs['ldapOU'];
			$ldapConn = ldap_connect(LDAP_SERVER)
				or die("Could not connect to LDAP server. Please check LDAP Server: " . LDAP_SERVER);
			if ($ldapConn) {
				ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
				$connectString=LDAP_PREFIX . LDAP_USERNAME . LDAP_SUFFIX;
				echo $connectString;
				$ldapBind = ldap_bind($ldapConn, $connectString, LDAP_PASSWORD);
				if ($ldapBind) {
					$ldapSearch = ldap_search($ldapConn, $ldapBase, "(" . LDAP_SEARCH_ATTRIB . "=" . $_REQUEST['username'] . ")");
					$ldapResults = ldap_get_entries($ldapConn, $ldapSearch);
					$ldapUserDN  = $ldapResults[0]['dn'];
					$_REQUEST['LastName']=$ldapResults[0][LDAP_SURNAME_ATTRIB][0];
					$_REQUEST['FirstName']=$ldapResults[0][LDAP_GIVENNAME_ATTRIB][0];
					$_REQUEST['Email']=$ldapResults[0][LDAP_EMAIL_ATTRIB][0];
					echo "<pre>";
                             		print_r($ldapResults);
                        		ldap_unbind($ldapConn);
					if (!isset($ldapUserDN)) {
						redirect_to("./invalidLogin.php?ldapNoValidUser");
						exit;
						ob_end_flush();
					}
					
               		              	$ldapConn = ldap_connect(LDAP_SERVER);
					ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
					$ldapBind = ldap_bind($ldapConn, $ldapUserDN, $_REQUEST['password']);
					echo $ldapUserDN;
					// echo $_REQUEST['password'];
					echo "</pre>";
					if (!$ldapBind) {
						redirect_to("./invalidLogin.php?ldapInvBindStep2");
						exit;
						ob_end_flush();
					}
					ldap_unbind($ldapConn);
					echo ("<pre>Authenticated user?\n\n" . $_REQUEST['LastName'] . "\n" . $_REQUEST['FirstName'] . "\n" . $_REQUEST['Email'] . "</pre>");
				} else {
					redirect_to("./invalidLogin.php?ldapInvBindStep1");
					exit;
					ob_end_flush();
				}
			}
		} elseif (PHPBB) {
			define('IN_PHPBB', true); 
			$phpbb_root_path = PHPBB_PATH;
			include($phpbb_root_path . 'extension.inc');
			include($phpbb_root_path . 'common.php');
			include($phpbb_root_path . 'config.php'); 
			$userdata = session_pagestart($user_ip, PAGE_INDEX);
			init_userprefs($userdata);
			if($userdata['session_logged_in']) { // if logged in
			// foreach ($userdata as $key => $value) {
			//	echo $key . " - " . $value . "<br>";
			// }
				$_REQUEST['FirstName']=$userdata['username'];
				$_REQUEST['LastName']="";
				$_REQUEST['Email']=$userdata['user_email'];
				$_REQUEST['Notes']="phpBB Login";
			} else {
				redirect_to("index.php");
				exit;
			}
		} else {
	  		$usersSQL="SELECT * FROM Users WHERE Username='" . $_REQUEST['username'] . "'";
			$usersResult=mysql_query($usersSQL, $conn);
			if(mysql_num_rows($usersResult)<1) {
				redirect_to("./invalidLogin.php");
				exit;
			}
			$usersRs=mysql_fetch_assoc($usersResult);
			if(md5($_REQUEST['password'])==$usersRs['Password']) {
				$_REQUEST['LastName']=$usersRs['LastName'];
				$_REQUEST['FirstName']=$usersRs['FirstName'];
				$_REQUEST['Email']=$usersRs['Email'];
				$_SESSION['street']=$usersRs['street'];
				$_SESSION['street2']=$usersRs['street2'];
				$_SESSION['city']=$usersRs['city'];
				$_SESSION['state']=$usersRs['state'];
				$_SESSION['zip']=$usersRs['zip'];
			} elseif ($_REQUEST['passwordmd5']==$usersRs['Password']) {
				$_REQUEST['LastName']=$usersRs['LastName'];
				$_REQUEST['FirstName']=$usersRs['FirstName'];
				$_REQUEST['Email']=$usersRs['Email'];
				$_SESSION['street']=$usersRs['street'];
				$_SESSION['street2']=$usersRs['street2'];
				$_SESSION['city']=$usersRs['city'];
				$_SESSION['state']=$usersRs['state'];
				$_SESSION['zip']=$usersRs['zip'];
			} else {
				redirect_to("./invalidLogin.php");
				exit;
			}
	  
	  	}
	}

	  $testsSQL="SELECT * FROM Tests WHERE ID='" . $_REQUEST['TestID'] . "'";
	  $result=mysql_query($testsSQL, $conn)
	  	or die(mysql_error());
	  $testsRows=mysql_fetch_assoc($result);
	  if($testsRows['MaxAttempts'] > 0 and isset($usersRs)) {
	  	$numAttempts=split(",",$usersRs['Attempts']);
		$count=0;
		foreach($numAttempts as $n) {
			if($n==$_REQUEST['TestID']) $count++;
		}
		if ($count >= $testsRows['MaxAttempts']) {
			redirect_to("./tooManyAttempts.php");
			session_destroy();
			exit;
		}
		$attemptsSQL="UPDATE Users SET Attempts = '" . $usersRs['Attempts'] . $_REQUEST['TestID'] . "," . "' WHERE ID = " . $usersRs['ID'];
		$attemptResult=mysql_query($attemptsSQL,$conn)
			or die("Invalid Query: " . $attemptsSQL . "<br>" . mysql_error());
	  }
	  if($usersRs['Limited']) {
	  	$limitedsubject=split(",",$usersRs['LimitedSubjects']);
		$allowed=false;
		foreach($limitedsubject as $l) {
			if($l==$testsRows['Subject']) $allowed=true;
		}
		if(!$allowed) {
			redirect_to("./invalidSubject.php");
			session_destroy();
			exit;
		}
	  }
	  if ($testsRows['QuestionLimit'] != 0) $limit = " LIMIT " . $testsRows['QuestionLimit'];
	  if ($testsRows['Random'] == 1) $var="' ORDER BY RAND()" . $limit;
	  if ($testsRows['Random'] != 1) $var="' ORDER BY 'sortOrder' ASC, 'ID' ASC";
	  if ($testsRows['LimitTime']) {
	  	$stoptime=time();
		$stoptime+=($testsRows['TimeLimitH']*3600);
		$stoptime+=($testsRows['TimeLimitM']*60);
	  }
	  $questionsSQL="SELECT * FROM Questions WHERE TestID='" . $_REQUEST['TestID'] . $var;
	  $result=mysql_query($questionsSQL, $conn);
	  $numQuestions=mysql_num_rows($result);
	  $questionsRows=mysql_fetch_array($result);
	  if ($numQuestions==0) {
			$delSQL="DELETE * FROM Sessions WHERE IP='" . $ip . "'";
			$result=mysql_query($delSQL, $conn);
	  		redirect_to("noQuestions.php");
			exit;
	  }
	  $firstQues=$questionsRows['ID'];
	  mysql_data_seek($result,0);
	  $questionOrder="";
	  while ($questionsRows = mysql_fetch_assoc($result)) {
	  	// echo($questionsRows['ID'] . ",");
		$ques=$questionsRows['ID'];
		$questionOrder=$questionOrder . $ques . ",";
		$lastQues=$ques;
	  }

	  // mysql_data_seek($result,$numQuestions-1);
	  // $lastQues=$questionsRows['ID'];
	  
	  $testsSQL="SELECT * FROM Tests WHERE ID='" . $_REQUEST['TestID'] . "'";
	  $result=mysql_query($testsSQL, $conn)
	  	or die(mysql_error());
	  $num_rows=mysql_num_rows($result);
	  $testsRows=mysql_fetch_array($result);
	  if ($num_rows = 0) {
		redirect_to("disabled.php");
		exit;
	  }
	  if (!$testsRows['Enabled'] == 1) {
		redirect_to("disabled.php");
		exit;
	  }
	  // echo date("n/j/Y g:i:s A")
	
	  $sessionsSQL="INSERT INTO Sessions
	  		(TestID,
			LastName,
			FirstName,
			Email,
			AltEmail,
			Notes,
			IP,
			StartTime,
			MaxTime,
			AllowQuit,
			takingTest,
			TestName,
			testImage,
			numQuestions,
			firstQuestion,
			lastQuestion,
			currentQuestion,
			questionNumber,
			questionOrder)
			VALUES
			('" . $_REQUEST['TestID'] . "',
			'" . addslashes($_REQUEST['LastName']) . "',
			'" . addslashes($_REQUEST['FirstName']) . "',
			'" . addslashes($_REQUEST['Email']) . "',
			'" . addslashes($_REQUEST['AltEmail']) . "',
			'" . addslashes($_REQUEST['Notes']) . "',
			'" . $ip . "',
			'" . time() . "',
			'" . $stoptime . "',
			'" . addslashes($testsRows['AllowQuit']) . "',
			'" . 1 . "',
			'" . addslashes($testsRows['TestName']) . "',
			'" . $testsRows['TestPicture'] . "',
			'" . $numQuestions . "',
			'" . $firstQues . "',
			'" . $lastQues . "',
			'" . $firstQues . "',
			'" . "1" . "',
			'" . $questionOrder . "')";
			
	  // echo $sessionsSQL;

	  $result=mysql_query($sessionsSQL, $conn)
	  	or die("Invalid Query: " . $sessionsSQL . " - " . mysql_error());
	  $sessID = mysql_insert_id($conn);
	  // setcookie('sessID',$sessID,time() + 14400,"/",SESSHOSTNAME);
	  setcookie('sessID',$sessID,time() + 14400);
	  
	  // This setcookie is for expiring sessions on browser close
	  //
	  // setcookie('sessID',$sessID,0,"/",SESSHOSTNAME);
	  
	  
	  // $_SESSION['ID'] = $sessID;
	  
	  $question=split(",",$questionOrder);
	  foreach($question as $q) {
	  	if($q>0) {
	  		$update = "INSERT INTO Answers
				(SessionID,
				TestID,
				QuesID)
				VALUES
				('" . $sessID . "',
				'" . $_REQUEST['TestID'] . "',
				'" . $q . "')";
			$result=mysql_query($update, $conn)
				or die(mysql_error());
		}
	   }

	  redirect_to("test.php")
?>
