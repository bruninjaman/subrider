<?php
/**
 * Gerenciador de Clientes
 * 
 * Classe responsável por gerenciar os clientes (proprietários com acesso)
 * incluindo autenticação, tokens de acesso e preferências.
 */
class ClientManager {
    private $db;
    private $notificationManager;
    
    public function __construct() {
        require_once __DIR__ . '/database.php';
        require_once __DIR__ . '/notification_manager.php';
        $this->db = new Database();
        $this->notificationManager = new NotificationManager();
    }
    
    /**
     * Cria credenciais de acesso para um proprietário
     * 
     * @param int $proprietarioId ID do proprietário
     * @param string $email Email do cliente
     * @param string $senha Senha do cliente (não criptografada)
     * @return bool
     */
    public function criarCredenciais(int $proprietarioId, string $email, string $senha): bool {
        // Verifica se já existe um cliente com este email
        $sql = "SELECT id FROM proprietarios WHERE email = ? AND id != ?";
        $result = $this->db->query($sql, [$email, $proprietarioId]);
        
        if (!empty($result)) {
            throw new Exception('Email já está em uso');
        }
        
        // Criptografa a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Atualiza proprietário com credenciais
        $sql = "UPDATE proprietarios 
                SET email = ?, senha = ?, ativo = TRUE 
                WHERE id = ?";
        
        $success = $this->db->query($sql, [$email, $senhaHash, $proprietarioId]);
        
        if ($success) {
            // Envia notificação de boas-vindas
            $this->notificationManager->criarNotificacao(
                $proprietarioId,
                'cliente_cadastro',
                'Bem-vindo à área do cliente',
                'Seu acesso foi criado com sucesso. Agora você pode acompanhar suas motos e serviços online.',
                '/cliente/dashboard.php'
            );
        }
        
        return $success !== false;
    }
    
    /**
     * Autentica um cliente
     * 
     * @param string $email Email do cliente
     * @param string $senha Senha do cliente
     * @return array|false Dados do cliente ou false se falhar
     */
    public function autenticar(string $email, string $senha) {
        $sql = "SELECT id, nome, email, senha, ativo
                FROM proprietarios
                WHERE email = ? AND deleted_at IS NULL";
        
        $result = $this->db->query($sql, [$email]);
        
        if (empty($result)) {
            return false;
        }
        
        $cliente = $result[0];
        
        if (!$cliente['ativo']) {
            throw new Exception('Conta desativada');
        }
        
        if (!password_verify($senha, $cliente['senha'])) {
            return false;
        }
        
        // Atualiza último acesso
        $sql = "UPDATE proprietarios SET ultimo_acesso = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$cliente['id']]);
        
        // Cria token de acesso
        $token = $this->criarTokenAcesso($cliente['id']);
        
        unset($cliente['senha']);
        $cliente['token'] = $token;
        
        return $cliente;
    }
    
    /**
     * Cria um token de acesso para o cliente
     * 
     * @param int $proprietarioId ID do proprietário
     * @return string Token gerado
     */
    private function criarTokenAcesso(int $proprietarioId): string {
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $sql = "INSERT INTO tokens_acesso (proprietario_id, token, user_agent, ip_address, expires_at)
                VALUES (?, ?, ?, ?, ?)";
        
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $this->db->query($sql, [
            $proprietarioId,
            $token,
            $userAgent,
            $ipAddress,
            $expiracao
        ]);
        
        return $token;
    }
    
    /**
     * Verifica se um token de acesso é válido
     * 
     * @param string $token Token de acesso
     * @return array|false Dados do cliente ou false se inválido
     */
    public function verificarToken(string $token) {
        $sql = "SELECT p.id, p.nome, p.email, p.ativo
                FROM tokens_acesso t
                JOIN proprietarios p ON t.proprietario_id = p.id
                WHERE t.token = ?
                AND t.expires_at > CURRENT_TIMESTAMP
                AND t.revoked_at IS NULL
                AND p.deleted_at IS NULL";
        
        $result = $this->db->query($sql, [$token]);
        
        if (empty($result)) {
            return false;
        }
        
        $cliente = $result[0];
        
        if (!$cliente['ativo']) {
            throw new Exception('Conta desativada');
        }
        
        return $cliente;
    }
    
    /**
     * Revoga um token de acesso
     * 
     * @param string $token Token a ser revogado
     * @return bool
     */
    public function revogarToken(string $token): bool {
        $sql = "UPDATE tokens_acesso 
                SET revoked_at = CURRENT_TIMESTAMP 
                WHERE token = ?";
        
        $result = $this->db->query($sql, [$token]);
        return $result !== false;
    }
    
    /**
     * Inicia processo de recuperação de senha
     * 
     * @param string $email Email do cliente
     * @return bool
     */
    public function iniciarRecuperacaoSenha(string $email): bool {
        $sql = "SELECT id, nome FROM proprietarios WHERE email = ? AND ativo = TRUE";
        $result = $this->db->query($sql, [$email]);
        
        if (empty($result)) {
            return false;
        }
        
        $cliente = $result[0];
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "UPDATE proprietarios 
                SET token_reset_senha = ?, token_expiracao = ?
                WHERE id = ?";
        
        $success = $this->db->query($sql, [$token, $expiracao, $cliente['id']]);
        
        if ($success) {
            // Envia email com link de recuperação
            $link = "https://subrider.com.br/cliente/redefinir_senha.php?token=" . $token;
            $mensagem = "Olá {$cliente['nome']},\n\n"
                     . "Recebemos uma solicitação para redefinir sua senha.\n"
                     . "Clique no link abaixo para criar uma nova senha:\n\n"
                     . $link . "\n\n"
                     . "Este link é válido por 1 hora.\n"
                     . "Se você não solicitou esta alteração, ignore este email.";
            
            mail($email, "Recuperação de Senha - SubRider", $mensagem);
        }
        
        return $success !== false;
    }
    
    /**
     * Redefine a senha do cliente
     * 
     * @param string $token Token de recuperação
     * @param string $novaSenha Nova senha
     * @return bool
     */
    public function redefinirSenha(string $token, string $novaSenha): bool {
        $sql = "SELECT id FROM proprietarios 
                WHERE token_reset_senha = ? 
                AND token_expiracao > CURRENT_TIMESTAMP
                AND ativo = TRUE";
        
        $result = $this->db->query($sql, [$token]);
        
        if (empty($result)) {
            return false;
        }
        
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        
        $sql = "UPDATE proprietarios 
                SET senha = ?, token_reset_senha = NULL, token_expiracao = NULL
                WHERE id = ?";
        
        $success = $this->db->query($sql, [$senhaHash, $result[0]['id']]);
        
        if ($success) {
            // Revoga todos os tokens de acesso
            $sql = "UPDATE tokens_acesso 
                    SET revoked_at = CURRENT_TIMESTAMP 
                    WHERE proprietario_id = ?";
            $this->db->query($sql, [$result[0]['id']]);
        }
        
        return $success !== false;
    }
    
    /**
     * Atualiza as preferências do cliente
     * 
     * @param int $proprietarioId ID do proprietário
     * @param array $preferencias Array com preferências
     * @return bool
     */
    public function atualizarPreferencias(int $proprietarioId, array $preferencias): bool {
        $campos = [];
        $valores = [];
        
        foreach ($preferencias as $campo => $valor) {
            if (in_array($campo, ['notificacao_email', 'notificacao_whatsapp', 'tema'])) {
                $campos[] = "$campo = ?";
                $valores[] = $valor;
            }
        }
        
        if (empty($campos)) {
            return false;
        }
        
        $valores[] = $proprietarioId;
        
        $sql = "UPDATE preferencias_cliente 
                SET " . implode(', ', $campos) . "
                WHERE proprietario_id = ?";
        
        $result = $this->db->query($sql, $valores);
        return $result !== false;
    }
    
    /**
     * Obtém as preferências do cliente
     * 
     * @param int $proprietarioId ID do proprietário
     * @return array
     */
    public function getPreferencias(int $proprietarioId): array {
        $sql = "SELECT notificacao_email, notificacao_whatsapp, tema
                FROM preferencias_cliente
                WHERE proprietario_id = ?";
        
        $result = $this->db->query($sql, [$proprietarioId]);
        return $result[0] ?? [
            'notificacao_email' => true,
            'notificacao_whatsapp' => false,
            'tema' => 'light'
        ];
    }
} 