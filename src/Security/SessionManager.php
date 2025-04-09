<?php
namespace Subrider\Security;

class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public function login(int $userId): void {
        $_SESSION['user_id'] = $userId;
    }

    public function logout(): void {
        session_destroy();
    }
} 