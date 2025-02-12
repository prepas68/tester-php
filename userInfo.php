<?php
ob_start();
include "./includes/timerhead.php";
include "./includes/conn.php";
include "./includes/includes.php";
include "./includes/nocache.php";
include "./includes/validation.php";
		session_start();



	  if (isset($_REQUEST['testID'])) {
	  	$testID=$_REQUEST['testID'];
	  }
	  if (isset($_POST['testID'])) {
	  	$testID=$_POST['testID'];
	  }
	  if (!isset($testID)) {
	  	redirect_to("index.php");
	  }

	$testSQL="SELECT * FROM Tests WHERE ID=" . $testID;
	$testResult=mysql_query($testSQL, $conn);
	$testRs=mysql_fetch_assoc($testResult);
	

?>
<html><!-- InstanceBegin template="/Templates/Test%20Layout.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<link href="includes/wtstyle.css" rel="stylesheet"  type="text/css">
<?
// Copyright (C) 2003 - 2007 Eppler Software.  All rights reserved.
// Any redistribution or reproduction of any materials herein is strictly prohibited.
?>
<!-- InstanceBeginEditable name="doctitle" -->
<title><?=TITLE?></title>
<!-- InstanceEndEditable --> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.style8 {color: #FF0000}
-->
</style>
<!-- InstanceEndEditable -->
<style type="text/css">
input.next
{ 
  margin-top       : 1.6em;
  float            : right;
}
.t {background: url(images/dot.gif) 0 0 repeat-x; width: 20em}
.b {background: url(images/dot.gif) 0 100% repeat-x}
.l {background: url(images/dot.gif) 0 0 repeat-y}
.r {background: url(images/dot.gif) 100% 0 repeat-y}
.bl {background: url(images/bl.gif) 0 100% no-repeat}
.br {background: url(images/br.gif) 100% 100% no-repeat}
.tl {background: url(images/tl.gif) 0 0 no-repeat}
.tr {background: url(images/tr.gif) 100% 0 no-repeat; padding:10px}
p {font-family: sans-serif; text-align:left}
<?php
if (DISABLE_PRINT) {  ?>
@media print {
body {display:none}
<?php
}
?>
}

</style>
</head>
<?php
	  if (IPSESSIONS) {
	  	$strSQL="SELECT * FROM Sessions WHERE IP='" . $ip . "'";
	  } else {
	  	$strSQL="SELECT * FROM Sessions WHERE ID='" . $sessID . "'";
	  }
	  // $strSQL="SELECT * From Sessions WHERE IP='" . $ip . "'";
	  $result=mysql_query($strSQL, $conn)
	  	or $myVar=true;
	 
	  if (!isset($myVar)) {
		  $num_records=mysql_num_rows($result);
		  $row=mysql_fetch_array($result);
		  if ($num_records == 0) {
	  		$myVar = true;
		  } else {
		  	$myVar = false;
		  }
	  }

?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=BGCOLOR?>">
<?php
include "./includes/top.php";
?>
<div align="center"> 
  <table width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr> 
      <td height="47" align="left" valign="middle"><img src="images/webtestertop.gif" width="<?=LOGOW?>" height="<?=LOGOH?>">
        <br>        
      </td>
      <td align="center" valign="middle">
	            <?php if (!$myVar) { ?>
	      
        <div class="t"><div class="b"><div class="l"><div class="r"><div class="bl"><div class="br"><div class="tl">
		  <div class="tr"><font face="Arial, Helvetica, sans-serif"><?php echo($row['FirstName'] . $myVar . " " . $row['LastName']) ?><br>
		    <?php } ?>
        <?php
 if ($myVar != 1) {
 	if ($row['TestName'] != "") {
 ?>
        <?=$row['TestName']?><br>
		<div id="countdowncontainer"></div>
		<?
		if ($row['AllowQuit']) {
			if (!$row['review'] and !$quit) {
		?>
		<div id="quit"><a href="./quitTest.php">Quit</a></div>
		<? 		} 
			}?>
		</font> 
		</div></div></div></div></div></div></div></div>
        <?php
 }
}
?> 
</td>
    </tr>
    <tr> 
      <td colspan="2" align="left" valign="top"> <div class="hr">
        <hr />
      </div>       
        <!-- InstanceBeginEditable name="Content Area" --> 
		<form name="form1" method="post" action="startTest.php">
		<?php
	if($testRs['RequireLogin']) {
		?>
          <p>Username:<br>
		  <?php
		  if (isset($_REQUEST['username'])) {
		  	$username=stripslashes($_REQUEST['username']);
		  }
		  if (isset($_REQUEST['password'])) {
		  	$password=stripslashes($_REQUEST['password']);
		  }
		  if (isset($_SESSION['username'])) {
		  	$username=stripslashes($_SESSION['username']);
			$password=stripslashes($_SESSION['password']);
		  }
		  if (PHPBB) {
		  	define('IN_PHPBB', true); 
			$phpbb_root_path = PHPBB_PATH;
			include($phpbb_root_path . 'extension.inc');
			include($phpbb_root_path . 'common.php');
			include($phpbb_root_path . 'config.php'); 
			$userdata = session_pagestart($user_ip, PAGE_INDEX);
			init_userprefs($userdata);
			if($userdata['session_logged_in']) { // if logged in
				echo("Logged In");
				$_SESSION['TestID']=$testID;
				redirect_to("startTest.php");
				ob_end_flush();
				exit;
			}
		  }
		  ?>
            <input name="username" type="text" id="username" size="30" maxlength="254" value="<?=$username?>">
			<script language="JavaScript">
			<!--

			document.form1.username.focus();

			//-->
		  </script>
          </p>
          <p>Password:<br>
		  	<?php
			if ($password!="") {
			?>
            <input name="passwordmd5" type="password" id="passwordmd5" size="30" maxlength="254" value="<?=$password?>"> 
			<?php } else { ?>
            <input name="password" type="password" id="password" size="30" maxlength="254"> 
			<?php } ?>			
          </p>	<?php
	} else {
		?>
        <p>Vyplnte nasledujuce udaje:</p>
        <?php if ($_REQUEST['noname']) { ?>
        <p class="style8">Musite vyplnit meno a priezvisko</p>
        <?php } ?>
        <p><?=FIRST_NAME?><br>
            <input name="FirstName" type="text" id="FirstName" size="30" maxlength="254">
          </p>
		  <script language="JavaScript">
			<!--

			document.form1.FirstName.focus();

			//-->
		  </script>
          <p><?=LAST_NAME?><br>
            <input name="LastName" type="text" id="LastName" size="30" maxlength="254">
          </p>
		  <?php
		  if($testRs['EmailUsers']) {
		  	if ($_REQUEST['emailerror']) {
		  ?>
          <p class="style8">Email adresa nie je vyplnena</p>
          <?php } ?>
          <?php if ($_REQUEST['noemail']) { ?>
          <p class="style8">Musite zadat email</p>
          <?php } ?>
          <p><?=EMAIL_ADDRESS?><br>
            <input name="Email" type="text" id="Email" size="30" maxlength="254">
			<input name="RequireEmail" type="hidden" value="true">
          </p>
          <?php
		  	if (VERIFY_EMAIL) {
		  ?>
          <p><?=VERIFY_EMAIL_STRING?><br>
            <input name="VerifyEmail" type="text" id="VerifyEmail" size="30" maxlength="254">
          </p>
          <?php
		  	}
		  }
		  if ($testRs['AltEmail']) {
		  ?>
          <p><?=ALT_EMAIL?><br>
            <input name="AltEmail" type="text" id="AltEmail" size="30" maxlength="254">
          </p>
          <?php
		  	if (VERIFY_EMAIL) {
		  ?>
          <p><?=VERIFY_ALT_EMAIL?><br>
            <input name="VerifyAltEmail" type="text" id="VerifyAltEmail" size="30" maxlength="254">
          </p>
          <?php
		  	}
		  }
	}
		  ?>
		  <?php
		  $prefsSQL="SELECT * FROM Preferences";
		  $prefsResult=mysql_query($prefsSQL, $conn);
		  $prefs=mysql_fetch_assoc($prefsResult);
		  $notes=$prefs['notes'];
		  ob_end_flush();
		  ?>
          <p><?=$notes?>:<br>
            <input name="Notes" type="text" id="Notes" size="30" maxlength="254">
          </p>
          <p> 
            <input type="submit" name="Submit" value="<?=START_BUTTON?>">
            <input name="TestID" type="hidden" id="TestID" value="<?=$testID?>">
            <input name="IPAddress" type="hidden" id="IPAddress" value="<?=$ip?>">
          </p>
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
