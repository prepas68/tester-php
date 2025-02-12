<?php
declare(strict_types=1);

namespace App\Test;

use PDO;
use DateTime;
use Exception;

class TestManager {
    private PDO $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function startTest(int $testId, int $userId): array {
        try {
            $this->db->beginTransaction();
            
            // Kontrola či test existuje a je dostupný
            $stmt = $this->db->prepare("
                SELECT t.*, s.Name as SubjectName
                FROM Tests t
                LEFT JOIN Subjects s ON t.Subject = s.ID
                WHERE t.ID = ? AND t.Enabled = 1
            ");
            $stmt->execute([$testId]);
            $test = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$test) {
                throw new Exception('Test not found or not available.');
            }
            
            // Kontrola či užívateľ má prístup k testu
            if (!$test['Browseable']) {
                $stmt = $this->db->prepare("
                    SELECT 1 FROM TestAccess 
                    WHERE TestID = ? AND UserID = ?
                ");
                $stmt->execute([$testId, $userId]);
                if (!$stmt->fetch()) {
                    throw new Exception('You do not have access to this test.');
                }
            }
            
            // Kontrola časového limitu medzi pokusmi
            if ($test['RetakeAfter'] > 0) {
                $stmt = $this->db->prepare("
                    SELECT Completed 
                    FROM TestResults 
                    WHERE TestID = ? AND UserID = ?
                    ORDER BY Completed DESC 
                    LIMIT 1
                ");
                $stmt->execute([$testId, $userId]);
                if ($lastAttempt = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $lastAttemptDate = new DateTime($lastAttempt['Completed']);
                    $waitUntil = $lastAttemptDate->modify("+{$test['RetakeAfter']} hours");
                    $now = new DateTime();
                    
                    if ($waitUntil > $now) {
                        throw new Exception("You must wait until {$waitUntil->format('Y-m-d H:i:s')} to retake this test.");
                    }
                }
            }
            
            // Vytvoriť nový pokus o test
            $stmt = $this->db->prepare("
                INSERT INTO TestAttempts (
                    TestID, 
                    UserID, 
                    Started, 
                    TimeLimit,
                    QuestionsLimit
                ) VALUES (?, ?, NOW(), ?, ?)
            ");
            $stmt->execute([
                $testId,
                $userId,
                $test['TimeLimit'],
                $test['QuestionsPerTest']
            ]);
            $attemptId = (int)$this->db->lastInsertId();
            
            // Vybrať náhodné otázky pre test
            $stmt = $this->db->prepare("
                SELECT ID, Question, Type, Points
                FROM Questions
                WHERE TestID = ? AND Enabled = 1
                ORDER BY RAND()
                LIMIT ?
            ");
            $stmt->execute([$testId, $test['QuestionsPerTest']]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Priradiť otázky k pokusu
            $stmt = $this->db->prepare("
                INSERT INTO AttemptQuestions (
                    AttemptID,
                    QuestionID,
                    QuestionOrder
                ) VALUES (?, ?, ?)
            ");
            
            foreach ($questions as $order => $question) {
                $stmt->execute([
                    $attemptId,
                    $question['ID'],
                    $order + 1
                ]);
            }
            
            $this->db->commit();
            
            return [
                'attemptId' => $attemptId,
                'test' => $test,
                'questions' => $questions
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}