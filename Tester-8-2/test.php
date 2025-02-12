<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/Auth.php';
require_once __DIR__ . '/src/Test/QuestionManager.php';

session_start();

// Kontrola prihlásenia
$auth = new \App\Auth\Auth(Database::getInstance()->getConnection());
if (!$auth->isLoggedIn()) {
    header('Location: userLogin.php');
    exit;
}

$error = null;
$question = null;
$questionOrder = filter_input(INPUT_GET, 'question', FILTER_VALIDATE_INT) ?: 1;

try {
    $db = Database::getInstance()->getConnection();
    $questionManager = new \App\Test\QuestionManager($db);
    
    // Získanie ID aktuálneho pokusu
    $attemptId = $_SESSION['currentAttempt'] ?? null;
    if (!$attemptId) {
        throw new Exception('No active test attempt.');
    }
    
    // Uloženie odpovede ak je POST požiadavka
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_VALIDATE_INT);
        $answer = $_POST['answer'] ?? null;
        
        if ($questionId && $answer !== null) {
            $questionManager->saveAnswer((int)$attemptId, (int)$questionId, $answer);
            $nextQuestionOrder = $questionOrder + 1;
            $totalQuestions = $questionManager->getQuestionCount((int)$attemptId);
            
            if ($nextQuestionOrder <= $totalQuestions) {
                header("Location: test.php?question=$nextQuestionOrder");
            } else {
                header("Location: results.php");
            }
            exit;
        } else {
            throw new Exception('Invalid question or answer.');
        }
    }
    
    // Získanie otázky
    $question = $questionManager->getQuestion((int)$attemptId, $questionOrder);
    if (!$question) {
        throw new Exception('Question not found.');
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Error in test.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - WebTester</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <style>
        .test-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .question {
            margin-bottom: 1.5rem;
        }
        
        .answers {
            margin-bottom: 1.5rem;
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
    <div class="test-container">
        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
                <p><a href="userArea.php">Return to Dashboard</a></p>
            </div>
        <?php elseif ($question): ?>
            <h1>Question <?= $questionOrder ?></h1>
            
            <div class="question">
                <?= nl2br(htmlspecialchars($question['Question'])) ?>
            </div>
            
            <form method="post">
                <input type="hidden" name="questionId" value="<?= $question['ID'] ?>">
                
                <div class="answers">
                    <?php if ($question['Type'] === 'multiple_choice'): ?>
                        <?php foreach (json_decode($question['Choices'], true) as $choice): ?>
                            <div>
                                <input type="radio" id="answer_<?= htmlspecialchars($choice) ?>" name="answer" value="<?= htmlspecialchars($choice) ?>" required>
                                <label for="answer_<?= htmlspecialchars($choice) ?>"><?= htmlspecialchars($choice) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($question['Type'] === 'text'): ?>
                        <textarea name="answer" rows="4" cols="50" required></textarea>
                    <?php elseif ($question['Type'] === 'true_false'): ?>
                        <div>
                            <input type="radio" id="answer_true" name="answer" value="true" required>
                            <label for="answer_true">True</label>
                        </div>
                        <div>
                            <input type="radio" id="answer_false" name="answer" value="false" required>
                            <label for="answer_false">False</label>
                        </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Next</button>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
    // Časovač (ak je použitý časový limit)
    <?php if (isset($_SESSION['timeLimit']) && $_SESSION['timeLimit'] > 0): ?>
        let timeLeft = <?= $_SESSION['timeLimit'] * 60 - (time() - $_SESSION['testStarted']) ?>;
        
        function startTimer() {
            const timerElement = document.getElementById('timer');
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                alert('Time is up! Submitting your answers...');
                document.forms[0].submit();
            } else {
                timeLeft--;
                setTimeout(startTimer, 1000);
            }
        }
        
        document.addEventListener('DOMContentLoaded', startTimer);
    <?php endif; ?>
    </script>
</body>
</html>