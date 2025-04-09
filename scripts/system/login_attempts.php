<?php
require_once __DIR__ . '/../../config/init.php'; // Assumindo que init.php carrega o necessário

use PDO;
use PDOException;

/**
 * Classe para gerenciar tentativas de login
 */
class LoginAttempts {
    // Configurações
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15 minutos em segundos
    
    /** @var PDO */
    private $conn;
    
    /**
     * @param PDO $conn A conexão PDO com o banco de dados.
     */
    public function __construct(PDO $conn) {
        $this->conn = $conn;
        $this->initializeTable();
    }
    
    /**
     * Inicializa a tabela de tentativas de login
     */
    private function initializeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL, -- Aumentar tamanho se necessário
            ip_address VARCHAR(45) NOT NULL,
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            success BOOLEAN DEFAULT FALSE,
            INDEX idx_username (username),
            INDEX idx_ip_address (ip_address),
            INDEX idx_attempt_time (attempt_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao inicializar tabela login_attempts: " . $e->getMessage());
            // Considerar lançar a exceção
        }
    }
    
    /**
     * Registra uma tentativa de login
     * 
     * @param string $username Nome do usuário
     * @param bool $success Se a tentativa foi bem sucedida
     * @return bool True se o registro foi bem sucedido, False caso contrário
     */
    public function recordAttempt(string $username, bool $success = false): bool {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        try {
            $sql = "INSERT INTO login_attempts (username, ip_address, success) VALUES (:username, :ip_address, :success)";
            $stmt = $this->conn->prepare($sql);

            // Bind dos parâmetros
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
            $stmt->bindParam(':success', $success, PDO::PARAM_BOOL);

            $result = $stmt->execute();
            
            // REMOVIDO: O log de auditoria é feito agora no script log-in.php
            // $status = $success ? 'bem-sucedida' : 'falha';
            // logAction('LOGIN_ATTEMPT', "Tentativa de login $status para usuário: $username de IP: $ipAddress");

            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao registrar tentativa de login (PDO): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se o usuário ou IP está bloqueado devido a tentativas falhas recentes
     * 
     * @param string $username Nome do usuário
     * @return array Array com status do bloqueio ['blocked' => bool, 'timeRemaining' => int]
     */
    public function isUserBlocked(string $username): array {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cutoffTimestamp = time() - self::LOCKOUT_TIME;
        $cutoffTime = date('Y-m-d H:i:s', $cutoffTimestamp);
        
        try {
            // Conta tentativas falhas recentes do usuário OU do IP
            $sqlAttempts = "SELECT COUNT(*) as attempts, MAX(attempt_time) as last_attempt_time
                           FROM login_attempts
                           WHERE (username = :username OR ip_address = :ip_address)
                           AND success = 0
                           AND attempt_time > :cutoff_time";

            $stmtAttempts = $this->conn->prepare($sqlAttempts);
            $stmtAttempts->bindParam(':username', $username, PDO::PARAM_STR);
            $stmtAttempts->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
            $stmtAttempts->bindParam(':cutoff_time', $cutoffTime, PDO::PARAM_STR);
            $stmtAttempts->execute();
            $result = $stmtAttempts->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['attempts'] >= self::MAX_ATTEMPTS) {
                $lastAttemptTime = strtotime($result['last_attempt_time']);
                $timeRemaining = self::LOCKOUT_TIME - (time() - $lastAttemptTime);

                return [
                    'blocked' => true,
                    'timeRemaining' => max(0, $timeRemaining) // Garante que não seja negativo
                ];
            }

            return [
                'blocked' => false,
                'timeRemaining' => 0
            ];

        } catch (PDOException $e) {
            error_log("Erro ao verificar bloqueio de login (PDO): " . $e->getMessage());
            // Em caso de erro, é mais seguro não bloquear, mas logar
            return [
                'blocked' => false,
                'timeRemaining' => 0
            ];
        }
    }
    
    /**
     * Limpa tentativas antigas de login (mais antigas que o dobro do tempo de bloqueio)
     * @return bool True se a limpeza foi bem sucedida, False caso contrário
     */
    public function cleanOldAttempts(): bool {
        // Limpa registros mais antigos que 2x o tempo de lockout para evitar acúmulo
        $cutoffTimestamp = time() - (self::LOCKOUT_TIME * 2);
        $cutoffTime = date('Y-m-d H:i:s', $cutoffTimestamp);

        try {
            $sql = "DELETE FROM login_attempts WHERE attempt_time < :cutoff_time";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cutoff_time', $cutoffTime, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao limpar tentativas antigas de login (PDO): " . $e->getMessage());
            return false;
        }
    }
} 