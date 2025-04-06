<?php
require_once __DIR__ . '/../connection/BaseRepository.php';

/**
 * Repositório para gerenciar peças
 */
class PecaRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('pecas');
    }
    
    /**
     * Busca peças por grupo
     * 
     * @param string $grupo Grupo de peças
     * @return array Lista de peças
     */
    public function findByGrupo($grupo) {
        $sql = "SELECT * FROM {$this->table} WHERE grupo = ? ORDER BY item";
        $result = $this->db->query($sql, [$grupo]);
        
        $pecas = [];
        while ($row = $result->fetch_assoc()) {
            $pecas[] = $row;
        }
        return $pecas;
    }
    
    /**
     * Lista todos os grupos distintos
     * 
     * @return array Lista de grupos
     */
    public function listGrupos() {
        $sql = "SELECT DISTINCT grupo FROM {$this->table} ORDER BY grupo";
        $result = $this->db->query($sql);
        
        $grupos = [];
        while ($row = $result->fetch_assoc()) {
            $grupos[] = $row['grupo'];
        }
        return $grupos;
    }
    
    /**
     * Busca peças por item (nome)
     * 
     * @param string $item Nome do item
     * @return array Lista de peças
     */
    public function findByItem($item) {
        $sql = "SELECT * FROM {$this->table} WHERE item LIKE ? ORDER BY grupo, item";
        $result = $this->db->query($sql, ["%{$item}%"]);
        
        $pecas = [];
        while ($row = $result->fetch_assoc()) {
            $pecas[] = $row;
        }
        return $pecas;
    }
    
    /**
     * Atualiza a foto de uma peça
     * 
     * @param int $id ID da peça
     * @param string $foto Caminho da nova foto
     * @return bool Sucesso da operação
     */
    public function updateFoto($id, $foto) {
        return $this->update($id, ['foto' => $foto]);
    }
    
    /**
     * Busca peças por parte específica
     * 
     * @param string $parte Nome da parte
     * @return array Lista de peças
     */
    public function findByParte($parte) {
        $sql = "SELECT * FROM {$this->table} WHERE parte LIKE ? ORDER BY grupo, item";
        $result = $this->db->query($sql, ["%{$parte}%"]);
        
        $pecas = [];
        while ($row = $result->fetch_assoc()) {
            $pecas[] = $row;
        }
        return $pecas;
    }
    
    /**
     * Busca peças por grupo e item
     * 
     * @param string $grupo Grupo de peças
     * @param string $item Nome do item
     * @return array Lista de peças
     */
    public function findByGrupoAndItem($grupo, $item) {
        $sql = "SELECT * FROM {$this->table} WHERE grupo = ? AND item LIKE ? ORDER BY parte";
        $result = $this->db->query($sql, [$grupo, "%{$item}%"]);
        
        $pecas = [];
        while ($row = $result->fetch_assoc()) {
            $pecas[] = $row;
        }
        return $pecas;
    }
    
    /**
     * Lista todas as partes distintas de um grupo
     * 
     * @param string $grupo Grupo de peças
     * @return array Lista de partes
     */
    public function listPartesByGrupo($grupo) {
        $sql = "SELECT DISTINCT parte FROM {$this->table} WHERE grupo = ? ORDER BY parte";
        $result = $this->db->query($sql, [$grupo]);
        
        $partes = [];
        while ($row = $result->fetch_assoc()) {
            $partes[] = $row['parte'];
        }
        return $partes;
    }
    
    /**
     * Verifica se uma peça já existe com o mesmo grupo, item e parte
     * 
     * @param string $grupo Grupo da peça
     * @param string $item Nome do item
     * @param string $parte Nome da parte
     * @param int $excludeId ID da peça a ser excluída da verificação (opcional)
     * @return bool True se a peça já existe
     */
    public function exists($grupo, $item, $parte, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE grupo = ? AND item = ? AND parte = ?";
        $params = [$grupo, $item, $parte];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->query($sql, $params);
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
}