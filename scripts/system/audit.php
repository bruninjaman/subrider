<?php
require_once __DIR__ . '/../../config/init.php';

use PDO;
use PDOException;

/**
 * Sistema de auditoria para registrar ações no sistema
 */
class AuditSystem {
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
     * Inicializa a tabela de auditoria se não existir
     */
    private function initializeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS audit_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            action_type VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            user_id VARCHAR(50) DEFAULT NULL, -- Permitir NULL para ações não autenticadas
            ip_address VARCHAR(45) DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"; // Especificar engine e charset

        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Erro ao inicializar tabela de auditoria: " . $e->getMessage());
            // Considerar lançar a exceção ou retornar um erro
        }
    }

    /**
     * Registra uma ação no log de auditoria
     *
     * @param string $actionType Tipo da ação (LOGIN, LOGOUT, CREATE, UPDATE, DELETE, etc)
     * @param string $description Descrição detalhada da ação
     * @return bool True se o registro foi bem sucedido, False caso contrário
     */
    public function logAction(string $actionType, string $description): bool {
        try {
            // Obtém informações da sessão e request (com segurança)
            // Usar null coalescing operator ?? para evitar warnings/errors se a chave não existir
            $userId = $_SESSION['user_id'] ?? null; // Assumindo que 'user_id' é armazenado na sessão
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            // Prepara a query usando PDO
            $sql = "INSERT INTO audit_log (action_type, description, user_id, ip_address, user_agent)
                    VALUES (:action_type, :description, :user_id, :ip_address, :user_agent)";
            $stmt = $this->conn->prepare($sql);

            // Bind dos parâmetros
            $stmt->bindParam(':action_type', $actionType, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR); // Ajustar tipo se user_id for INT
            $stmt->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
            $stmt->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);

            // Executa a query
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erro ao registrar auditoria (PDO): " . $e->getMessage());
            return false;
        } catch (Exception $e) { // Captura outras exceções potenciais
            error_log("Erro geral ao registrar auditoria: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca registros de auditoria com filtros
     *
     * @param array $filters Array associativo com filtros (action_type, user_id, date_from, date_to)
     * @param int $limit Limite de registros (default 50)
     * @param int $offset Offset para paginação (default 0)
     * @return array Registros encontrados ou array vazio em caso de erro
     */
    public function searchLogs(array $filters = [], int $limit = 50, int $offset = 0): array {
        try {
            $where = [];
            $params = [];

            // Monta as condições WHERE baseado nos filtros
            if (!empty($filters['action_type'])) {
                $where[] = "action_type = :action_type";
                $params[':action_type'] = $filters['action_type'];
            }

            if (!empty($filters['user_id'])) {
                $where[] = "user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }

            if (!empty($filters['date_from'])) {
                $where[] = "created_at >= :date_from";
                // Adicionar hora inicial para incluir o dia todo
                $params[':date_from'] = $filters['date_from'] . ' 00:00:00'; 
            }

            if (!empty($filters['date_to'])) {
                $where[] = "created_at <= :date_to";
                // Adicionar hora final para incluir o dia todo
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59'; 
            }

            // Monta a query SQL base
            $sql = "SELECT * FROM audit_log";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

            // Adiciona limit e offset aos parâmetros
            // Bind como INT
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;

            // Prepara a query
            $stmt = $this->conn->prepare($sql);

            // Bind dos parâmetros (incluindo limit/offset)
            // É necessário bindValue para limit/offset pois são inteiros
            foreach ($params as $key => &$val) { // Usar referência para bindParam/bindValue
                if ($key === ':limit' || $key === ':offset') {
                   $stmt->bindValue($key, (int)$val, PDO::PARAM_INT); 
                } else {
                   $stmt->bindParam($key, $val); // Outros são strings ou definidos pelo tipo no filtro
                }
            }
            unset($val); // Desfaz a referência após o loop

            // Executa e busca os resultados
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $logs ?? []; // Retorna array vazio se fetchAll falhar ou não retornar nada

        } catch (PDOException $e) {
            error_log("Erro ao buscar logs de auditoria (PDO): " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            error_log("Erro geral ao buscar logs de auditoria: " . $e->getMessage());
            return [];
        }
    }
} 