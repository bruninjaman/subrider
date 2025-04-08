<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Subrider\AvaliacaoManager;
use Subrider\Database;
use Subrider\NotificationManager;
use ReflectionClass;

class AvaliacaoManagerTest extends TestCase {
    private $db;
    private $avaliacaoManager;
    private $notificationManager;
    
    protected function setUp(): void {
        // Mock do Database
        $this->db = $this->createMock(Database::class);
        
        // Mock do NotificationManager
        $this->notificationManager = $this->createMock(NotificationManager::class);
        
        // Instancia AvaliacaoManager com mocks
        $this->avaliacaoManager = new AvaliacaoManager();
        $this->setPrivateProperty($this->avaliacaoManager, 'db', $this->db);
        $this->setPrivateProperty($this->avaliacaoManager, 'notificationManager', $this->notificationManager);
    }
    
    /**
     * Teste de criação de avaliação com sucesso
     */
    public function testCriarAvaliacaoComSucesso() {
        // Arrange
        $ordemId = "123";
        $nota = 5;
        $comentario = "Ótimo serviço!";
        $proprietarioId = 1;
        
        // Configura expectativas para as chamadas do banco de dados
        $this->db->expects($this->exactly(2))
            ->method('query')
            ->withConsecutive(
                [
                    $this->stringContains("INSERT INTO avaliacoes"),
                    [$ordemId, $nota, $comentario, $proprietarioId]
                ],
                [
                    $this->stringContains("SELECT id FROM usuarios"),
                    []
                ]
            )
            ->willReturnOnConsecutiveCalls(true, [['id' => 1]]);
        
        $this->db->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(1);
        
        // Act
        $result = $this->avaliacaoManager->criarAvaliacao($ordemId, $nota, $comentario, $proprietarioId);
        
        // Assert
        $this->assertEquals(1, $result);
    }
    
    /**
     * Teste de criação de avaliação com nota inválida
     */
    public function testCriarAvaliacaoComNotaInvalida() {
        // Arrange
        $ordemId = "123";
        $nota = 6; // Nota inválida
        $comentario = "Ótimo serviço!";
        $proprietarioId = 1;
        
        // Act
        $result = $this->avaliacaoManager->criarAvaliacao($ordemId, $nota, $comentario, $proprietarioId);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Teste de aprovação de avaliação
     */
    public function testAprovarAvaliacao() {
        // Arrange
        $avaliacaoId = 1;
        
        $this->db->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains("UPDATE avaliacoes SET status = 'aprovada'"),
                [$avaliacaoId]
            )
            ->willReturn(true);
        
        // Act
        $result = $this->avaliacaoManager->aprovarAvaliacao($avaliacaoId);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Teste de rejeição de avaliação
     */
    public function testRejeitarAvaliacao() {
        // Arrange
        $avaliacaoId = 1;
        $motivo = "Conteúdo inadequado";
        
        $this->db->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains("UPDATE avaliacoes"),
                [$motivo, $avaliacaoId]
            )
            ->willReturn(true);
        
        // Act
        $result = $this->avaliacaoManager->rejeitarAvaliacao($avaliacaoId, $motivo);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Teste de listagem de avaliações
     */
    public function testListarAvaliacoes() {
        // Arrange
        $filtros = ['status' => 'pendente'];
        $page = 1;
        $perPage = 20;
        
        $avaliacoesMock = [
            [
                'id' => 1,
                'ordem_id' => '123',
                'nota' => 5,
                'comentario' => 'Ótimo serviço!',
                'status' => 'pendente'
            ]
        ];
        
        $this->db->expects($this->exactly(2))
            ->method('query')
            ->willReturnOnConsecutiveCalls(
                [['total' => 1]],
                $avaliacoesMock
            );
        
        // Act
        $result = $this->avaliacaoManager->listarAvaliacoes($filtros, $page, $perPage);
        
        // Assert
        $this->assertEquals(1, $result['total']);
        $this->assertEquals($avaliacoesMock, $result['avaliacoes']);
    }
    
    /**
     * Teste de cálculo de estatísticas
     */
    public function testCalcularEstatisticas() {
        // Arrange
        $estatisticasMock = [
            'total' => 10,
            'media' => 4.5,
            'positivas' => 8,
            'negativas' => 1
        ];
        
        $this->db->expects($this->once())
            ->method('query')
            ->willReturn([$estatisticasMock]);
        
        // Act
        $result = $this->avaliacaoManager->calcularEstatisticas();
        
        // Assert
        $this->assertEquals($estatisticasMock['total'], $result['total']);
        $this->assertEquals($estatisticasMock['media'], $result['media']);
        $this->assertEquals($estatisticasMock['positivas'], $result['positivas']);
        $this->assertEquals($estatisticasMock['negativas'], $result['negativas']);
    }
    
    /**
     * Helper para definir propriedades privadas
     */
    private function setPrivateProperty($object, $propertyName, $value) {
        $reflection = new ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
} 