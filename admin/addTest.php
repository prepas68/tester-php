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
<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.style8 {font-size: 10px}
-->
</style>
<!-- InstanceEndEditable -->
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
        <p class="style4">Create New Test </p>
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
		   <!-- InstanceBeginEditable name="nav" --><a href="index.php">WebTester</a> &gt; <a href="./testManage.php">Test Management</a> &gt; Create New Test <!-- InstanceEndEditable --></div></td>
    </tr>
    <tr> 
      <td align="center" valign="top">		<div align="left"><!-- InstanceBeginEditable name="Content Area" --> 
        <?php if ($_SESSION['loggedIn'] != "1") {
			redirect_to("index.php");
			exit;
		}
		?>
		<form name="form1" method="post" action="insertTest.php">
		<p class="style7">Create New  Test</p>
		<table width="100%"  border="0" cellspacing="2" cellpadding="0" onMouseOut="javascript:highlightTableRowVersionA(0);">
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172"><span class="style1">Name:</span></td>
    <td colspan="2"><input name="txtTestName" type="text" id="txtTestName" size="60"></td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" class="style1">Passing Score:</td>
    <td colspan="2"><input name="txtPercent" type="text" id="txtPercent" value="60" size="2" maxlength="2">
      <span class="style1">%</span></td>
    </tr>
  <tr onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td bgcolor="#E2ECFF" width="172" valign="top" class="style1">Instructions: <div id="safari" style="visibility:hidden">(HTML editor not supported in Safari)</div></td>
    <td bgcolor="#E2ECFF"><textarea name="directions" cols="80" rows="15" id="directions"><?php
					function encodeHTML($sHTML)
					{
						$sHTML=ereg_replace("&","&amp;",$sHTML);
						$sHTML=ereg_replace("<","&lt;",$sHTML);
						$sHTML=ereg_replace(">","&gt;",$sHTML);
						return $sHTML;
					}

					if(isset($_POST["directions"]))
					{
						$sContent=stripslashes($_POST['directions']); //Remove slashes
						echo encodeHTML($sContent);
					}
				?></textarea>				    
      <script>
					if (!checkIt('safari'))
					{
						var oEdit1 = new InnovaEditor("oEdit1");
						oEdit1.width="100%";
						oEdit1.height="350px";
						oEdit1.arrStyle=[["BODY",false,"","background:white;"]];
						// oEdit1.cmdAssetManager="modalDialogShow('../../../assetmanager/assetmanager.php?TestID=<?=$_REQUEST['TestID']?>',640,445);";
						oEdit1.btnPrint=true;
						oEdit1.btnPasteText=true;
						oEdit1.btnFlash=true;
						oEdit1.btnMedia=true;
						oEdit1.btnLTR=true;
						oEdit1.btnRTL=true;
						oEdit1.btnSpellCheck=true;
						oEdit1.btnStrikethrough=true;
						oEdit1.btnSuperscript=true;
						oEdit1.btnSubscript=true;
						oEdit1.btnClearAll=true;
						oEdit1.REPLACE("directions");
					} else {
						document.getElementById('safari').style.visibility = 'visible'; 
					}
				</script>	  </td><td bgcolor="#E2ECFF">&nbsp;</td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">Subject:</span></td>
    <td><select name="subject" id="subject">
	<?php
	$subjectSQL="SELECT * FROM Subjects";
	$subjectResult=mysql_query($subjectSQL, $conn);
	while($subject = mysql_fetch_assoc($subjectResult)) {
	?>
	<option value="<?=$subject['ID']?>"><?=$subject['Name']?></option>
	<?php
	}
	?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">
      <label>Enabled:</label>
</span></td>
    <td onClick="getElementById('enabled').checked=!getElementById('enabled').checked;"><span class="style1">Check this box to enable this test </span></td>
    <td>      <input name="enabled" type="checkbox" value="enabled" id="enabled" checked>      </td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td valign="top"><span class="style1">
    <label>Browseable:</label>
    </span></td>
    <td onClick="getElementById('browseable').checked=!getElementById('browseable').checked;"><span class="style1">Check this box to show the test on the main page</span></td>
    <td><input name="browseable" type="checkbox" value="browseable" id="browseable" checked></td>
  </tr>
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">
      <label></label>      
      <label>Randomize Questions:</label>
</span></td>
    <td onClick="getElementById('random').checked=!getElementById('random').checked;"><span class="style1">
    Check this box to have the questions shuffled at the beginning of every test       </span></td>
    <td><input name="random" type="checkbox" value="random" id="random"></td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">Require Login: </span></td>
    <td onClick="getElementById('login').checked=!getElementById('login').checked;" class="style1">Check this box to require users to login to begin this test </td>
    <td><input name="login" type="checkbox" id="login" value="login"></td>
  </tr>
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">Email Instructor Results:</span></td>
    <td onClick="getElementById('emailInstructor').checked=!getElementById('emailInstructor').checked;"><div align="center" class="style1">
        <div align="left">Check this box to have results emailed to the test instructor </div>
    </div></td>
    <td><input name="emailInstructor" type="checkbox" id="emailInstructor" value="1" <?=$var5?>></td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">Email User Results:</span></td>
    <td onClick="getElementById('emailUsers').checked=!getElementById('emailUsers').checked;"><div align="center" class="style1">
        <div align="left">Check this box to have results emailed to the test taker </div>
    </div></td>
    <td><input name="emailUsers" type="checkbox" id="emailUsers" value="1" <?=$var6?>></td>
  </tr>
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td valign="top"><span class="style1">Alternative Email:</span></td>
    <td onClick="getElementById('allowQuit').checked=!getElementById('allowQuit').checked;"><span class="style1">Check this box to provide an alternative email field</span></td>
    <td><input name="altemail" type="checkbox" id="altemail" value="1" <?=$var10?>></td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td valign="top"><span class="style1">Allow Quit: </span></td>
    <td onClick="getElementById('allowQuit').checked=!getElementById('allowQuit').checked;"><span class="style1">Check this box to allow users to terminate a test early </span></td>
    <td><input name="allowQuit" type="checkbox" id="allowQuit" value="1"></td>
  </tr>
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td valign="top"><span class="style1">Auto Session Reset: </span></td>
    <td onClick="getElementById('autoSession').checked=!getElementById('autoSession').checked;"><span class="style1">Check this box to enable users to automatically reset workstations at the end of a test </span></td>
    <td><input name="autoSession" type="checkbox" id="autoSession" value="1" <?=$var6?>></td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td valign="top"><span class="style1">Maximum Attempts:</span></td>
    <td><input name="MaxAttempts" type="text" id="MaxAttempts" value="0" size="4" maxlength="4">
      <span class="style1">(Enter 0 to allow unlimited tries. Require Login must be enabled) </span> </td>
    <td>&nbsp;</td>
  </tr>
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">Email Template: </span></td>
    <td><select name="emailTemplate" id="emailTemplate">
        <?php
	$emailSQL="SELECT * FROM EmailTemplates";
	$emailResult=mysql_query($emailSQL, $conn);
	while($email = mysql_fetch_assoc($emailResult)) {
	?>
        <option value="<?=$email['ID']?>">
        <?=$email['Name']?>
        </option>
        <?php
	}
	?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">Report Template: </span></td>
    <td><select name="reportTemplate" id="reportTemplate">
        <?php
	$reportSQL="SELECT * FROM ReportTemplates";
	$reportResult=mysql_query($reportSQL, $conn);
	while($report = mysql_fetch_assoc($reportResult)) {
	?>
        <option value="<?=$report['ID']?>">
        <?=$report['Name']?>
        </option>
        <?php
	}
	?>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr class="d0" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td width="172" valign="top"><span class="style1">
      <label>Time Limit: </label>
    </span></td>
    <td class="style1 style8">      <input name="TimeHours" type="text" id="TimeHours" value="01" size="2" maxlength="2" disabled>
      h
      :
      <input name="TimeMinutes" type="text" id="TimeMinutes" value="00" size="2" maxlength="2" disabled>
      m    </td>
    <td>      <input name="TimeEnabled" type="checkbox" id="TimeEnabled" value="TimeEnabled" onClick="TimeHours.disabled=!(this.checked); TimeMinutes.disabled=!(this.checked); return true;"></td>
  </tr>
  <tr class="d1" onMouseOver="javascript:highlightTableRowVersionA(this, '#FFFF99');">
    <td valign="top"><span class="style1">Question Limit</span></td>
    <td><input name="QuestionLimit" type="text" id="QuestionLimit" value="0" size="4" maxlength="4">
      <span class="style1">(Enter 0 to ask all questions assigned to this test)</span> </td>
    <td>&nbsp;</td>
  </tr>
  <tr align="center">
    <td colspan="3" valign="top"><input type="submit" name="Submit" value="Create"></td>
    </tr>
</table>
          </form>
        <p>&nbsp;</p>
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
