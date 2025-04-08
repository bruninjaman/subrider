<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Testes unitários para o PasswordPolicy
 */
class PasswordPolicyTest extends TestCase
{
    private $passwordPolicy;
    private $conn;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock da conexão com o banco
        $this->conn = $this->createMock(\mysqli::class);
        $this->passwordPolicy = new \PasswordPolicy($this->conn);
    }

    public function testValidaSenhaForte()
    {
        $senha = "Teste123@";
        $resultado = $this->passwordPolicy->validatePassword($senha);
        
        $this->assertTrue($resultado['valid']);
        $this->assertEmpty($resultado['errors']);
    }

    public function testRejeicaoSenhaCurta()
    {
        $senha = "Abc12@";
        $resultado = $this->passwordPolicy->validatePassword($senha);
        
        $this->assertFalse($resultado['valid']);
        $this->assertContains('A senha deve ter pelo menos 8 caracteres', $resultado['errors']);
    }

    public function testRejeicaoSenhaSemMaiuscula()
    {
        $senha = "teste123@";
        $resultado = $this->passwordPolicy->validatePassword($senha);
        
        $this->assertFalse($resultado['valid']);
        $this->assertContains('A senha deve conter pelo menos uma letra maiúscula', $resultado['errors']);
    }

    public function testRejeicaoSenhaSemMinuscula()
    {
        $senha = "TESTE123@";
        $resultado = $this->passwordPolicy->validatePassword($senha);
        
        $this->assertFalse($resultado['valid']);
        $this->assertContains('A senha deve conter pelo menos uma letra minúscula', $resultado['errors']);
    }

    public function testRejeicaoSenhaSemNumero()
    {
        $senha = "TesteAbc@";
        $resultado = $this->passwordPolicy->validatePassword($senha);
        
        $this->assertFalse($resultado['valid']);
        $this->assertContains('A senha deve conter pelo menos um número', $resultado['errors']);
    }

    public function testRejeicaoSenhaSemCaracterEspecial()
    {
        $senha = "TesteAbc123";
        $resultado = $this->passwordPolicy->validatePassword($senha);
        
        $this->assertFalse($resultado['valid']);
        $this->assertContains('A senha deve conter pelo menos um caractere especial', $resultado['errors']);
    }

    public function testVerificacaoSenhaExpirada()
    {
        $userId = "testuser";
        
        // Mock para simular senha expirada
        $stmt = $this->createMock(\mysqli_stmt::class);
        $result = $this->createMock(\mysqli_result::class);
        
        $stmt->expects($this->once())
             ->method('execute');
             
        $stmt->expects($this->once())
             ->method('get_result')
             ->willReturn($result);
             
        $result->expects($this->once())
               ->method('fetch_assoc')
               ->willReturn(['password_changed_at' => date('Y-m-d H:i:s', strtotime('-100 days'))]);
               
        $this->conn->expects($this->once())
                   ->method('prepare')
                   ->willReturn($stmt);
        
        $this->assertTrue($this->passwordPolicy->passwordNeedsChange($userId));
    }

    public function testVerificacaoSenhaNaoExpirada()
    {
        $userId = "testuser";
        
        // Mock para simular senha não expirada
        $stmt = $this->createMock(\mysqli_stmt::class);
        $result = $this->createMock(\mysqli_result::class);
        
        $stmt->expects($this->once())
             ->method('execute');
             
        $stmt->expects($this->once())
             ->method('get_result')
             ->willReturn($result);
             
        $result->expects($this->once())
               ->method('fetch_assoc')
               ->willReturn(['password_changed_at' => date('Y-m-d H:i:s', strtotime('-30 days'))]);
               
        $this->conn->expects($this->once())
                   ->method('prepare')
                   ->willReturn($stmt);
        
        $this->assertFalse($this->passwordPolicy->passwordNeedsChange($userId));
    }

    public function testVerificacaoSenhaReutilizada()
    {
        $userId = "testuser";
        $senha = "Teste123@";
        
        // Mock para simular histórico de senhas
        $stmt = $this->createMock(\mysqli_stmt::class);
        $result = $this->createMock(\mysqli_result::class);
        
        $stmt->expects($this->once())
             ->method('execute');
             
        $stmt->expects($this->once())
             ->method('get_result')
             ->willReturn($result);
             
        $result->expects($this->once())
               ->method('fetch_assoc')
               ->willReturn(['password_hash' => password_hash($senha, PASSWORD_DEFAULT)]);
               
        $this->conn->expects($this->once())
                   ->method('prepare')
                   ->willReturn($stmt);
        
        $this->assertTrue($this->passwordPolicy->isPasswordReused($userId, $senha));
    }
} 