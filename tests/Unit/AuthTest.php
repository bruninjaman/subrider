<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . "/../../config.php";
    }

    public function testLoginValidation()
    {
        $_POST['username'] = 'test@example.com';
        $_POST['password'] = 'password123';
        
        ob_start();
        include __DIR__ . "/../../login.php";
        $output = ob_get_clean();
        
        $this->assertStringContainsString('login', $output);
    }

    public function testInvalidLoginAttempt()
    {
        $_POST['username'] = 'invalid@example.com';
        $_POST['password'] = 'wrongpassword';
        
        ob_start();
        include __DIR__ . "/../../login.php";
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Invalid', $output);
    }

    public function testPasswordValidation()
    {
        $_POST['password'] = '123'; // Senha muito curta
        
        ob_start();
        include __DIR__ . "/../../login.php";
        $output = ob_get_clean();
        
        $this->assertStringContainsString('password', $output);
    }
}