<?php
// Inicializácia premenných s null coalescing operátorom
$go = $go ?? false;
$inTest = $inTest ?? false;
$inReview = $inReview ?? false;
$finished = $finished ?? false;

// Vytvorenie SQL dotazu podľa typu session
$strSQL = IPSESSIONS 
    ? "SELECT * FROM Sessions WHERE IP = ?" 
    : "SELECT * FROM Sessions WHERE ID = ?";

// Prepare statement pre bezpečnejší prístup
$stmt = $conn->prepare($strSQL);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameter podľa typu session
if (IPSESSIONS) {
    $stmt->bind_param("s", $ip);
} else {
    $stmt->bind_param("s", $sessID);
}

// Vykonanie dotazu
$stmt->execute();
$result = $stmt->get_result();
$num_rows = $result->num_rows;
$row = $result->fetch_array();

if ($num_rows != 0) {
    if ($go) {
        // Vymazanie odpovedí
        $stmt = $conn->prepare("DELETE FROM Answers WHERE SessionID = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $sessID);
        if (!$stmt->execute()) {
            die("Invalid Query: " . $stmt->error);
        }

        // Vymazanie session
        $stmt = $conn->prepare(IPSESSIONS 
            ? "DELETE FROM Sessions WHERE IP = ?"
            : "DELETE FROM Sessions WHERE ID = ?"
        );
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        if (IPSESSIONS) {
            $stmt->bind_param("s", $ip);
        } else {
            $stmt->bind_param("s", $sessID);
        }
        
        if (!$stmt->execute()) {
            die("Invalid Query: " . $stmt->error);
        }

        session_destroy();
        redirect_to("go.php?testID=" . $_GET['testID']);
        exit;
    }

    if ($row['finished']) {
        if (!$finished) {
            redirect_to("grade.php");
            exit;
        }
    } elseif ($row['review']) {
        if (!$inReview && !isset($_POST['Save'])) {
            redirect_to("reviewTest.php");
            exit;
        } elseif (isset($_REQUEST['Grade'])) {
            // do nothing
        }
    } elseif ($row['takingTest']) {
        if (!$inTest) {
            redirect_to("test.php");
            exit;
        }
    }
} else {
    if ($inTest) {
        redirect_to("index.php");
    }
}

// Zatvorenie prepared statements
$stmt->close();
?>