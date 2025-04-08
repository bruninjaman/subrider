<?php
namespace Tests;

use PHPUnit\Framework\TestListener as PHPUnitTestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;

/**
 * Listener personalizado para configurar ambiente de testes
 */
class TestListener implements PHPUnitTestListener
{
    use TestListenerDefaultImplementation;

    private static $dbInitialized = false;

    public function startTestSuite(TestSuite $suite): void
    {
        if (!self::$dbInitialized) {
            $this->initializeTestDatabase();
            self::$dbInitialized = true;
        }
    }

    private function initializeTestDatabase(): void
    {
        // Executa script de setup do banco de dados
        require_once __DIR__ . '/setup_test_db.php';
        
        echo "\nBanco de dados de teste inicializado\n";
    }
} 