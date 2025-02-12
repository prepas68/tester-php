<?php
declare(strict_types=1);

// Error reporting - počas vývoja
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Časová zóna
date_default_timezone_set('Europe/Bratislava');

// Autoloader
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/includes/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Session management
session_start([
    'cookie_httponly' => true,     // Ochrana proti XSS
    'cookie_secure' => true,       // Len cez HTTPS
    'cookie_samesite' => 'Lax',    // CSRF ochrana
    'use_strict_mode' => true      // Dodatočná ochrana
]);

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('CSRF token validation failed');
    }
}

// Load configuration
$config = require_once __DIR__ . '/config.php';