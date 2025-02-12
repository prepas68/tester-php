<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/PasswordReset.php';

session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = false;
$showResetForm = false;

try {
    $db = Database::getInstance()->getConnection();
    $passwordReset = new \App\Auth\PasswordReset($db);
    
    // Spracovanie požiadavky na reset hesla
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid security token.');
        }
        
        switch ($_POST['action']) {
            case 'request':
                $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    throw new Exception('Invalid email address.');
                }
                
                $token = $passwordReset->createResetToken($email);
                
                // TODO: Implement email sending
                // For now, we'll just show the token (in production, this should be sent via email)
                $_SESSION['reset_token'] = $token;
                $success = true;
                break;
                
            case 'reset':
                $token = $_POST['token'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirmPassword'] ?? '';
                
                if (empty($token) || empty($password) || empty($confirmPassword)) {
                    throw new Exception('All fields are required.');
                }
                
                if ($password !== $confirmPassword) {
                    throw new Exception('Passwords do not match.');
                }
                
                if (strlen($password) < 8) {
                    throw new Exception('Password must be at least 8 characters long.');
                }
                
                if ($passwordReset->resetPassword($token, $password)) {
                    $success = true;
                    $_SESSION['password_reset_success'] = true;
                    header('Location: userLogin.php');
                    exit;
                }
                break;
        }
    }
    
    // Kontrola tokenu v URL
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        if ($passwordReset->validateToken($token)) {
            $showResetForm = true;
        } else {
            throw new Exception('Invalid or expired reset token.');
        }
    }
    
} catch (Exception $e) {
    $errors[] = $e->getMessage();
    error_log("Password reset error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - WebTester</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <style>
        .reset-container {
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
        
        .success-message {
            color: #28a745;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #28a745;
            border-radius: 4px;
            background-color: #d4edda;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
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

        <main class="reset-container">
            <h1><?= $showResetForm ? 'Set New Password' : 'Reset Password' ?></h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success && !$showResetForm): ?>
                <div class="success-message">
                    <p>Reset instructions have been sent to your email.</p>
                    <!-- For development only - remove in production -->
                    <p>Reset token: <?= htmlspecialchars($_SESSION['reset_token']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($showResetForm): ?>
                <!-- Form pre nastavenie nového hesla -->
                <form action="resetPassword.php" method="post" id="resetForm">
                    <input type="hidden" name="action" value="reset">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               required 
                               minlength="8">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" 
                               id="confirmPassword" 
                               name="confirmPassword" 
                               class="form-control" 
                               required>
                    </div>
                    
                    <button type="submit" class="btn-primary">Set New Password</button>
                </form>
            <?php else: ?>
                <!-- Form pre vyžiadanie resetu hesla -->
                <form action="resetPassword.php" method="post" id="requestForm">
                    <input type="hidden" name="action" value="request">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               required>
                    </div>
                    
                    <button type="submit" class="btn-primary">Request Password Reset</button>
                </form>
            <?php endif; ?>
            
            <p class="mt-3 text-center">
                <a href="userLogin.php">Back to Login</a>
            </p>
        </main>

        <footer>
            <hr>
            <?php include "./includes/copyright.php" ?>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long.');
                }
            });
        }
    });
    </script>
</body>
</html>