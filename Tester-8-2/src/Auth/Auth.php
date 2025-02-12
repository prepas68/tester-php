<?php
declare(strict_types=1);

namespace App\Auth;

use PDO;
use Exception;

class Auth {
    private PDO $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function login(string $username, string $password): bool {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['loggedInTest'] = "1";
            $_SESSION['userId'] = $user['ID'];
            $_SESSION['userName'] = $user['Username'];
            $_SESSION['userLevel'] = $user['Level'];
            return true;
        }
        
        return false;
    }
    
    public function logout(): void {
        $_SESSION = [];
        session_destroy();
    }
    
    public function isLoggedIn(): bool {
        return isset($_SESSION['loggedInTest']) && $_SESSION['loggedInTest'] === "1";
    }
    
    public function getCurrentUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE ID = ?");
        $stmt->execute([$_SESSION['userId']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}