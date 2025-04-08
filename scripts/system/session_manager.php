<?php
/**
 * Gerenciador Central de Sessões
 * 
 * Este arquivo é responsável por toda a configuração e gerenciamento
 * de sessões do sistema SubRider.
 */

class SessionManager {
    private static $instance = null;
    private $isInitialized = false;
    private $logFile;
    
    private function __construct() {
        $this->logFile = __DIR__ . '/../../logs/session.log';
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0755, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
    
    public function startSession() {
        if ($this->isInitialized) {
            return;
        }
        
        // Configurações de segurança da sessão
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        
        // Configuração do tempo de vida da sessão (30 dias)
        ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
        ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60);
        
        session_set_cookie_params([
            'lifetime' => 30 * 24 * 60 * 60,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->log("Sessão iniciada - ID: " . session_id());
        }
        
        $this->isInitialized = true;
        
        // Regenera o ID da sessão periodicamente para segurança
        if (!isset($_SESSION['last_regeneration']) || 
            (time() - $_SESSION['last_regeneration']) > 3600) {
            $oldId = session_id();
            session_regenerate_id(true);
            $newId = session_id();
            $_SESSION['last_regeneration'] = time();
            $this->log("ID da sessão regenerado - Antigo: $oldId, Novo: $newId");
        }
    }
    
    public function destroySession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $sessionId = session_id();
            session_unset();
            session_destroy();
            setcookie(session_name(), '', time() - 3600, '/');
            $this->log("Sessão destruída - ID: $sessionId");
        }
        $this->isInitialized = false;
    }
    
    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    public function setUserSession($userId, $username, $userType) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user'] = $username;
        $_SESSION['type'] = $userType;
        $_SESSION['last_activity'] = time();
        $this->log("Usuário autenticado - ID: $userId, Username: $username, Tipo: $userType");
    }
    
    public function checkSessionTimeout() {
        $timeout = 3600; // 1 hora (consistente com init.php)
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > $timeout)) {
            $this->log("Sessão expirada por timeout - Último acesso: " . date('Y-m-d H:i:s', $_SESSION['last_activity']));
            $this->destroySession();
            return false;
        }
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    public function refreshActivity() {
        $_SESSION['last_activity'] = time();
    }
    
    public function getSessionInfo() {
        return [
            'session_id' => session_id(),
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['user'] ?? null,
            'user_type' => $_SESSION['type'] ?? null,
            'last_activity' => isset($_SESSION['last_activity']) ? 
                date('Y-m-d H:i:s', $_SESSION['last_activity']) : null,
            'last_regeneration' => isset($_SESSION['last_regeneration']) ? 
                date('Y-m-d H:i:s', $_SESSION['last_regeneration']) : null
        ];
    }
} 