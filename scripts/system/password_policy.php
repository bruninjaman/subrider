<?php
namespace Subrider\Security;

require_once __DIR__ . '/../../config/init.php';

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
    
    /** @var \PDO */
    private $conn;
    
    /**
     * @param \PDO $conn A conexão PDO com o banco de dados.
     */
    public function __construct(\PDO $conn) {
        $this->conn = $conn;
        $this->initializeTables();
    }
    
    /**
     * Inicializa as tabelas necessárias (histórico de senhas)
     * e garante que a coluna password_changed_at exista na tabela login.
     */
    private function initializeTables() {
        try {
            // Tabela de histórico de senhas
            $sqlHistory = "CREATE TABLE IF NOT EXISTS password_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(255) NOT NULL, -- Ajustar tipo se user_id for INT
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->conn->exec($sqlHistory);

            // Verifica se a coluna password_changed_at existe na tabela login
            // Usando PDO::query e fetchColumn para verificar
            $dbName = $this->conn->query('select database()')->fetchColumn();
            $stmtCheckColumn = $this->conn->prepare("
                SELECT COUNT(*)
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = :dbName
                AND TABLE_NAME = 'login'
                AND COLUMN_NAME = 'password_changed_at'
            ");
            $stmtCheckColumn->bindParam(':dbName', $dbName, \PDO::PARAM_STR);
            $stmtCheckColumn->execute();
            $columnExists = $stmtCheckColumn->fetchColumn();

            if ($columnExists == 0) {
                // Adiciona a coluna se não existir
                $sqlAlter = "ALTER TABLE login 
                             ADD COLUMN password_changed_at TIMESTAMP 
                             DEFAULT CURRENT_TIMESTAMP 
                             ON UPDATE CURRENT_TIMESTAMP"; // Atualiza automaticamente na mudança
                $this->conn->exec($sqlAlter);
                error_log("Coluna 'password_changed_at' adicionada à tabela 'login'.");
            }
        } catch (\PDOException $e) {
            error_log("Erro ao inicializar tabelas de política de senha (PDO): " . $e->getMessage());
            // Considerar lançar a exceção
        }
    }
    
    /**
     * Valida se uma senha atende aos requisitos de segurança
     * 
     * @param string $password Senha a ser validada
     * @return array ['valid' => bool, 'errors' => string[]] Resultado da validação
     */
    public function validatePassword(string $password): array {
        $errors = [];
        
        if (strlen($password) < self::MIN_LENGTH) {
            $errors[] = "A senha deve ter pelo menos " . self::MIN_LENGTH . " caracteres.";
        }
        if (self::REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "A senha deve conter pelo menos uma letra maiúscula.";
        }
        if (self::REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
            $errors[] = "A senha deve conter pelo menos uma letra minúscula.";
        }
        if (self::REQUIRE_NUMBERS && !preg_match('/[0-9]/', $password)) {
            $errors[] = "A senha deve conter pelo menos um número.";
        }
        // Regex ajustado para caracteres especiais comuns
        if (self::REQUIRE_SPECIAL && !preg_match('/[!@#$%^&*(),.?":{}|<>_-]/', $password)) {
            $errors[] = "A senha deve conter pelo menos um caractere especial (ex: !@#$%^&*).";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Verifica se a senha do usuário expirou (precisa ser alterada)
     * 
     * @param string $userId ID do usuário (geralmente o username ou ID numérico)
     * @return bool True se a senha precisa ser alterada, False caso contrário ou erro
     */
    public function passwordNeedsChange(string $userId): bool {
        // Se MAX_AGE_DAYS for 0 ou negativo, a expiração está desativada
        if (self::MAX_AGE_DAYS <= 0) {
            return false;
        }

        try {
            // Assumindo que a coluna de identificação na tabela login é 'username' ou 'id'
            // Ajuste :user_id_column se for diferente (ex: id)
            $sql = "SELECT password_changed_at FROM login WHERE username = :user_id"; 
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result && isset($result['password_changed_at'])) {
                $changedAt = strtotime($result['password_changed_at']);
                $maxAgeSeconds = self::MAX_AGE_DAYS * 24 * 60 * 60;
                $expiryTimestamp = $changedAt + $maxAgeSeconds;
                return time() > $expiryTimestamp;
            }
            
            // Se não encontrar o usuário ou a data, considera que não precisa (ou logar erro)
            return false; 
        } catch (\PDOException $e) {
            error_log("Erro ao verificar expiração de senha (PDO) para usuário '$userId': " . $e->getMessage());
            return false; // Evita forçar troca em caso de erro
        }
    }
    
    /**
     * Verifica se a nova senha já foi usada recentemente (está no histórico)
     * 
     * @param string $userId ID do usuário
     * @param string $newPassword Nova senha (em texto plano)
     * @return bool True se a senha foi reutilizada, False caso contrário ou erro
     */
    public function isPasswordReused(string $userId, string $newPassword): bool {
        // Se HISTORY_SIZE for 0 ou negativo, a verificação está desativada
        if (self::HISTORY_SIZE <= 0) {
            return false;
        }
        
        try {
            $sql = "SELECT password_hash FROM password_history
                    WHERE user_id = :user_id
                    ORDER BY created_at DESC
                    LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_STR);
            $stmt->bindValue(':limit', self::HISTORY_SIZE, \PDO::PARAM_INT);
            $stmt->execute();
            $history = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($history as $row) {
                if (password_verify($newPassword, $row['password_hash'])) {
                    return true; // Senha encontrada no histórico
                }
            }

            return false; // Senha não encontrada no histórico
        } catch (\PDOException $e) {
            error_log("Erro ao verificar reutilização de senha (PDO) para usuário '$userId': " . $e->getMessage());
            return false; // Evita bloquear em caso de erro
        }
    }
    
    /**
     * Atualiza a senha do usuário, registra no histórico e limpa entradas antigas.
     * Assume que a nova senha já foi validada pela política.
     * 
     * @param string $userId ID do usuário
     * @param string $newPassword Nova senha (texto plano)
     * @return bool True se a operação foi bem sucedida, False caso contrário
     */
    public function updateUserPassword(string $userId, string $newPassword): bool {
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            $this->conn->beginTransaction();

            // 1. Atualiza a senha na tabela principal (login)
            //    A coluna password_changed_at será atualizada automaticamente pelo ON UPDATE CURRENT_TIMESTAMP
            $sql_update_login = "UPDATE login SET password = :password WHERE username = :user_id"; // Ajuste WHERE se usar ID
            $stmt_update = $this->conn->prepare($sql_update_login);
            $stmt_update->bindParam(':password', $newPasswordHash, \PDO::PARAM_STR);
            $stmt_update->bindParam(':user_id', $userId, \PDO::PARAM_STR);
            $stmt_update->execute();

            // Verifica se alguma linha foi afetada (usuário existe?)
            if ($stmt_update->rowCount() === 0) {
                throw new \Exception("Usuário '$userId' não encontrado para atualização de senha.");
            }

            // 2. Insere a nova senha no histórico
            if (self::HISTORY_SIZE > 0) {
                $sql_insert_history = "INSERT INTO password_history (user_id, password_hash) VALUES (:user_id, :password_hash)";
                $stmt_history = $this->conn->prepare($sql_insert_history);
                $stmt_history->bindParam(':user_id', $userId, \PDO::PARAM_STR);
                $stmt_history->bindParam(':password_hash', $newPasswordHash, \PDO::PARAM_STR);
                $stmt_history->execute();
    
                // 3. Remove histórico antigo (mantém apenas os HISTORY_SIZE mais recentes)
                //    Esta query pode ser complexa ou ineficiente em alguns DBs. Alternativa: delete por data.
                $sql_delete_old = "DELETE ph
                                   FROM password_history ph
                                   LEFT JOIN (
                                       SELECT id
                                       FROM password_history
                                       WHERE user_id = :user_id_sub
                                       ORDER BY created_at DESC
                                       LIMIT :limit
                                   ) keep ON ph.id = keep.id
                                   WHERE ph.user_id = :user_id AND keep.id IS NULL";
                $stmt_delete = $this->conn->prepare($sql_delete_old);
                $stmt_delete->bindParam(':user_id_sub', $userId, \PDO::PARAM_STR);
                $stmt_delete->bindValue(':limit', self::HISTORY_SIZE, \PDO::PARAM_INT);
                $stmt_delete->bindParam(':user_id', $userId, \PDO::PARAM_STR);
                $stmt_delete->execute();
            }
            
            // Se tudo deu certo, commita a transação
            $this->conn->commit();
            
            // REMOVIDO: Log de auditoria deve ser feito externamente
            // logAction('PASSWORD_CHANGE', "Senha alterada para o usuário: $userId");
            
            return true;

        } catch (\PDOException $e) {
            $this->conn->rollBack(); // Desfaz a transação em caso de erro PDO
            error_log("Erro PDO ao atualizar senha para '$userId': " . $e->getMessage());
            return false;
        } catch (\Exception $e) { // Captura outras exceções (ex: usuário não encontrado)
            $this->conn->rollBack(); // Desfaz a transação
            error_log("Erro ao atualizar senha para '$userId': " . $e->getMessage());
            return false;
        }
    }

    /**
     * @deprecated Usar updateUserPassword em vez disso.
     * Registra uma nova senha no histórico e atualiza data (método antigo separado).
     *
     * @param string $userId ID do usuário
     * @param string $password Nova senha
     */
    /*
    public function recordPasswordHistory($userId, $password) {
        // Este método foi integrado em updateUserPassword para garantir atomicidade com a transação.
        // Mantenha comentado ou remova se não for mais necessário.
    }
    */

    /**
     * Obtém a política de senha como um array associativo.
     *
     * @return array
     */
    public function getPolicyAsArray(): array
    {
        return [
            'minLength' => self::MIN_LENGTH,
            'requireUppercase' => self::REQUIRE_UPPERCASE,
            'requireLowercase' => self::REQUIRE_LOWERCASE,
            'requireNumbers' => self::REQUIRE_NUMBERS,
            'requireSpecial' => self::REQUIRE_SPECIAL,
            'maxAgeDays' => self::MAX_AGE_DAYS,
            'historySize' => self::HISTORY_SIZE,
        ];
    }
} 