<?php
require_once "../includes/conn.php";
require_once "../includes/includes.php";

session_start();

// Kontrola či boli odoslané prihlasovacie údaje
if (!isset($_POST['txtName']) || !isset($_POST['txtPassword'])) {
    redirect_to("index.php");
    exit;
}

// Prevencia proti SQL injection použitím prepared statements
$usersSQL = "SELECT * FROM Users WHERE Username = ?";
$stmt = $conn->prepare($usersSQL);

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    redirect_to("index.php");
    exit;
}

// Bind parameter a execute
$stmt->bind_param("s", $_POST['txtName']);

if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    $stmt->close();
    redirect_to("index.php");
    exit;
}

// Get result
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Kontrola hesla
    if ($user['Password'] === md5($_POST['txtPassword'])) {
        if ($user['Admin']) {
            // Nastavenie session premenných
            $_SESSION['loggedIn'] = 1; // Zmenené na integer
            $_SESSION['loggedInName'] = htmlspecialchars($user['Username']);
            $_SESSION['userID'] = (int)$user['ID'];
            
            // Log úspešného prihlásenia
            $currentTime = date('Y-m-d H:i:s');
            $logSQL = "INSERT INTO user_logs (user_id, username, login_time, ip_address) VALUES (?, ?, ?, ?)";
            $logStmt = $conn->prepare($logSQL);
            if ($logStmt) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $logStmt->bind_param("isss", $user['ID'], $user['Username'], $currentTime, $ip);
                $logStmt->execute();
                $logStmt->close();
            }

            // Presmerovanie na požadovanú stránku alebo dashboard
            $stmt->close();
            if (!empty($_POST['referrer'])) {
                redirect_to($_POST['referrer']);
            } else {
                redirect_to("testManage.php"); // Presmerovanie na hlavnú admin stránku
            }
            exit;
        }
    }
}

// Ak prihlásenie zlyhalo
$stmt->close();
$_SESSION['error_message'] = "Nesprávne prihlasovacie údaje";
redirect_to("index.php");
exit;
?>