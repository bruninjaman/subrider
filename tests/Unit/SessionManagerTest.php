<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Testes unitários para o SessionManager
 */
class SessionManagerTest extends TestCase
{
    private $sessionManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionManager = new \SessionManager();
        // Limpa qualquer sessão existente
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testStartSession()
    {
        $this->sessionManager->startSession();
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    public function testSetAndGetUserId()
    {
        $this->sessionManager->startSession();
        $userId = 123;
        $this->sessionManager->setUserId($userId);
        $this->assertEquals($userId, $this->sessionManager->getUserId());
    }

    public function testIsLoggedIn()
    {
        $this->sessionManager->startSession();
        $this->assertFalse($this->sessionManager->isLoggedIn());
        
        $this->sessionManager->setUserId(123);
        $this->assertTrue($this->sessionManager->isLoggedIn());
    }

    public function testLogout()
    {
        $this->sessionManager->startSession();
        $this->sessionManager->setUserId(123);
        $this->assertTrue($this->sessionManager->isLoggedIn());
        
        $this->sessionManager->logout();
        $this->assertFalse($this->sessionManager->isLoggedIn());
    }

    public function testSessionTimeout()
    {
        $this->sessionManager->startSession();
        $this->sessionManager->setUserSession(123, 'testuser', 'admin');
        
        // Simula última atividade há 31 minutos
        $_SESSION['last_activity'] = time() - (31 * 60);
        
        $this->assertFalse($this->sessionManager->checkSessionTimeout());
        $this->assertFalse($this->sessionManager->isAuthenticated());
    }

    public function testSessionRefresh()
    {
        $this->sessionManager->startSession();
        $this->sessionManager->setUserSession(123, 'testuser', 'admin');
        
        $oldActivity = $_SESSION['last_activity'];
        sleep(1); // Espera 1 segundo
        
        $this->sessionManager->refreshActivity();
        $this->assertGreaterThan($oldActivity, $_SESSION['last_activity']);
    }

    public function testGetSessionInfo()
    {
        $this->sessionManager->startSession();
        $userId = 123;
        $username = 'testuser';
        $userType = 'admin';
        
        $this->sessionManager->setUserSession($userId, $username, $userType);
        
        $info = $this->sessionManager->getSessionInfo();
        
        $this->assertIsArray($info);
        $this->assertEquals($userId, $info['user_id']);
        $this->assertEquals($username, $info['username']);
        $this->assertEquals($userType, $info['user_type']);
        $this->assertArrayHasKey('last_activity', $info);
        $this->assertArrayHasKey('last_regeneration', $info);
    }
} 