<?php
ob_start();
// Copyright (C) 2003 - 2025 KAI.  All rights reserved.
// Any redistribution or reproduction of any materials herein is strictly prohibited.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    session_start();
    require_once "../includes/timerhead.php";
    require_once "../includes/conn.php";
    require_once "../includes/includes.php";
    require_once "../includes/nocache.php";
    ?>
    <title>WebTester Online Testing</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../includes/wtstyle.css" rel="stylesheet" type="text/css">
    
    <script>
    function MM_preloadImages() {
        const d = document;
        if(d.images) {
            if(!d.MM_p) d.MM_p = [];
            const a = MM_preloadImages.arguments;
            for(let i = 0; i < a.length; i++) {
                if(a[i].indexOf("#") !== 0) {
                    d.MM_p[d.MM_p.length] = new Image;
                    d.MM_p[d.MM_p.length-1].src = a[i];
                }
            }
        }
    }

    function MM_swapImgRestore() {
        const a = document.MM_sr;
        for(let i = 0; a && i < a.length && (x=a[i]) && x.oSrc; i++) {
            x.src = x.oSrc;
        }
    }

    function MM_findObj(n, d) {
        let p,i,x;
        if(!d) d = document;
        if((p = n.indexOf("?")) > 0 && parent.frames.length) {
            d = parent.frames[n.substring(p+1)].document;
            n = n.substring(0,p);
        }
        if(!(x = d[n]) && d.all) x = d.all[n];
        for(i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
        for(i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n,d.layers[i].document);
        if(!x && d.getElementById) x = d.getElementById(n);
        return x;
    }

    function MM_swapImage() {
        let i,j=0,x;
        const a = MM_swapImage.arguments;
        document.MM_sr = [];
        for(i = 0; i < (a.length-2); i += 3) {
            if((x = MM_findObj(a[i])) !== null) {
                document.MM_sr[j++] = x;
                if(!x.oSrc) x.oSrc = x.src;
                x.src = a[i+2];
            }
        }
    }

    function checkIt(string) {
        const detect = navigator.userAgent.toLowerCase();
        return detect.indexOf(string) + 1;
    }
    </script>
    <script src="../includes/tableH.js"></script>
    <script src="../editor/scripts/innovaeditor.js"></script>
</head>

<body class="m-0 p-0">
<div class="container">
    <table class="w-100" style="height: 50px" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="338" class="align-top text-left">
                <a href="./index.php">
                    <img src="../images/webtestertop.gif" width="337" height="75" alt="WebTester" border="0">
                </a>
            </td>
            <td class="align-middle text-center">
                <p class="style4">Login</p>
            </td>
        </tr>
    </table>
    
    <div class="hr"><hr></div>
    
    <table class="w-100" style="height: 50px" border="0" cellpadding="3" cellspacing="1">
        <tr>
            <td class="align-top text-center">
                <!-- Navigation Menu -->
                <table class="w-100 border-0" cellspacing="2" cellpadding="0">
                    <tr>
                        <td class="text-center">
                            <a href="testManage.php" class="style1">
                                <img src="../images/tests.gif" width="48" height="48" alt="Tests" border="0"><br>Tests
                            </a>
                        </td>
                        <!-- Other navigation items... -->
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="align-top text-center">
                <div class="text-left style2">
                   <?php
// V index.php
if (isset($_SESSION['error_message'])) {
    echo '<div class="error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== 1) {
    // Zobraziť prihlasovací formulár
    ?>
    <form action="login.php" method="post" name="login" id="login">
        <p>
            <label for="txtName">Name:</label><br>
            <input name="txtName" type="text" id="txtName" size="30" required><br>
            
            <label for="txtPassword">Password:</label><br>
            <input name="txtPassword" type="password" id="txtPassword" size="30" required><br>
            
            <input name="referrer" type="hidden" value="<?= htmlspecialchars($_SERVER['HTTP_REFERER'] ?? '') ?>">
            <input name="Login" type="submit" value="Login">
        </p>
    </form>
    <script>
        document.getElementById('txtName').focus();
    </script>
    <?php
} else {
    // Už je prihlásený, presmerovať na admin rozhranie
    redirect_to("testManage.php");
}
?>
                </div>
                <div class="hr"><hr></div>
            </td>
        </tr>
        <tr>
            <td class="align-top text-center">
                <p>
                    <span class="style1 style5">
                        Copyright &copy; 2025 - <?= date('Y') ?> 
                        <a href="./about.php">KAI</a>
                    </span><br>
                    <small>
                        Page created in <?php include "../includes/timerfoot.php" ?> seconds.<br>
                        Version 2.4.2025
                    </small>
                </p>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
<?php ob_end_flush(); ?>