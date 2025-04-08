<?php
/**
 * Testes para o PermissionManager
 * 
 * @package Subrider
 * @subpackage Tests\Permissions
 */

namespace Tests\Permissions;

use PHPUnit\Framework\TestCase;
use Subrider\Permissions\PermissionManager;

class PermissionManagerTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function testIsPublicPage(): void
    {
        $this->assertTrue(PermissionManager::isPublicPage('login.php'));
        $this->assertTrue(PermissionManager::isPublicPage('register.php'));
        $this->assertFalse(PermissionManager::isPublicPage('admin.php'));
    }

    public function testIsAuthenticated(): void
    {
        $this->assertFalse(PermissionManager::isAuthenticated());

        $_SESSION['user'] = 'test';
        $this->assertFalse(PermissionManager::isAuthenticated());

        $_SESSION['type'] = PERMISSION_USER;
        $this->assertTrue(PermissionManager::isAuthenticated());
    }

    public function testHasPermission(): void
    {
        $this->assertFalse(PermissionManager::hasPermission(PERMISSION_USER));

        $_SESSION['user'] = 'test';
        $_SESSION['type'] = PERMISSION_USER;
        
        $this->assertTrue(PermissionManager::hasPermission(PERMISSION_USER));
        $this->assertFalse(PermissionManager::hasPermission(PERMISSION_ADMIN));

        $_SESSION['type'] = PERMISSION_ADMIN;
        $this->assertTrue(PermissionManager::hasPermission(PERMISSION_USER));
        $this->assertTrue(PermissionManager::hasPermission(PERMISSION_ADMIN));
    }

    public function testAddAndRemovePublicPage(): void
    {
        $testPage = 'test.php';
        
        $this->assertFalse(PermissionManager::isPublicPage($testPage));
        
        PermissionManager::addPublicPage($testPage);
        $this->assertTrue(PermissionManager::isPublicPage($testPage));
        
        PermissionManager::removePublicPage($testPage);
        $this->assertFalse(PermissionManager::isPublicPage($testPage));
    }

    public function testLogoutClearsSession(): void
    {
        $_SESSION['user'] = 'test';
        $_SESSION['type'] = PERMISSION_USER;
        
        $this->assertTrue(PermissionManager::isAuthenticated());
        
        PermissionManager::logout();
        
        $this->assertFalse(PermissionManager::isAuthenticated());
        $this->assertEmpty($_SESSION);
    }
} 