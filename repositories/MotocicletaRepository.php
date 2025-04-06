<?php
require_once __DIR__ . '/../connection/BaseRepository.php';

/**
 * Repositório para gerenciar motocicletas
 */
class MotocicletaRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('motocicletas');
    }
    
    /**
     * Busca uma motocicleta pela placa
     * 
     * @param string $placa Placa da motocicleta
     * @return array|null Dados da motocicleta ou null se não encontrada
     */
    public function findByPlaca($placa) {
        $sql = "SELECT * FROM {$this->table} WHERE placa = ?";
        $result = $this->db->query($sql, [$placa]);
        return $result ? $result->fetch_assoc() : null;
    }
    
    /**
     * Busca motocicletas por proprietário
     * 
     * @param string $proprietario Nome do proprietário
     * @return array Lista de motocicletas
     */
    public function findByProprietario($proprietario) {
        $sql = "SELECT * FROM {$this->table} WHERE proprietario LIKE ?";
        $result = $this->db->query($sql, ["%{$proprietario}%"]);
        
        $motos = [];
        while ($row = $result->fetch_assoc()) {
            $motos[] = $row;
        }
        return $motos;
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