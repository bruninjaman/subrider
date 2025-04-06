<?php
/**
 * Gerenciador de Notificações
 * 
 * Classe responsável por gerenciar todas as notificações do sistema,
 * incluindo criação, leitura e envio de notificações.
 */
class NotificationManager {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/database.php';
        $this->db = new Database();
    }
    
    /**
     * Cria uma nova notificação
     * 
     * @param int $usuarioId ID do usuário que receberá a notificação
     * @param string $tipo Tipo da notificação
     * @param string $titulo Título da notificação
     * @param string $mensagem Mensagem da notificação
     * @param string|null $link Link opcional relacionado à notificação
     * @return int ID da notificação criada
     */
    public function criarNotificacao(
        int $usuarioId,
        string $tipo,
        string $titulo,
        string $mensagem,
        ?string $link = null
    ): int {
        $sql = "INSERT INTO notificacoes (usuario_id, tipo, titulo, mensagem, link)
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [$usuarioId, $tipo, $titulo, $mensagem, $link];
        $this->db->query($sql, $params);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Marca uma notificação como lida
     * 
     * @param int $notificacaoId ID da notificação
     * @param int $usuarioId ID do usuário (para validação)
     * @return bool
     */
    public function marcarComoLida(int $notificacaoId, int $usuarioId): bool {
        $sql = "UPDATE notificacoes 
                SET lida = TRUE 
                WHERE id = ? AND usuario_id = ?";
        
        $result = $this->db->query($sql, [$notificacaoId, $usuarioId]);
        return $result !== false;
    }
    
    /**
     * Marca todas as notificações do usuário como lidas
     * 
     * @param int $usuarioId ID do usuário
     * @return bool
     */
    public function marcarTodasComoLidas(int $usuarioId): bool {
        $sql = "UPDATE notificacoes 
                SET lida = TRUE 
                WHERE usuario_id = ? AND lida = FALSE";
        
        $result = $this->db->query($sql, [$usuarioId]);
        return $result !== false;
    }
    
    /**
     * Retorna as notificações não lidas do usuário
     * 
     * @param int $usuarioId ID do usuário
     * @param int $limit Limite de notificações (opcional)
     * @return array
     */
    public function getNotificacoesNaoLidas(int $usuarioId, int $limit = 10): array {
        $sql = "SELECT id, tipo, titulo, mensagem, link, created_at
                FROM notificacoes
                WHERE usuario_id = ? 
                AND lida = FALSE 
                AND deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$usuarioId, $limit]);
    }
    
    /**
     * Retorna o total de notificações não lidas do usuário
     * 
     * @param int $usuarioId ID do usuário
     * @return int
     */
    public function getTotalNaoLidas(int $usuarioId): int {
        $sql = "SELECT COUNT(*) as total
                FROM notificacoes
                WHERE usuario_id = ? 
                AND lida = FALSE 
                AND deleted_at IS NULL";
        
        $result = $this->db->query($sql, [$usuarioId]);
        return (int) $result[0]['total'];
    }
    
    /**
     * Retorna todas as notificações do usuário
     * 
     * @param int $usuarioId ID do usuário
     * @param int $page Número da página
     * @param int $perPage Itens por página
     * @return array
     */
    public function getTodasNotificacoes(
        int $usuarioId,
        int $page = 1,
        int $perPage = 20
    ): array {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT id, tipo, titulo, mensagem, link, lida, created_at
                FROM notificacoes
                WHERE usuario_id = ? 
                AND deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->query($sql, [$usuarioId, $perPage, $offset]);
    }
    
    /**
     * Exclui uma notificação (soft delete)
     * 
     * @param int $notificacaoId ID da notificação
     * @param int $usuarioId ID do usuário (para validação)
     * @return bool
     */
    public function excluirNotificacao(int $notificacaoId, int $usuarioId): bool {
        $sql = "UPDATE notificacoes 
                SET deleted_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND usuario_id = ?";
        
        $result = $this->db->query($sql, [$notificacaoId, $usuarioId]);
        return $result !== false;
    }
    
    /**
     * Cria uma notificação para todos os usuários ativos
     * 
     * @param string $tipo Tipo da notificação
     * @param string $titulo Título da notificação
     * @param string $mensagem Mensagem da notificação
     * @param string|null $link Link opcional
     * @return bool
     */
    public function notificarTodos(
        string $tipo,
        string $titulo,
        string $mensagem,
        ?string $link = null
    ): bool {
        $sql = "INSERT INTO notificacoes (usuario_id, tipo, titulo, mensagem, link)
                SELECT id, ?, ?, ?, ?
                FROM usuarios
                WHERE deleted_at IS NULL";
        
        $result = $this->db->query($sql, [$tipo, $titulo, $mensagem, $link]);
        return $result !== false;
    }
    
    /**
     * Retorna o ícone apropriado para o tipo de notificação
     * 
     * @param string $tipo Tipo da notificação
     * @return string Classe do ícone FontAwesome
     */
    public function getIcone(string $tipo): string {
        $icones = [
            'os_criada' => 'fa-clipboard-list',
            'os_atualizada' => 'fa-sync',
            'os_finalizada' => 'fa-check-circle',
            'os_cancelada' => 'fa-times-circle',
            'moto_transferida' => 'fa-exchange-alt',
            'backup_erro' => 'fa-exclamation-triangle',
            'sistema_atualizacao' => 'fa-cog'
        ];
        
        return $icones[$tipo] ?? 'fa-bell';
    }
    
    /**
     * Retorna a classe de cor apropriada para o tipo de notificação
     * 
     * @param string $tipo Tipo da notificação
     * @return string Classe Bootstrap
     */
    public function getCorTipo(string $tipo): string {
        $cores = [
            'os_criada' => 'primary',
            'os_atualizada' => 'info',
            'os_finalizada' => 'success',
            'os_cancelada' => 'danger',
            'moto_transferida' => 'warning',
            'backup_erro' => 'danger',
            'sistema_atualizacao' => 'secondary'
        ];
        
        return $cores[$tipo] ?? 'primary';
    }
} 