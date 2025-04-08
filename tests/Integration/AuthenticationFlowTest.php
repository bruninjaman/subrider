<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Testes de integração para o fluxo de autenticação
 */
class AuthenticationFlowTest extends TestCase
{
    private $conn;
    private $sessionManager;
    private $passwordPolicy;
    private $loginAttempts;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuração do banco de dados de teste
        require_once __DIR__ . '/../../config.php';
        $this->conn = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME . '_test');
        
        // Inicializa as classes necessárias
        $this->sessionManager = new \SessionManager();
        $this->passwordPolicy = new \PasswordPolicy($this->conn);
        $this->loginAttempts = new \LoginAttempts($this->conn);
        
        // Limpa dados de teste anteriores
        $this->conn->query("TRUNCATE TABLE login_attempts");
        $this->conn->query("TRUNCATE TABLE password_history");
        $this->conn->query("DELETE FROM login WHERE username = 'testuser'");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->conn->close();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testFluxoAutenticacaoCompleto()
    {
        // 1. Criação de usuário com senha forte
        $username = 'testuser';
        $password = 'Teste123@';
        
        // Valida a senha
        $validacao = $this->passwordPolicy->validatePassword($password);
        $this->assertTrue($validacao['valid'], 'A senha deve atender aos requisitos de segurança');
        
        // Cria o usuário
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO login (username, password) VALUES (?, ?)");
        $stmt->bind_param('ss', $username, $passwordHash);
        $stmt->execute();
        $userId = $stmt->insert_id;
        $stmt->close();
        
        // 2. Tentativa de login com senha incorreta
        $wrongPassword = 'SenhaErrada123@';
        $this->assertFalse($this->tryLogin($username, $wrongPassword));
        
        // Verifica se a tentativa foi registrada
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM login_attempts WHERE username = ? AND success = 0");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $this->assertEquals(1, $result['count'], 'Deve registrar tentativa falha de login');
        $stmt->close();
        
        // 3. Login com credenciais corretas
        $this->assertTrue($this->tryLogin($username, $password));
        
        // Verifica se a sessão foi iniciada corretamente
        $this->assertTrue($this->sessionManager->isLoggedIn());
        $this->assertEquals($userId, $this->sessionManager->getUserId());
        
        // 4. Verifica bloqueio após múltiplas tentativas falhas
        for ($i = 0; $i < 5; $i++) {
            $this->tryLogin($username, $wrongPassword);
        }
        
        $blockStatus = $this->loginAttempts->isUserBlocked($username);
        $this->assertTrue($blockStatus['blocked'], 'Usuário deve ser bloqueado após 5 tentativas falhas');
        
        // 5. Tenta login com credenciais corretas durante bloqueio
        $this->assertFalse($this->tryLogin($username, $password));
        
        // 6. Simula expiração da senha
        $stmt = $this->conn->prepare("UPDATE login SET password_changed_at = DATE_SUB(NOW(), INTERVAL 100 DAY) WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->close();
        
        $this->assertTrue($this->passwordPolicy->passwordNeedsChange($username));
        
        // 7. Troca de senha
        $newPassword = 'NovaSenha456@';
        $validacao = $this->passwordPolicy->validatePassword($newPassword);
        $this->assertTrue($validacao['valid']);
        
        // Verifica se a nova senha não foi usada recentemente
        $this->assertFalse($this->passwordPolicy->isPasswordReused($username, $newPassword));
        
        // Atualiza a senha
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE login SET password = ? WHERE username = ?");
        $stmt->bind_param('ss', $newPasswordHash, $username);
        $stmt->execute();
        $stmt->close();
        
        // 8. Login com nova senha
        $this->assertTrue($this->tryLogin($username, $newPassword));
    }

    private function tryLogin(string $username, string $password): bool
    {
        $stmt = $this->conn->prepare("SELECT * FROM login WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user) {
            return false;
        }
        
        $blockStatus = $this->loginAttempts->isUserBlocked($username);
        if ($blockStatus['blocked']) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            $this->sessionManager->startSession();
            $this->sessionManager->setUserSession($user['id'], $username, $user['userType'] ?? 'user');
            $this->loginAttempts->recordAttempt($username, true);
            return true;
        }
        
        $this->loginAttempts->recordAttempt($username, false);
        return false;
    }
} 