<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/src/Auth/Auth.php';

session_start();

// Kontrola prihlásenia
$auth = new \App\Auth\Auth(Database::getInstance()->getConnection());
if (!$auth->isLoggedIn()) {
    header('Location: userLogin.php');
    exit;
}

// Získanie informácií o užívateľovi
$user = $auth->getCurrentUser();

// Získanie testov užívateľa
try {
    $db = Database::getInstance()->getConnection();
    
    // Získanie aktívnych testov
    $stmt = $db->prepare("
        SELECT t.*, s.Name as SubjectName,
               (SELECT COUNT(*) FROM Questions q WHERE q.TestID = t.ID) as QuestionCount
        FROM Tests t
        LEFT JOIN Subjects s ON t.Subject = s.ID
        WHERE t.Enabled = 1
        AND (
            t.Browseable = 1
            OR EXISTS (
                SELECT 1 FROM TestAccess ta 
                WHERE ta.TestID = t.ID 
                AND ta.UserID = ?
            )
        )
        ORDER BY t.TestName
    ");
    $stmt->execute([$user['ID']]);
    $availableTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Získanie výsledkov testov
    $stmt = $db->prepare("
        SELECT tr.*, t.TestName, t.PassingScore
        FROM TestResults tr
        JOIN Tests t ON tr.TestID = t.ID
        WHERE tr.UserID = ?
        ORDER BY tr.Completed DESC
    ");
    $stmt->execute([$user['ID']]);
    $testResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Error in userArea: " . $e->getMessage());
    $error = 'An error occurred while loading your data.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - WebTester</title>
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .test-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .test-list th,
        .test-list td {
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .test-list th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: left;
        }
        
        .test-list tr:hover {
            background: #f8f9fa;
        }
        
        .score {
            font-weight: bold;
        }
        
        .score.passing {
            color: #28a745;
        }
        
        .score.failing {
            color: #dc3545;
        }
        
        .btn {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($user['FirstName'], 0, 1)) ?>
                </div>
                <div>
                    <h2><?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></h2>
                    <p><?= htmlspecialchars($user['Email']) ?></p>
                </div>
            </div>
            <div>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Available Tests</h3>
                <?php if (!empty($availableTests)): ?>
                    <table class="test-list">
                        <thead>
                            <tr>
                                <th>Test Name</th>
                                <th>Subject</th>
                                <th>Questions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($availableTests as $test): ?>
                                <tr>
                                    <td><?= htmlspecialchars($test['TestName']) ?></td>
                                    <td><?= htmlspecialchars($test['SubjectName']) ?></td>
                                    <td><?= $test['QuestionCount'] ?></td>
                                    <td>
                                        <a href="startTest.php?id=<?= $test['ID'] ?>" 
                                           class="btn btn-primary">
                                            Start Test
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tests available at the moment.</p>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <h3>Test Results</h3>
                <?php if (!empty($testResults)): ?>
                    <table class="test-list">
                        <thead>
                            <tr>
                                <th>Test Name</th>
                                <th>Score</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testResults as $result): ?>
                                <tr>
                                    <td><?= htmlspecialchars($result['TestName']) ?></td>
                                    <td class="score <?= $result['Score'] >= $result['PassingScore'] ? 'passing' : 'failing' ?>">
                                        <?= $result['Score'] ?>%
                                    </td>
                                    <td><?= (new DateTime($result['Completed']))->format('Y-m-d H:i') ?></td>
                                    <td>
                                        <?= $result['Score'] >= $result['PassingScore'] ? 'Passed' : 'Failed' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No test results yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pridanie potvrdenia pred začatím testu
        document.querySelectorAll('a[href^="startTest.php"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to start this test?')) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</body>
</html>