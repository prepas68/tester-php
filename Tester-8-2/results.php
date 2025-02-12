<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/Auth.php';
require_once __DIR__ . '/src/Test/ResultManager.php';

session_start();

// Kontrola prihlásenia
$auth = new \App\Auth\Auth(Database::getInstance()->getConnection());
if (!$auth->isLoggedIn()) {
    header('Location: userLogin.php');
    exit;
}

$error = null;
$result = null;

try {
    $db = Database::getInstance()->getConnection();
    $resultManager = new \App\Test\ResultManager($db);
    
    // Získanie ID aktuálneho pokusu
    $attemptId = $_SESSION['currentAttempt'] ?? null;
    if (!$attemptId) {
        throw new Exception('No active test attempt.');
    }
    
    // Vyhodnotenie testu
    $result = $resultManager->evaluateTest((int)$attemptId);
    
    // Vymazanie pokusu zo session
    unset($_SESSION['currentAttempt'], $_SESSION['testStarted'], $_SESSION['timeLimit']);
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Error in results.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results - WebTester</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <style>
        .results-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .status {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        
        .status.passed {
            color: #28a745;
        }
        
        .status.failed {
            color: #dc3545;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="results-container">
        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
                <p><a href="userArea.php">Return to Dashboard</a></p>
            </div>
        <?php elseif ($result): ?>
            <h1>Test Results</h1>
            
            <div class="status <?= $result['status'] === 'Passed' ? 'passed' : 'failed' ?>">
                <?= htmlspecialchars($result['status']) ?>
            </div>
            
            <p><strong>Test Name:</strong> <?= htmlspecialchars($result['testName']) ?></p>
            <p><strong>Score:</strong> <?= $result['score'] ?></p>
            <p><strong>Percentage:</strong> <?= number_format($result['percentage'], 2) ?>%</p>
            
            <a href="userArea.php" class="btn btn-primary">Return to Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>