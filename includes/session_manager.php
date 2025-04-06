<?php
/**
 * Gerenciador de Sessões
 * 
 * Classe responsável por gerenciar as sessões do sistema,
 * incluindo autenticação de usuários e clientes.
 */
class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Define o ID do usuário na sessão
     * 
     * @param int $userId ID do usuário
     */
    public function setUserId(int $userId): void {
        $_SESSION['user_id'] = $userId;
    }
    
    /**
     * Obtém o ID do usuário da sessão
     * 
     * @return int|null ID do usuário ou null se não estiver logado
     */
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Define o ID do cliente na sessão
     * 
     * @param int $clienteId ID do cliente
     */
    public function setClienteId(int $clienteId): void {
        $_SESSION['cliente_id'] = $clienteId;
    }
    
    /**
     * Obtém o ID do cliente da sessão
     * 
     * @return int|null ID do cliente ou null se não estiver logado
     */
    public function getClienteId(): ?int {
        return $_SESSION['cliente_id'] ?? null;
    }
    
    /**
     * Define o token de acesso do cliente na sessão
     * 
     * @param string $token Token de acesso
     */
    public function setClienteToken(string $token): void {
        $_SESSION['cliente_token'] = $token;
    }
    
    /**
     * Obtém o token de acesso do cliente da sessão
     * 
     * @return string|null Token de acesso ou null se não estiver logado
     */
    public function getClienteToken(): ?string {
        return $_SESSION['cliente_token'] ?? null;
    }
    
    /**
     * Verifica se o usuário está logado
     * 
     * @return bool
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Verifica se o cliente está logado
     * 
     * @return bool
     */
    public function isClienteLoggedIn(): bool {
        return isset($_SESSION['cliente_id']) && isset($_SESSION['cliente_token']);
    }
    
    /**
     * Encerra a sessão
     */
    public function logout(): void {
        session_destroy();
    }
} 