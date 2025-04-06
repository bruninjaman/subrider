<?php
class StatusOrdem {
    private $conn;
    private $ordem_id;

    public function __construct($conn, $ordem_id) {
        $this->conn = $conn;
        $this->ordem_id = $ordem_id;
    }

    /**
     * Atualiza o status de uma ordem de serviço
     * 
     * @param string $novo_status O novo status da ordem
     * @param string $observacao Observação opcional sobre a mudança de status
     * @return bool Retorna true se a atualização foi bem sucedida
     */
    public function atualizarStatus($novo_status, $observacao = '') {
        $status_permitidos = ['Em Andamento', 'Concluída', 'Cancelada', 'Aguardando Peças', 'Aguardando Aprovação'];
        
        if (!in_array($novo_status, $status_permitidos)) {
            return false;
        }

        $query = "UPDATE ordem_servicos SET status = ? WHERE Codigo = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $novo_status, $this->ordem_id);
        
        if ($stmt->execute()) {
            // Registra a mudança de status no histórico
            $this->registrarHistorico($novo_status, $observacao);
            return true;
        }
        
        return false;
    }

    /**
     * Registra mudança de status no histórico
     */
    private function registrarHistorico($status, $observacao) {
        $query = "INSERT INTO historico_status (ordem_id, status, observacao, data_mudanca, usuario) 
                 VALUES (?, ?, ?, NOW(), ?)";
        
        $stmt = $this->conn->prepare($query);
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Sistema';
        $stmt->bind_param('ssss', $this->ordem_id, $status, $observacao, $usuario);
        $stmt->execute();
    }

    /**
     * Obtém o status atual da ordem
     */
    public function getStatus() {
        $query = "SELECT status FROM ordem_servicos WHERE Codigo = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $this->ordem_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['status'];
        }
        
        return null;
    }

    /**
     * Obtém o histórico de status da ordem
     */
    public function getHistorico() {
        $query = "SELECT * FROM historico_status WHERE ordem_id = ? ORDER BY data_mudanca DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $this->ordem_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}