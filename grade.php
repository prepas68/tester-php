<?php
ob_start();
session_start();
include "./includes/conn.php";
include "./includes/includes.php";

// Získaj údaje o aktuálnej session, ktorá zahŕňa informácie o teste.
if (IPSESSIONS) {
    $strSQL = "SELECT * FROM Sessions WHERE IP='" . $_SERVER['REMOTE_ADDR'] . "'";
} else {
    $strSQL = "SELECT * FROM Sessions WHERE ID='" . session_id() . "'";
}
$result = mysql_query($strSQL, $conn) or die(mysql_error());
$row = mysql_fetch_array($result);

if ($row) {
    $testName = $row['TestName'];
    $testDate = date("n/j/Y", strtotime($row['StartTime']));
    $correctAnswers = $row['CorrectAnswers'];
    $totalPoints = $row['TotalPoints'];
    $score = ($correctAnswers / $totalPoints) * 100;
    $evaluation = $score >= 60 ? "Úspešný" : "Neúspešný";
?>
<html>
<head>
<title>Výsledky testu</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
</head>
<body>
<div align="center"> 
    <h2>Výsledky testu</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr> 
            <td>Test:</td>
            <td><?=$testName?></td>
        </tr>
        <tr> 
            <td>Dátum:</td>
            <td><?=$testDate?></td>
        </tr>
        <tr> 
            <td>Správne:</td>
            <td><?=$correctAnswers?></td>
        </tr>
        <tr> 
            <td>Max. bodov:</td>
            <td><?=$totalPoints?></td>
        </tr>
        <tr> 
            <td>Skóre:</td>
            <td><?=sprintf("%.2f%%", $score)?></td>
        </tr>
        <tr> 
            <td>Výsledné hodnotenie:</td>
            <td><?=$evaluation?></td>
        </tr>
    </table>
    <form action="clearResults.php" method="post">
        <input type="submit" name="Clear" value="Hotovo">
    </form>
</div>
</body>
</html>
<?php
} else {
    echo "Nepodarilo sa načítať údaje o teste.";
}
ob_end_flush();
?>
