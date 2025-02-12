<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/Auth.php';
require_once __DIR__ . '/src/Test/TestManager.php';

session_start();

// Kontrola prihlásenia
$auth = new \App\Auth\Auth(Database::getInstance()->getConnection());
if (!$auth->isLoggedIn()) {
    header('Location: userLogin.php');
    exit;
}

$error = null;
$testInfo = null;

try {
    $db = Database::getInstance()->getConnection();
    $testManager = new \App\Test\TestManager($db);
    
    // Získanie ID testu z URL
    $testId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$testId) {
        throw new Exception('Invalid test ID.');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Užívateľ potvrdil začatie testu
        $attempt = $testManager->startTest($testId, $_SESSION['userId']);
        
        // Uložiť informácie o pokuse do session
        $_SESSION['currentAttempt'] = $attempt['attemptId'];
        $_SESSION['testStarted'] = time();
        $_SESSION['timeLimit'] = $attempt['test']['TimeLimit'];
        
        // Presmerovanie na prvú otázku
        header('Location: test.php?question=1');
        exit;
    }
    
    // Získanie informácií o teste
    $stmt = $db->prepare("
        SELECT t.*, s.Name as SubjectName,
               (SELECT COUNT(*) FROM Questions q WHERE q.TestID = t.ID AND q.Enabled = 1) as TotalQuestions
        FROM Tests t
        LEFT JOIN Subjects s ON t.Subject = s.ID
        WHERE t.ID = ? AND t.Enabled = 1
    ");
    $stmt->execute([$testId]);
    $testInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$testInfo) {
        throw new Exception('Test not found or not available.');
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Error in startTest.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Test - WebTester</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <style>
        .test-info-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .test-details {
            margin: 2rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .test-details dl {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            margin: 0;
        }
        
        .test-details dt {
            font-weight: bold;
        }
        
        .warning {
            margin: 1rem 0;
            padding: 1rem;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            color: #856404;
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="test-info-container">
        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
                <p><a href="userArea.php">Return to Dashboard</a></p>
            </div>
        <?php elseif ($testInfo): ?>
            <h1><?= htmlspecialchars($testInfo['TestName']) ?></h1>
            
            <div class="test-details">
                <dl>
                    <dt>Subject:</dt>
                    <dd><?= htmlspecialchars($testInfo['SubjectName']) ?></dd>
                    
                    <dt>Time Limit:</dt>
                    <dd><?= $testInfo['TimeLimit'] ?> minutes</dd>
                    
                    <dt>Questions:</dt>
                    <dd><?= $testInfo['QuestionsPerTest'] ?> (from pool of <?= $testInfo['TotalQuestions'] ?>)</dd>
                    
                    <dt>Passing Score:</dt>
                    <dd><?= $testInfo['PassingScore'] ?>%</dd>
                    
                    <?php if ($testInfo['RetakeAfter'] > 0): ?>
                        <dt>Retake After:</dt>
                        <dd><?= $testInfo['RetakeAfter'] ?> hours</dd>
                    <?php endif; ?>
                </dl>
            </div>
            
            <?php if (!empty($testInfo['Instructions'])): ?>
                <div class="instructions">
                    <h2>Instructions</h2>
                    <?= nl2br(htmlspecialchars($testInfo['Instructions'])) ?>
                </div>
            <?php endif; ?>
            
            <div class="warning">
                <strong>Important:</strong>
                <ul>
                    <li>Once you start the test, the timer cannot be paused.</li>
                    <li>Ensure you have a stable internet connection.</li>
                    <li>Do not refresh the page or use browser navigation during the test.</li>
                    <li>Answer all questions before the time limit expires.</li>
                </ul>
            </div>
            
            <div class="btn-group">
                <form method="post" id="startTestForm">
                    <button type="submit" class="btn btn-primary">Start Test</button>
                </form>
                <a href="userArea.php" class="btn btn-secondary">Cancel</a>
            </div>
            
        <?php endif; ?>
    </div>

    <script>
    document.getElementById('startTestForm')?.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to start the test? The timer will begin immediately.')) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html>