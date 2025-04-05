<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/audit.php';

/**
 * Classe para gerenciar tentativas de login
 */
class LoginAttempts {
    // Configurações
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15 minutos em segundos
    
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->initializeTable();
    }
    
    /**
     * Inicializa a tabela de tentativas de login
     */
    private function initializeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            success BOOLEAN DEFAULT FALSE,
            INDEX (username),
            INDEX (ip_address),
            INDEX (attempt_time)
        )";
        
        mysqli_query($this->conn, $sql);
    }
    
    /**
     * Registra uma tentativa de login
     * 
     * @param string $username Nome do usuário
     * @param bool $success Se a tentativa foi bem sucedida
     */
    public function recordAttempt($username, $success = false) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ssi", $username, $ipAddress, $success);
        mysqli_stmt_execute($stmt);
        
        // Registra na auditoria
        $status = $success ? 'bem-sucedida' : 'falha';
        logAction('LOGIN_ATTEMPT', "Tentativa de login $status para usuário: $username de IP: $ipAddress");
    }
    
    /**
     * Verifica se o usuário está bloqueado
     * 
     * @param string $username Nome do usuário
     * @return array Array com status do bloqueio e tempo restante
     */
    public function isUserBlocked($username) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cutoffTime = date('Y-m-d H:i:s', time() - self::LOCKOUT_TIME);
        
        // Verifica tentativas recentes
        $stmt = mysqli_prepare($this->conn,
            "SELECT COUNT(*) as attempts 
             FROM login_attempts 
             WHERE (username = ? OR ip_address = ?)
             AND success = 0 
             AND attempt_time > ?"
        );
        mysqli_stmt_bind_param($stmt, "sss", $username, $ipAddress, $cutoffTime);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['attempts'] >= self::MAX_ATTEMPTS) {
            // Obtém o tempo da última tentativa
            $stmt = mysqli_prepare($this->conn,
                "SELECT attempt_time 
                 FROM login_attempts 
                 WHERE (username = ? OR ip_address = ?)
                 AND success = 0 
                 ORDER BY attempt_time DESC 
                 LIMIT 1"
            );
            mysqli_stmt_bind_param($stmt, "ss", $username, $ipAddress);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $lastAttempt = mysqli_fetch_assoc($result);
            
            $timeRemaining = self::LOCKOUT_TIME - (time() - strtotime($lastAttempt['attempt_time']));
            
            return [
                'blocked' => true,
                'timeRemaining' => max(0, $timeRemaining)
            ];
        }
        
        return [
            'blocked' => false,
            'timeRemaining' => 0
        ];
    }
    
    /**
     * Limpa tentativas antigas de login
     */
    public function cleanOldAttempts() {
        $cutoffTime = date('Y-m-d H:i:s', time() - (self::LOCKOUT_TIME * 2));
        
        $stmt = mysqli_prepare($this->conn,
            "DELETE FROM login_attempts WHERE attempt_time < ?"
        );
        mysqli_stmt_bind_param($stmt, "s", $cutoffTime);
        mysqli_stmt_execute($stmt);
    }
} 