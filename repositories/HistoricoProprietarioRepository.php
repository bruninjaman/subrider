<?php
require_once __DIR__ . '/../connection/Database.php';

class HistoricoProprietarioRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Registra uma nova transferência de proprietário
     * 
     * @param int $motoId ID da motocicleta
     * @param int $proprietarioId ID do novo proprietário
     * @param string $observacao Observação opcional
     * @return bool Sucesso da operação
     */
    public function registrarTransferencia($motoId, $proprietarioId, $observacao = '') {
        try {
            // Fecha o registro anterior se existir
            $sql = "UPDATE historico_proprietarios 
                   SET data_fim = NOW() 
                   WHERE moto_id = :moto_id 
                   AND data_fim IS NULL";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':moto_id' => $motoId]);

            // Insere o novo registro
            $sql = "INSERT INTO historico_proprietarios 
                   (moto_id, proprietario_id, data_inicio, observacao) 
                   VALUES (:moto_id, :proprietario_id, NOW(), :observacao)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':moto_id' => $motoId,
                ':proprietario_id' => $proprietarioId,
                ':observacao' => $observacao
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao registrar transferência: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca o histórico de uma motocicleta
     * 
     * @param int $motoId ID da motocicleta
     * @return array Lista de registros do histórico
     */
    public function buscarHistoricoMoto($motoId) {
        $sql = "SELECT h.*, 
                       p.nome as proprietario_nome,
                       m.placa as moto_placa,
                       m.marca as moto_marca,
                       m.modelo as moto_modelo
                FROM historico_proprietarios h
                JOIN proprietarios p ON h.proprietario_id = p.id
                JOIN motocicletas m ON h.moto_id = m.id
                WHERE h.moto_id = :moto_id
                ORDER BY h.data_inicio DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':moto_id' => $motoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar histórico: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca o histórico de um proprietário
     * 
     * @param int $proprietarioId ID do proprietário
     * @return array Lista de registros do histórico
     */
    public function buscarHistoricoProprietario($proprietarioId) {
        $sql = "SELECT h.*, 
                       m.placa as moto_placa,
                       m.marca as moto_marca,
                       m.modelo as moto_modelo
                FROM historico_proprietarios h
                JOIN motocicletas m ON h.moto_id = m.id
                WHERE h.proprietario_id = :proprietario_id
                ORDER BY h.data_inicio DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':proprietario_id' => $proprietarioId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar histórico: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca o proprietário atual de uma motocicleta
     * 
     * @param int $motoId ID da motocicleta
     * @return array|null Dados do proprietário atual ou null se não encontrado
     */
    public function buscarProprietarioAtual($motoId) {
        $sql = "SELECT h.*, p.*
                FROM historico_proprietarios h
                JOIN proprietarios p ON h.proprietario_id = p.id
                WHERE h.moto_id = :moto_id
                AND h.data_fim IS NULL
                ORDER BY h.data_inicio DESC
                LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':moto_id' => $motoId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar proprietário atual: " . $e->getMessage());
            return null;
        }
    }
} 