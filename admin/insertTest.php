<?php
ob_start();
// Copyright (C) 2003 - 2007 Eppler Software.  All rights reserved.
// Any redistribution or reproduction of any materials herein is strictly prohibited.
?>
<html><!-- InstanceBegin template="/Templates/Admin%20Layout.dwt.php" codeOutsideHTMLIsLocked="true" -->
<head>
<?php
session_start();
include "../includes/timerhead.php";
include "../includes/conn.php";
include "../includes/includes.php";
include "../includes/nocache.php";


?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>WebTester Online Testing</title>
<!-- InstanceEndEditable --> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
<link href="../includes/wtstyle.css" rel="stylesheet"  type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
<script language="javascript" type="text/javascript" src="../includes/tableH.js"></script>
<script language="Javascript" src="../editor/scripts/innovaeditor.js"></script>
<script language="javascript">
function checkIt(string)
{
	var detect = navigator.userAgent.toLowerCase();
	place = detect.indexOf(string) + 1;
	thestring = string;
	return place;
}
</script>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center"> 
  <table width="100%" height="50" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#333333" BORDERCOLORLIGHT="#999999" BORDERCOLORDARK="#333333">
    <tr> 
      <td width="338" align="center" valign="top"><div align="left"><a href="./index.php"><img src="../images/webtestertop.gif" width="337" height="75" border="0"></a></div></td>
      <td align="center" valign="middle"><!-- InstanceBeginEditable name="CurrentArea" -->
        <p class="style4">Create New Test</p>
        <!-- InstanceEndEditable --></td>
    </tr>
  </table>
  <div class="hr"><hr /></div>
  <table width="100%" height="50" border="0" align="center" cellpadding="3" cellspacing="1" bordercolor="#333333" BORDERCOLORLIGHT="#999999" BORDERCOLORDARK="#333333">
    <tr> 
      <td align="center" valign="top"><table width="100%"  border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td><div align="center"><span class="style1"><a href="testManage.php"><img src="../images/tests.gif" width="48" height="48" border="0"><br>
            Tests</a></span></div></td>
          <td><div align="center"><span class="style1"><a href="subjects.php"><img src="../images/subjects.gif" width="48" height="48" border="0"><br>
            Subjects</a></span></div></td>
          <td><div align="center"><span class="style1"><a href="currentSessions.php"><img src="../images/sessions.gif" width="48" height="48" border="0"><br>
            Sessions</a></span></div></td>
          <td><div align="center"><span class="style1"><a href="viewReports.php"><img src="../images/reports.gif" width="48" height="48" border="0"><br>
            Reports</a></span></div></td>
          <td><div align="center"><span class="style1"><a href="preferences.php"><img src="../images/preferences.gif" width="48" height="48" border="0"><br>
            Preferences</a></span></div></td>
          <td><div align="center"><span class="style1"><a href="logout.php"><img src="../images/logout.gif" width="48" height="48" border="0"><br>
            Logout</a></span></div></td>
        </tr>
      </table>        </td>
    </tr>
    <tr>
      <td align="center" valign="top">
	          <div align="left" class="style2">
	  <?php
	if (isset($_SESSION['loggedInName'])) {
		if ($_SESSION['loggedInName'] != "") {
		?>
          <div align="center">Currently logged in as:
            <?=$_SESSION['loggedInName']?>
		  </div>
            <?php
		}
	}
?>
		   <div class="hr"><hr /></div><br>
		   <!-- InstanceBeginEditable name="nav" --><a href="index.php">WebTester</a> &gt; <a href="./testManage.php">Test Management</a> &gt; <a href="./addTest.php">Create New Test</a> &gt; Adding... <!-- InstanceEndEditable --></div></td>
    </tr>
    <tr> 
      <td align="center" valign="top">		<div align="left"><!-- InstanceBeginEditable name="Content Area" -->Adding <?=$_POST['txtTestName']?>
<?php
		if ($_SESSION['loggedIn'] != "1") {
			redirect_to("index.php");
			exit;
		}
		
	  $sql="SELECT mimetex FROM Preferences";
	  $result=mysql_query($sql, $conn);
	  $pref=mysql_fetch_assoc($result);
	  
		if ($_POST['enabled']) {
			$enabled=1;
		} else {
			$enabled=0;
		}
		if ($_POST['random']) {
			$random=1;
		} else {
			$random=0;
		}
		if ($_POST['TimeEnabled']) {
			$limit=1;
		} else {
			$limit=0;
		}
		if ($_POST['login']) {
			$login=1;
		} else {
			$login=0;
		}
		if ($_POST['emailInstructor']) {
			$emailInstructor=1;
		} else {
			$emailInstructor=0;
		}
		if ($_POST['emailUsers']) {
			$emailUsers=1;
		} else {
			$emailUsers=0;
		}
		if ($_POST['autoSession']) {
			$autoSession=1;
		} else {
			$autoSession=0;
		}
		if ($_POST['allowQuit']) {
			$allowQuit=1;
		} else {
			$allowQuit=0;
		}
		if ($_POST['browseable']) {
			$browseable=1;
		} else {
			$browseable=0;
		}
		if ($_POST['altemail']) {
			$altemail=1;
		} else {
			$altemail=0;
		}
		// $timelimit=$_POST['TimeHours'] . ":" . $_POST['TimeMinutes'] . ":00";
		
	 $_POST['directions']=preg_replace('/\[tex\](.*?)\[\/tex\]/ie',
     "'<img src=\"" . $pref['mimetex'] . "?'.rawurlencode('$1').'\" align=\"middle\" />'",
     $_POST['directions']);
		
		$testsSQL="INSERT INTO Tests
			(TestName,
			PassingScore,
			Directions,
			Creator,
			Enabled,
			TimeLimitH,
			TimeLimitM,
			LimitTime,
			Subject,
			RequireLogin,
			EmailInstructor,
			EmailUsers,
			EmailTemplate,
			ReportTemplate,
			AutoSession,
			AllowQuit,
			Browseable,
			AltEmail,
			QuestionLimit,
			MaxAttempts,
			Random)
			VALUES
			('" . addslashes($_POST['txtTestName']) . "',
			'" . addslashes($_POST['txtPercent']) . "',
			'" . addslashes($_POST['directions']) . "',
			'" . addslashes($_SESSION['loggedInName']) . "',
			'" . $enabled . "',
			'" . $_POST['TimeHours'] . "',
			'" . $_POST['TimeMinutes'] . "',
			'" . $limit . "',
			'" . $_POST['subject'] . "',
			'" . $login . "',
			'" . $emailInstructor . "',
			'" . $emailUsers . "',
			'" . $_POST['emailTemplate'] . "',
			'" . $_POST['reportTemplate'] . "',
			'" . $autoSession . "',
			'" . $allowQuit . "',
			'" . $browseable . "',
			'" . $altemail . "',
			'" . $_POST['QuestionLimit'] . "',
			'" . $_POST['MaxAttempts'] . "',
			'" . $random . "')";
		$result=mysql_query($testsSQL, $conn)
			or die("Invalid Query: " . $testsSQL . " - " . mysql_error());
		$lastID=mysql_insert_id();
		// echo("Last ID: " . mysql_insert_id());
		redirect_to("questionEdit.php?TestID=" . $lastID);
		?>
		<!-- InstanceEndEditable --> </div>	<div class="hr"><hr /></div>
</td>
    </tr>
    <tr> 
      <td align="center" valign="top">
        <p><span class="style1 style5">Copyright &copy; 2003 - 2010<a href="./about.php">Eppler 
            Software</a> </span><br>
          <font size="-2">Page created in
        <?php include "../includes/timerfoot.php" ?> seconds.<br />
		Version 5.1.20101016</font><br>
</p>
      </td>
    </tr>
  </table>
</div>
</body>
<!-- InstanceEnd --></html>
<?php ob_end_flush() ?>
