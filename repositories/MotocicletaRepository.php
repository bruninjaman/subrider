<?php
require_once __DIR__ . '/../connection/BaseRepository.php';
require_once __DIR__ . '/HistoricoProprietarioRepository.php';

/**
 * Repositório para gerenciar motocicletas
 */
class MotocicletaRepository extends BaseRepository {
    private $historicoRepository;
    
    public function __construct() {
        parent::__construct('motocicletas');
        $this->historicoRepository = new HistoricoProprietarioRepository();
    }
    
    /**
     * Busca uma motocicleta pela placa
     * 
     * @param string $placa Placa da motocicleta
     * @return array|null Dados da motocicleta ou null se não encontrada
     */
    public function findByPlaca($placa) {
        $sql = "SELECT m.*, p.nome as proprietario_nome 
                FROM {$this->table} m 
                LEFT JOIN proprietarios p ON m.proprietario_id = p.id 
                WHERE m.placa = ?";
        $result = $this->db->query($sql, [$placa]);
        return $result ? $result->fetch_assoc() : null;
    }
    
    /**
     * Busca motocicletas por proprietário
     * 
     * @param int $proprietarioId ID do proprietário
     * @return array Lista de motocicletas
     */
    public function findByProprietarioId($proprietarioId) {
        $sql = "SELECT * FROM {$this->table} WHERE proprietario_id = ?";
        $result = $this->db->query($sql, [$proprietarioId]);
        
        $motos = [];
        while ($row = $result->fetch_assoc()) {
            $motos[] = $row;
        }
        return $motos;
    }
    
    /**
     * Atualiza o proprietário de uma motocicleta
     * 
     * @param int $motoId ID da motocicleta
     * @param int $proprietarioId ID do novo proprietário
     * @param string $observacao Observação opcional
     * @return bool Sucesso da operação
     */
    public function updateProprietario($motoId, $proprietarioId, $observacao = '') {
        try {
            // Atualiza o proprietário na tabela de motocicletas
            $success = $this->update($motoId, ['proprietario_id' => $proprietarioId]);
            
            if ($success) {
                // Registra no histórico
                return $this->historicoRepository->registrarTransferencia(
                    $motoId, 
                    $proprietarioId, 
                    $observacao
                );
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erro ao atualizar proprietário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca o histórico de proprietários de uma motocicleta
     * 
     * @param int $motoId ID da motocicleta
     * @return array Histórico de proprietários
     */
    public function getHistoricoProprietarios($motoId) {
        return $this->historicoRepository->buscarHistoricoMoto($motoId);
    }
    
    /**
     * Busca o proprietário atual de uma motocicleta
     * 
     * @param int $motoId ID da motocicleta
     * @return array|null Dados do proprietário atual
     */
    public function getProprietarioAtual($motoId) {
        return $this->historicoRepository->buscarProprietarioAtual($motoId);
    }
    
    /**
     * Busca motocicletas por marca e modelo
     * 
     * @param string $marca Marca da motocicleta
     * @param string $modelo Modelo da motocicleta
     * @return array Lista de motocicletas
     */
    public function findByMarcaModelo($marca, $modelo) {
        $sql = "SELECT * FROM {$this->table} WHERE marca = ? AND modelo = ?";
        $result = $this->db->query($sql, [$marca, $modelo]);
        
        $motos = [];
        while ($row = $result->fetch_assoc()) {
            $motos[] = $row;
        }
        return $motos;
    }
    
    /**
     * Atualiza a quilometragem de uma motocicleta
     * 
     * @param int $id ID da motocicleta
     * @param int $km Nova quilometragem
     * @return bool Sucesso da operação
     */
    public function updateKM($id, $km) {
        return $this->update($id, ['KM' => $km]);
    }
    
    /**
     * Atualiza a foto de uma motocicleta
     * 
     * @param int $id ID da motocicleta
     * @param string $foto Caminho da nova foto
     * @return bool Sucesso da operação
     */
    public function updateFoto($id, $foto) {
        return $this->update($id, ['foto' => $foto]);
    }
    
    /**
     * Busca motocicletas com quilometragem maior que a especificada
     * 
     * @param int $km Quilometragem mínima
     * @return array Lista de motocicletas
     */
    public function findByKMGreaterThan($km) {
        $sql = "SELECT * FROM {$this->table} WHERE KM > ? ORDER BY KM DESC";
        $result = $this->db->query($sql, [$km]);
        
        $motos = [];
        while ($row = $result->fetch_assoc()) {
            $motos[] = $row;
        }
        return $motos;
    }
    
    /**
     * Busca motocicletas por ano
     * 
     * @param int $ano Ano da motocicleta
     * @return array Lista de motocicletas
     */
    public function findByAno($ano) {
        $sql = "SELECT * FROM {$this->table} WHERE ano = ? ORDER BY modelo";
        $result = $this->db->query($sql, [$ano]);
        
        $motos = [];
        while ($row = $result->fetch_assoc()) {
            $motos[] = $row;
        }
        return $motos;
    }
    
    /**
     * Lista todas as marcas distintas cadastradas
     * 
     * @return array Lista de marcas
     */
    public function listMarcas() {
        $sql = "SELECT DISTINCT marca FROM {$this->table} ORDER BY marca";
        $result = $this->db->query($sql);
        
        $marcas = [];
        while ($row = $result->fetch_assoc()) {
            $marcas[] = $row['marca'];
        }
        return $marcas;
    }
    
    /**
     * Lista todos os modelos de uma marca
     * 
     * @param string $marca Marca da motocicleta
     * @return array Lista de modelos
     */
    public function listModelosByMarca($marca) {
        $sql = "SELECT DISTINCT modelo FROM {$this->table} WHERE marca = ? ORDER BY modelo";
        $result = $this->db->query($sql, [$marca]);
        
        $modelos = [];
        while ($row = $result->fetch_assoc()) {
            $modelos[] = $row['modelo'];
        }
        return $modelos;
    }
    
    /**
     * Verifica se uma placa já está cadastrada
     * 
     * @param string $placa Placa a ser verificada
     * @param int $excludeId ID da motocicleta a ser excluída da verificação (opcional)
     * @return bool True se a placa já existe
     */
    public function placaExists($placa, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE placa = ?";
        $params = [$placa];
        
        if ($excludeId !== null) {
            $sql .= " AND motoId != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->query($sql, $params);
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
}