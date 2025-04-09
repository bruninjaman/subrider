<?php
require_once __DIR__ . '/../../config/config.php';
/**
 * Sistema de auditoria para registrar ações no sistema
 */
class AuditSystem {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->initializeTable();
    }
    
    /**
     * Inicializa a tabela de auditoria se não existir
     */
    private function initializeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS audit_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            action_type VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            user_id VARCHAR(50),
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        mysqli_query($this->conn, $sql);
    }
    
    /**
     * Registra uma ação no log de auditoria
     * 
     * @param string $actionType Tipo da ação (LOGIN, LOGOUT, CREATE, UPDATE, DELETE, etc)
     * @param string $description Descrição detalhada da ação
     * @return bool True se o registro foi bem sucedido, False caso contrário
     */
    public function logAction($actionType, $description) {
        try {
            // Obtém informações da sessão e request
            $userId = isset($_SESSION['user']) ? $_SESSION['user'] : null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            // Prepara e executa a query
            $stmt = mysqli_prepare($this->conn, 
                "INSERT INTO audit_log (action_type, description, user_id, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            
            mysqli_stmt_bind_param($stmt, "sssss", 
                $actionType,
                $description,
                $userId,
                $ipAddress,
                $userAgent
            );
            
            return mysqli_stmt_execute($stmt);
            
        } catch (Exception $e) {
            error_log("Erro ao registrar auditoria: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca registros de auditoria com filtros
     * 
     * @param array $filters Array associativo com filtros (action_type, user_id, date_from, date_to)
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array Registros encontrados
     */
    public function searchLogs($filters = [], $limit = 50, $offset = 0) {
        try {
            $where = [];
            $params = [];
            $types = "";
            
            // Monta as condições WHERE baseado nos filtros
            if (!empty($filters['action_type'])) {
                $where[] = "action_type = ?";
                $params[] = $filters['action_type'];
                $types .= "s";
            }
            
            if (!empty($filters['user_id'])) {
                $where[] = "user_id = ?";
                $params[] = $filters['user_id'];
                $types .= "s";
            }
            
            if (!empty($filters['date_from'])) {
                $where[] = "created_at >= ?";
                $params[] = $filters['date_from'];
                $types .= "s";
            }
            
            if (!empty($filters['date_to'])) {
                $where[] = "created_at <= ?";
                $params[] = $filters['date_to'];
                $types .= "s";
            }
            
            // Monta a query
            $sql = "SELECT * FROM audit_log";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            
            // Adiciona limit e offset aos parâmetros
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";
            
            // Prepara e executa a query
            $stmt = mysqli_prepare($this->conn, $sql);
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $logs = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $logs[] = $row;
            }
            
            return $logs;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar logs de auditoria: " . $e->getMessage());
            return [];
        }
    }
}

// Cria instância global do sistema de auditoria
$auditSystem = new AuditSystem($conn);

/**
 * Função helper para registrar ações
 */
function logAction($actionType, $description) {
    global $auditSystem;
    return $auditSystem->logAction($actionType, $description);
} 