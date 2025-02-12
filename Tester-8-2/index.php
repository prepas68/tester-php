<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/timerhead.php';
require_once __DIR__ . '/includes/includes.php';
require_once __DIR__ . '/includes/nocache.php';
require_once __DIR__ . '/includes/validation.php';

// Redirect if already logged in
if (isset($_SESSION['loggedInTest']) && $_SESSION['loggedInTest'] === "1") {
    header("Location: userArea.php");
    exit;
}

// Get database connection
try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("System error. Please try again later.");
}

// Get preferences
try {
    $stmt = $db->query("SELECT * FROM Preferences LIMIT 1");
    $prefs = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to get preferences: " . $e->getMessage());
    $prefs = ['AllowBrowse' => false, 'WelcomeMessage' => 'Welcome to WebTester'];
}

// Check session
$sessionValid = false;
if (defined('IPSESSIONS') && IPSESSIONS) {
    $stmt = $db->prepare("SELECT * FROM Sessions WHERE IP = ?");
    $stmt->execute([$_SERVER['REMOTE_ADDR']]);
} else {
    $stmt = $db->prepare("SELECT * FROM Sessions WHERE ID = ?");
    $stmt->execute([session_id()]);
}
$sessionData = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(defined('TITLE') ? TITLE : 'WebTester') ?></title>
    
    <link href="includes/wtstyle.css" rel="stylesheet" type="text/css">
    
    <style>
        .next { 
            margin-top: 1.6em;
            float: right;
        }
        
        .box {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .test-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .test-list th {
            background-color: #C8D8FF;
            padding: 8px;
            text-align: left;
        }
        
        .test-list td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .test-list tr:hover {
            background-color: #FFFF99;
        }
        
        <?php if (defined('DISABLE_PRINT') && DISABLE_PRINT): ?>
        @media print {
            body { display: none; }
        }
        <?php endif; ?>
    </style>
</head>

<body class="bg-<?= htmlspecialchars(defined('BGCOLOR') ? BGCOLOR : 'white') ?>">
    <?php include "./includes/top.php"; ?>
    
    <div class="container">
        <header>
            <img src="images/webtestertop.gif" 
                 width="<?= defined('LOGOW') ? LOGOW : '337' ?>" 
                 height="<?= defined('LOGOH') ? LOGOH : '75' ?>"
                 alt="WebTester Logo">
                 
            <?php if ($sessionData): ?>
            <div class="box">
                <div class="user-info">
                    <?= htmlspecialchars($sessionData['FirstName'] . " " . $sessionData['LastName']) ?>
                    
                    <?php if (!empty($sessionData['TestName'])): ?>
                        <div class="test-info">
                            <?= htmlspecialchars($sessionData['TestName']) ?>
                            <div id="countdowncontainer"></div>
                            
                            <?php if ($sessionData['AllowQuit'] && !$sessionData['review'] && !($quit ?? false)): ?>
                                <div id="quit">
                                    <a href="./quitTest.php" class="btn btn-warning">Quit Test</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </header>

        <main>
            <?php if ($prefs['AllowBrowse']): ?>
                <?= $prefs['WelcomeMessage'] ?>
                
                <?php
                try {
                    $where = isset($_REQUEST['subject']) 
                        ? "WHERE Enabled = 1 AND Subject = ?" 
                        : "WHERE Enabled = 1 AND Browseable = 1";
                    
                    $stmt = $db->prepare("
                        SELECT t.*, s.Name as SubjectName 
                        FROM Tests t 
                        LEFT JOIN Subjects s ON t.Subject = s.ID 
                        $where 
                        ORDER BY TestName
                    ");
                    
                    if (isset($_REQUEST['subject'])) {
                        $stmt->execute([htmlspecialchars($_REQUEST['subject'])]);
                    } else {
                        $stmt->execute();
                    }
                    
                    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <table class="test-list">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tests as $test): ?>
                            <tr>
                                <td>
                                    <a href="go.php?testID=<?= htmlspecialchars((string)$test['ID']) ?>">
                                        <?= htmlspecialchars($test['TestName']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($test['SubjectName'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                <?php
                } catch (PDOException $e) {
                    error_log("Database error: " . $e->getMessage());
                    echo "<p class='error'>Error loading tests. Please try again later.</p>";
                }
                ?>
                
            <?php else: ?>
                <p class="alert alert-info">Browsing is not enabled.</p>
            <?php endif; ?>
        </main>

        <footer>
            <hr>
            <?php include "./includes/copyright.php" ?>
        </footer>
    </div>

    <script>
        // Moderný JavaScript pre zvýraznenie riadkov v tabuľke
        document.querySelectorAll('.test-list tbody tr').forEach(row => {
            row.addEventListener('mouseenter', () => row.classList.add('highlight'));
            row.addEventListener('mouseleave', () => row.classList.remove('highlight'));
        });
    </script>
</body>
</html>