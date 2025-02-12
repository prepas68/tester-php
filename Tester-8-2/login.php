<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/Auth.php';

session_start();

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid security token. Please try again.';
        header('Location: userLogin.php');
        exit;
    }
}

try {
    $db = Database::getInstance()->getConnection();
    $auth = new \App\Auth\Auth($db);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['txtName'] ?? '');
        $password = $_POST['txtPassword'] ?? '';
        
        // Validácia vstupu
        if (empty($username) || empty($password)) {
            throw new Exception('Please fill in all fields.');
        }
        
        // Pokus o prihlásenie
        if ($auth->login($username, $password)) {
            // Úspešné prihlásenie
            $referrer = $_POST['referrer'] ?? 'userArea.php';
            
            // Log successful login
            error_log("Successful login for user: $username");
            
            // Redirect to intended page
            header("Location: " . filter_var($referrer, FILTER_SANITIZE_URL));
            exit;
        } else {
            throw new Exception('Invalid username or password.');
        }
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    error_log("Login error: " . $e->getMessage());
    header('Location: userLogin.php');
    exit;
}