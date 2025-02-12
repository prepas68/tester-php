<?php
declare(strict_types=1);

namespace App\Auth;

use PDO;
use Exception;
use DateTime;

class PasswordReset {
    private PDO $db;
    private const TOKEN_EXPIRY_HOURS = 24;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function createResetToken(string $email): string {
        // Kontrola existencie užívateľa
        $stmt = $this->db->prepare("SELECT ID FROM Users WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('User with this email does not exist.');
        }
        
        try {
            $this->db->beginTransaction();
            
            // Zneplatnenie starých tokenov
            $stmt = $this->db->prepare("
                DELETE FROM PasswordResets 
                WHERE UserID = ? OR (Created < DATE_SUB(NOW(), INTERVAL ? HOUR))
            ");
            $stmt->execute([$user['ID'], self::TOKEN_EXPIRY_HOURS]);
            
            // Vytvorenie nového tokenu
            $token = bin2hex(random_bytes(32));
            $stmt = $this->db->prepare("
                INSERT INTO PasswordResets (UserID, Token, Created) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$user['ID'], password_hash($token, PASSWORD_DEFAULT)]);
            
            $this->db->commit();
            return $token;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception('Failed to create reset token.');
        }
    }
    
    public function validateToken(string $token): bool {
        $stmt = $this->db->prepare("
            SELECT pr.*, u.Email 
            FROM PasswordResets pr
            JOIN Users u ON pr.UserID = u.ID
            WHERE Created > DATE_SUB(NOW(), INTERVAL ? HOUR)
        ");
        $stmt->execute([self::TOKEN_EXPIRY_HOURS]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($token, $row['Token'])) {
                return true;
            }
        }
        
        return false;
    }
    
    public function resetPassword(string $token, string $newPassword): bool {
        try {
            $this->db->beginTransaction();
            
            // Nájdenie platného tokenu
            $stmt = $this->db->prepare("
                SELECT pr.UserID
                FROM PasswordResets pr
                WHERE Created > DATE_SUB(NOW(), INTERVAL ? HOUR)
            ");
            $stmt->execute([self::TOKEN_EXPIRY_HOURS]);
            
            $userId = null;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($token, $row['Token'])) {
                    $userId = $row['UserID'];
                    break;
                }
            }
            
            if (!$userId) {
                throw new Exception('Invalid or expired reset token.');
            }
            
            // Update hesla
            $stmt = $this->db->prepare("
                UPDATE Users 
                SET Password = ? 
                WHERE ID = ?
            ");
            $stmt->execute([
                password_hash($newPassword, PASSWORD_DEFAULT),
                $userId
            ]);
            
            // Vymazanie použitého tokenu
            $stmt = $this->db->prepare("DELETE FROM PasswordResets WHERE UserID = ?");
            $stmt->execute([$userId]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}