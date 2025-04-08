<?php
namespace Tests\Integration;

use Tests\TestCase;

class LoginToAvaliacaoTest extends TestCase
{
    public function testCompleteLoginToAvaliacaoFlow()
    {
        // 1. Teste de Login
        $_POST['username'] = 'test@example.com';
        $_POST['password'] = 'password123';
        
        ob_start();
        include __DIR__ . "/../../login.php";
        $output = ob_get_clean();
        
        $this->assertStringContainsString('success', $output);

        // 2. Teste de Redirecionamento para Dashboard
        $_SESSION['user_id'] = 1; // Simula usuário logado
        
        ob_start();
        include __DIR__ . "/../../pages/dashboard.php";
        $output = ob_get_clean();
        
        $this->assertStringContainsString('dashboard', $output);

        // 3. Teste de Acesso à Página de Avaliações
        ob_start();
        include __DIR__ . "/../../pages/avaliacoes.php";
        $output = ob_get_clean();
        
        $this->assertStringContainsString('avaliacoes', $output);
    }

    public function testUnauthorizedAccessPrevention()
    {
        // Tenta acessar avaliações sem login
        $_SESSION = []; // Limpa sessão
        
        ob_start();
        include __DIR__ . "/../../pages/avaliacoes.php";
        $output = ob_get_clean();
        
        $this->assertStringContainsString('login', $output);
    }
}