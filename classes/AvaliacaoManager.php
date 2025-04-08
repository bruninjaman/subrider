<?php
namespace Subrider;

/**
 * Gerenciador de Avaliações
 * 
 * Classe responsável por gerenciar as avaliações de serviços,
 * incluindo criação, leitura e notificações.
 */
class AvaliacaoManager {
    private $db;
    private $notificationManager;
    
    public function __construct() {
        require_once __DIR__ . '/../includes/database.php';
        require_once __DIR__ . '/../includes/notification_manager.php';
        $this->db = new Database();
        $this->notificationManager = new NotificationManager();
        $this->initializeTable();
    }
    
    /**
     * Inicializa a tabela de avaliações se não existir
     */
    private function initializeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS avaliacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ordem_id VARCHAR(50) NOT NULL,
            nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
            comentario TEXT,
            data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            proprietario_id INT NOT NULL,
            status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
            motivo_rejeicao TEXT,
            INDEX (ordem_id),
            INDEX (proprietario_id),
            INDEX (status)
        )";
        
        $this->db->query($sql);
    }
    
    /**
     * Cria uma nova avaliação
     * 
     * @param string $ordemId ID da ordem de serviço
     * @param int $nota Nota da avaliação (1-5)
     * @param string $comentario Comentário opcional
     * @param int $proprietarioId ID do proprietário
     * @return bool|int ID da avaliação criada ou false em caso de erro
     */
    public function criarAvaliacao(string $ordemId, int $nota, string $comentario, int $proprietarioId) {
        if ($nota < 1 || $nota > 5) {
            return false;
        }
        
        $sql = "INSERT INTO avaliacoes (ordem_id, nota, comentario, proprietario_id)
                VALUES (?, ?, ?, ?)";
        
        $params = [$ordemId, $nota, $comentario, $proprietarioId];
        $result = $this->db->query($sql, $params);
        
        if ($result) {
            $avaliacaoId = $this->db->lastInsertId();
            
            // Notifica administradores sobre nova avaliação
            $this->notificarAdministradores($avaliacaoId, $ordemId, $nota);
            
            return $avaliacaoId;
        }
        
        return false;
    }
    
    /**
     * Notifica administradores sobre nova avaliação
     */
    private function notificarAdministradores($avaliacaoId, $ordemId, $nota) {
        $titulo = "Nova Avaliação - OS #$ordemId";
        $mensagem = "Uma nova avaliação foi registrada para a OS #$ordemId com nota $nota/5.";
        $link = "avaliacoes.php?id=" . $avaliacaoId;
        
        // Obtém IDs dos administradores
        $sql = "SELECT id FROM usuarios WHERE nivel = 'admin' AND deleted_at IS NULL";
        $admins = $this->db->query($sql);
        
        foreach ($admins as $admin) {
            $this->notificationManager->criarNotificacao(
                $admin['id'],
                'nova_avaliacao',
                $titulo,
                $mensagem,
                $link
            );
        }
    }
    
    /**
     * Aprova uma avaliação
     * 
     * @param int $avaliacaoId ID da avaliação
     * @return bool
     */
    public function aprovarAvaliacao(int $avaliacaoId): bool {
        $sql = "UPDATE avaliacoes SET status = 'aprovada' WHERE id = ?";
        return $this->db->query($sql, [$avaliacaoId]) !== false;
    }
    
    /**
     * Rejeita uma avaliação
     * 
     * @param int $avaliacaoId ID da avaliação
     * @param string $motivo Motivo da rejeição
     * @return bool
     */
    public function rejeitarAvaliacao(int $avaliacaoId, string $motivo): bool {
        $sql = "UPDATE avaliacoes SET status = 'rejeitada', motivo_rejeicao = ? WHERE id = ?";
        return $this->db->query($sql, [$motivo, $avaliacaoId]);
    }
    
    /**
     * Obtém uma avaliação específica
     * 
     * @param int $avaliacaoId ID da avaliação
     * @return array|null Dados da avaliação ou null se não encontrada
     */
    public function getAvaliacao(int $avaliacaoId): ?array {
        $sql = "SELECT a.*, 
                       os.descricao as ordem_descricao,
                       p.nome as proprietario_nome
                FROM avaliacoes a
                JOIN ordem_servicos os ON a.ordem_id = os.Codigo
                JOIN proprietarios p ON a.proprietario_id = p.id
                WHERE a.id = ?";
        
        $result = $this->db->query($sql, [$avaliacaoId]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Lista avaliações com filtros
     * 
     * @param array $filtros Array com filtros (status, data_inicio, data_fim)
     * @param int $page Número da página
     * @param int $perPage Itens por página
     * @return array Array com avaliações e total
     */
    public function listarAvaliacoes(array $filtros = [], int $page = 1, int $perPage = 20): array {
        $where = [];
        $params = [];
        
        if (!empty($filtros['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filtros['status'];
        }
        
        if (!empty($filtros['data_inicio'])) {
            $where[] = "a.data_avaliacao >= ?";
            $params[] = $filtros['data_inicio'];
        }
        
        if (!empty($filtros['data_fim'])) {
            $where[] = "a.data_avaliacao <= ?";
            $params[] = $filtros['data_fim'];
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // Total de registros
        $sqlCount = "SELECT COUNT(*) as total FROM avaliacoes a $whereClause";
        $total = $this->db->query($sqlCount, $params)[0]['total'];
        
        // Registros da página
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT a.*, 
                       os.descricao as ordem_descricao,
                       p.nome as proprietario_nome
                FROM avaliacoes a
                JOIN ordem_servicos os ON a.ordem_id = os.Codigo
                JOIN proprietarios p ON a.proprietario_id = p.id
                $whereClause
                ORDER BY a.data_avaliacao DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $avaliacoes = $this->db->query($sql, $params);
        
        return [
            'total' => $total,
            'avaliacoes' => $avaliacoes
        ];
    }
    
    /**
     * Calcula estatísticas das avaliações
     * 
     * @return array Array com estatísticas
     */
    public function calcularEstatisticas(): array {
        $sql = "SELECT 
                    COUNT(*) as total,
                    AVG(nota) as media,
                    COUNT(CASE WHEN nota >= 4 THEN 1 END) as positivas,
                    COUNT(CASE WHEN nota <= 2 THEN 1 END) as negativas
                FROM avaliacoes
                WHERE status = 'aprovada'";
        
        $result = $this->db->query($sql)[0];
        
        return [
            'total' => (int) $result['total'],
            'media' => round($result['media'], 1),
            'positivas' => (int) $result['positivas'],
            'negativas' => (int) $result['negativas']
        ];
    }
} 