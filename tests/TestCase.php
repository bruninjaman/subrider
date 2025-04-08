<?php
namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Limpa qualquer saída anterior
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Inicia buffer de saída
        ob_start();
        
        // Reseta variáveis de sessão
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        
        // Reseta variáveis POST
        $_POST = [];
        
        // Reseta headers
        if (!headers_sent()) {
            header_remove();
        }
    }

    protected function tearDown(): void
    {
        // Limpa buffer de saída
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        parent::tearDown();
    }
}