<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/UserManager.php';

session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF protection
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid security token. Please try again.');
        }
        
        $db = Database::getInstance()->getConnection();
        $userManager = new \App\Auth\UserManager($db);
        
        // Create user
        $userId = $userManager->createUser([
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'firstName' => trim($_POST['firstName'] ?? ''),
            'lastName' => trim($_POST['lastName'] ?? ''),
            'level' => 'student'
        ]);
        
        // Log successful registration
        error_log("New user registered with ID: $userId");
        
        $success = true;
        $_SESSION['registration_success'] = true;
        
        // Redirect to login page
        header('Location: userLogin.php');
        exit;
        
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        error_log("Registration error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WebTester</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <style>
        .register-container {
            max-width: 500px;
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
            font-weight: bold;
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
        
        .password-requirements {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
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

        <main class="register-container">
            <h1>Create New Account</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="register.php" method="post" id="registerForm">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           required 
                           pattern="[a-zA-Z0-9_-]+"
                           title="Username can only contain letters, numbers, underscores and dashes"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required 
                           minlength="8">
                    <div class="password-requirements">
                        Password must be at least 8 characters long
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" 
                           id="confirmPassword" 
                           name="confirmPassword" 
                           class="form-control" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" 
                           id="firstName" 
                           name="firstName" 
                           class="form-control" 
                           required
                           value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" 
                           id="lastName" 
                           name="lastName" 
                           class="form-control" 
                           required
                           value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>">
                </div>
                
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <button type="submit" class="btn-primary">Register</button>
            </form>
            
            <p class="mt-3">
                Already have an account? <a href="userLogin.php">Login here</a>
            </p>
        </main>

        <footer>
            <hr>
            <?php include "./includes/copyright.php" ?>
        </footer>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
            
            // Additional client-side validation
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            
            if (!username || !email || !firstName || !lastName) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>
</html>