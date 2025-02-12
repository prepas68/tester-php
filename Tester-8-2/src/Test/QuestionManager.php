<?php
declare(strict_types=1);

namespace App\Test;

use PDO;
use Exception;

class QuestionManager {
    private PDO $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function getQuestion(int $attemptId, int $questionOrder): ?array {
        $stmt = $this->db->prepare("
            SELECT q.*, aq.QuestionOrder 
            FROM AttemptQuestions aq
            JOIN Questions q ON aq.QuestionID = q.ID
            WHERE aq.AttemptID = ?
            AND aq.QuestionOrder = ?
        ");
        $stmt->execute([$attemptId, $questionOrder]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function saveAnswer(int $attemptId, int $questionId, $answer): bool {
        $stmt = $this->db->prepare("
            INSERT INTO AttemptAnswers (AttemptID, QuestionID, Answer) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE Answer = VALUES(Answer)
        ");
        return $stmt->execute([$attemptId, $questionId, $answer]);
    }
    
    public function getQuestionCount(int $attemptId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM AttemptQuestions 
            WHERE AttemptID = ?
        ");
        $stmt->execute([$attemptId]);
        return (int)$stmt->fetchColumn();
    }
}