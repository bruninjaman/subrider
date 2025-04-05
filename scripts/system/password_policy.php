<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/audit.php';

/**
 * Classe para gerenciar política de senhas
 */
class PasswordPolicy {
    // Configurações da política de senha
    const MIN_LENGTH = 8;
    const REQUIRE_UPPERCASE = true;
    const REQUIRE_LOWERCASE = true;
    const REQUIRE_NUMBERS = true;
    const REQUIRE_SPECIAL = true;
    const MAX_AGE_DAYS = 90;
    const HISTORY_SIZE = 5;
    
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->initializeTables();
    }
    
    /**
     * Inicializa as tabelas necessárias
     */
    private function initializeTables() {
        // Tabela de histórico de senhas
        $sql = "CREATE TABLE IF NOT EXISTS password_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id VARCHAR(50) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (user_id)
        )";
        mysqli_query($this->conn, $sql);
        
        // Adiciona coluna de última alteração de senha na tabela de usuários se não existir
        $sql = "SHOW COLUMNS FROM login LIKE 'password_changed_at'";
        $result = mysqli_query($this->conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            $sql = "ALTER TABLE login ADD COLUMN password_changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            mysqli_query($this->conn, $sql);
        }
    }
    
    /**
     * Valida se uma senha atende aos requisitos de segurança
     * 
     * @param string $password Senha a ser validada
     * @return array Array com resultado da validação e mensagens de erro
     */
    public function validatePassword($password) {
        $errors = [];
        
        // Verifica comprimento mínimo
        if (strlen($password) < self::MIN_LENGTH) {
            $errors[] = "A senha deve ter pelo menos " . self::MIN_LENGTH . " caracteres";
        }
        
        // Verifica letra maiúscula
        if (self::REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "A senha deve conter pelo menos uma letra maiúscula";
        }
        
        // Verifica letra minúscula
        if (self::REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
            $errors[] = "A senha deve conter pelo menos uma letra minúscula";
        }
        
        // Verifica números
        if (self::REQUIRE_NUMBERS && !preg_match('/[0-9]/', $password)) {
            $errors[] = "A senha deve conter pelo menos um número";
        }
        
        // Verifica caracteres especiais
        if (self::REQUIRE_SPECIAL && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "A senha deve conter pelo menos um caractere especial";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Verifica se a senha precisa ser alterada
     * 
     * @param string $userId ID do usuário
     * @return bool True se a senha precisa ser alterada
     */
    public function passwordNeedsChange($userId) {
        $stmt = mysqli_prepare($this->conn, 
            "SELECT password_changed_at FROM login WHERE username = ?"
        );
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $changedAt = strtotime($row['password_changed_at']);
            $maxAge = time() - (self::MAX_AGE_DAYS * 24 * 60 * 60);
            return $changedAt < $maxAge;
        }
        
        return false;
    }
    
    /**
     * Verifica se a senha já foi usada recentemente
     * 
     * @param string $userId ID do usuário
     * @param string $password Nova senha
     * @return bool True se a senha já foi usada
     */
    public function isPasswordReused($userId, $password) {
        $stmt = mysqli_prepare($this->conn,
            "SELECT password_hash FROM password_history 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?"
        );
        mysqli_stmt_bind_param($stmt, "si", $userId, self::HISTORY_SIZE);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password_hash'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Registra uma nova senha no histórico
     * 
     * @param string $userId ID do usuário
     * @param string $password Nova senha
     */
    public function recordPasswordHistory($userId, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Registra no histórico
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO password_history (user_id, password_hash) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ss", $userId, $passwordHash);
        mysqli_stmt_execute($stmt);
        
        // Atualiza data de alteração
        $stmt = mysqli_prepare($this->conn,
            "UPDATE login SET password_changed_at = CURRENT_TIMESTAMP WHERE username = ?"
        );
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        
        // Remove histórico antigo
        $stmt = mysqli_prepare($this->conn,
            "DELETE FROM password_history 
             WHERE user_id = ? 
             AND id NOT IN (
                 SELECT id FROM (
                     SELECT id FROM password_history 
                     WHERE user_id = ? 
                     ORDER BY created_at DESC 
                     LIMIT ?
                 ) tmp
             )"
        );
        mysqli_stmt_bind_param($stmt, "ssi", $userId, $userId, self::HISTORY_SIZE);
        mysqli_stmt_execute($stmt);
        
        // Registra na auditoria
        logAction('PASSWORD_CHANGE', "Senha alterada para o usuário: $userId");
    }
} 