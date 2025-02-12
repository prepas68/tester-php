<?php
declare(strict_types=1);

namespace App\Auth;

use PDO;
use Exception;
use DateTime;

class UserManager {
    private PDO $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function createUser(array $data): int {
        $this->validateUserData($data);
        
        try {
            $this->db->beginTransaction();
            
            // Kontrola či užívateľ už neexistuje
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM Users WHERE Username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Username already exists.');
            }
            
            // Vytvorenie užívateľa
            $stmt = $this->db->prepare("
                INSERT INTO Users (
                    Username, 
                    Password, 
                    Email, 
                    FirstName, 
                    LastName, 
                    Level,
                    Created
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['email'],
                $data['firstName'],
                $data['lastName'],
                $data['level'] ?? 'student',
                (new DateTime())->format('Y-m-d H:i:s')
            ]);
            
            $userId = (int)$this->db->lastInsertId();
            
            $this->db->commit();
            return $userId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function updateUser(int $userId, array $data): bool {
        try {
            $this->db->beginTransaction();
            
            $updates = [];
            $params = [];
            
            // Dynamicky vytvoríme UPDATE statement len pre poskytnuté polia
            foreach (['Email', 'FirstName', 'LastName', 'Level'] as $field) {
                if (isset($data[lcfirst($field)])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[lcfirst($field)];
                }
            }
            
            // Ak je poskytnuté nové heslo, pridáme ho do updatu
            if (!empty($data['password'])) {
                $updates[] = "Password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updates)) {
                return false;
            }
            
            $params[] = $userId;
            
            $sql = "UPDATE Users SET " . implode(', ', $updates) . " WHERE ID = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            $this->db->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    private function validateUserData(array $data): void {
        $required = ['username', 'password', 'email', 'firstName', 'lastName'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '$field' is required.");
            }
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }
        
        if (strlen($data['password']) < 8) {
            throw new Exception('Password must be at least 8 characters long.');
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $data['username'])) {
            throw new Exception('Username can only contain letters, numbers, underscores and dashes.');
        }
    }
}