<?php
namespace Tests\UI;

use PHPUnit\Framework\TestCase;

class AvaliacoesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . "/../../config.php";
    }

    public function testAvaliacoesPageContainsRequiredElements()
    {
        ob_start();
        include __DIR__ . "/../../pages/avaliacoes.php";
        $output = ob_get_clean();
        
        // Verifica elementos essenciais da página
        $this->assertStringContainsString('<div class="container">', $output);
        $this->assertStringContainsString('avaliacoes', $output);
        $this->assertStringContainsString('form', $output);
    }

    public function testAvaliacoesPageResponsiveness()
    {
        ob_start();
        include __DIR__ . "/../../pages/avaliacoes.php";
        $output = ob_get_clean();
        
        // Verifica elementos responsivos
        $this->assertStringContainsString('class="table-responsive"', $output);
        $this->assertStringContainsString('class="container"', $output);
    }

    public function testAvaliacoesFormValidation()
    {
        ob_start();
        include __DIR__ . "/../../pages/avaliacoes.php";
        $output = ob_get_clean();
        
        // Verifica campos obrigatórios
        $this->assertStringContainsString('required', $output);
        $this->assertStringContainsString('validation', $output);
    }
}