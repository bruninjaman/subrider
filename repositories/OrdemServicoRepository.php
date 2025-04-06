<?php
require_once __DIR__ . '/../connection/BaseRepository.php';

/**
 * Repositório para gerenciar ordens de serviço
 */
class OrdemServicoRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('ordem_servicos');
    }
    
    /**
     * Busca uma ordem de serviço pelo código
     * 
     * @param string $codigo Código da ordem de serviço
     * @return array|null Dados da ordem ou null se não encontrada
     */
    public function findByCodigo($codigo) {
        $sql = "SELECT * FROM {$this->table} WHERE Codigo = ?";
        $result = $this->db->query($sql, [$codigo]);
        return $result ? $result->fetch_assoc() : null;
    }
    
    /**
     * Busca ordens de serviço por motocicleta
     * 
     * @param int $motoId ID da motocicleta
     * @return array Lista de ordens de serviço
     */
    public function findByMoto($motoId) {
        $sql = "SELECT * FROM {$this->table} WHERE motoID = ? ORDER BY data_entrada DESC";
        $result = $this->db->query($sql, [$motoId]);
        
        $ordens = [];
        while ($row = $result->fetch_assoc()) {
            $ordens[] = $row;
        }
        return $ordens;
    }
    
    /**
     * Busca ordens de serviço por período
     * 
     * @param string $dataInicio Data inicial (Y-m-d)
     * @param string $dataFim Data final (Y-m-d)
     * @return array Lista de ordens de serviço
     */
    public function findByPeriodo($dataInicio, $dataFim) {
        $sql = "SELECT os.*, m.marca, m.modelo, m.placa 
                FROM {$this->table} os 
                LEFT JOIN motocicletas m ON m.motoId = os.motoID 
                WHERE os.data_entrada BETWEEN ? AND ?
                ORDER BY os.data_entrada DESC";
                
        $result = $this->db->query($sql, [$dataInicio, $dataFim]);
        
        $ordens = [];
        while ($row = $result->fetch_assoc()) {
            $ordens[] = $row;
        }
        return $ordens;
    }
    
    /**
     * Busca ordens de serviço por status
     * 
     * @param string $status Status da ordem
     * @return array Lista de ordens de serviço
     */
    public function findByStatus($status) {
        $sql = "SELECT os.*, m.marca, m.modelo, m.placa 
                FROM {$this->table} os 
                LEFT JOIN motocicletas m ON m.motoId = os.motoID 
                WHERE os.status = ?
                ORDER BY os.data_entrada DESC";
                
        $result = $this->db->query($sql, [$status]);
        
        $ordens = [];
        while ($row = $result->fetch_assoc()) {
            $ordens[] = $row;
        }
        return $ordens;
    }
    
    /**
     * Atualiza o status de uma ordem de serviço
     * 
     * @param int $id ID da ordem
     * @param string $status Novo status
     * @param string $observacao Observação opcional
     * @return bool Sucesso da operação
     */
    public function updateStatus($id, $status, $observacao = null) {
        $data = ['status' => $status];
        if ($observacao !== null) {
            $data['observacao'] = $observacao;
        }
        return $this->update($id, $data);
    }
    
    /**
     * Gera um novo código único para ordem de serviço
     * 
     * @return string Código gerado
     */
    public function generateCodigo() {
        $sql = "SELECT MAX(CAST(Codigo AS UNSIGNED)) as ultimo FROM {$this->table}";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        
        $ultimo = (int)$row['ultimo'];
        return str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Busca ordens de serviço com itens e valores totais
     * 
     * @param array $filters Filtros opcionais
     * @return array Lista de ordens com totais
     */
    public function findWithTotals($filters = []) {
        $sql = "SELECT os.*, m.marca, m.modelo, m.placa,
                       COUNT(i.id) as total_itens,
                       SUM(i.Valor * i.Quantidade) as valor_total
                FROM {$this->table} os 
                LEFT JOIN motocicletas m ON m.motoId = os.motoID
                LEFT JOIN item_ordem i ON i.Ordem = os.Codigo";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['status'])) {
            $where[] = "os.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['data_inicio'])) {
            $where[] = "os.data_entrada >= ?";
            $params[] = $filters['data_inicio'];
        }
        
        if (!empty($filters['data_fim'])) {
            $where[] = "os.data_entrada <= ?";
            $params[] = $filters['data_fim'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " GROUP BY os.id ORDER BY os.data_entrada DESC";
        
        $result = $this->db->query($sql, $params);
        
        $ordens = [];
        while ($row = $result->fetch_assoc()) {
            $ordens[] = $row;
        }
        return $ordens;
    }
    
    /**
     * Adiciona um item à ordem de serviço
     * 
     * @param string $codigo Código da ordem
     * @param array $item Dados do item
     * @return bool|int ID do item inserido ou false em caso de erro
     */
    public function addItem($codigo, $item) {
        try {
            $this->db->beginTransaction();
            
            // Verifica se a ordem existe
            $ordem = $this->findByCodigo($codigo);
            if (!$ordem) {
                throw new Exception("Ordem não encontrada");
            }
            
            // Prepara os dados do item
            $item['Ordem'] = $codigo;
            
            // Insere o item
            $sql = "INSERT INTO item_ordem (Foto, Grupo, Tipo, Item, Parte, Quantidade, Valor, Descricao, Ordem, Categoria) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
            $params = [
                $item['Foto'] ?? '',
                $item['Grupo'] ?? '',
                $item['Tipo'] ?? '',
                $item['Item'],
                $item['Parte'] ?? '',
                $item['Quantidade'],
                $item['Valor'],
                $item['Descricao'] ?? '',
                $codigo,
                $item['Categoria']
            ];
            
            $result = $this->db->query($sql, $params);
            
            if ($result) {
                $itemId = $this->db->getConnection()->insert_id;
                $this->db->commit();
                return $itemId;
            }
            
            $this->db->rollback();
            return false;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Remove um item da ordem de serviço
     * 
     * @param int $itemId ID do item
     * @param string $codigo Código da ordem
     * @return bool Sucesso da operação
     */
    public function removeItem($itemId, $codigo) {
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM item_ordem WHERE id = ? AND Ordem = ?";
            $result = $this->db->query($sql, [$itemId, $codigo]);
            
            if ($result) {
                $this->db->commit();
                return true;
            }
            
            $this->db->rollback();
            return false;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Lista todos os itens de uma ordem de serviço
     * 
     * @param string $codigo Código da ordem
     * @return array Lista de itens
     */
    public function listItems($codigo) {
        $sql = "SELECT * FROM item_ordem WHERE Ordem = ? ORDER BY id";
        $result = $this->db->query($sql, [$codigo]);
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }
}