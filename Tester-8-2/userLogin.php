<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/Auth.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $db = Database::getInstance()->getConnection();
    $auth = new \App\Auth\Auth($db);
    
    // Redirect if already logged in
    if ($auth->isLoggedIn()) {
        header('Location: userArea.php');
        exit;
    }
} catch (Exception $e) {
    error_log("System error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - WebTester</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .error-message {
            color: #dc3545;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #dc3545;
            border-radius: 4px;
            background-color: #f8d7da;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="images/webtestertop.gif" alt="WebTester" class="logo">
        </header>

        <main class="login-container">
            <h1>User Login</h1>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <form action="login.php" method="post" id="loginForm">
                <div class="form-group">
                    <label for="txtName">Username:</label>
                    <input type="text" 
                           id="txtName" 
                           name="txtName" 
                           class="form-control" 
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="txtPassword">Password:</label>
                    <input type="password" 
                           id="txtPassword" 
                           name="txtPassword" 
                           class="form-control" 
                           required>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="referrer" value="<?= htmlspecialchars($_SERVER['HTTP_REFERER'] ?? '') ?>">
                
                <button type="submit" class="btn-primary">Login</button>
            </form>
            
            <p class="mt-3">
                <a href="register.php">Create new account</a> |
                <a href="resetPassword.php">Forgot password?</a>
            </p>
        </main>

        <footer>
            <hr>
            <?php include "./includes/copyright.php" ?>
        </footer>
    </div>

    <script>
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('txtName').value.trim();
            const password = document.getElementById('txtPassword').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>
</html>