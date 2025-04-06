<?php
/**
 * Classe responsável por gerenciar o histórico de alterações de motos
 */
class HistoricoMoto {
    private $conn;
    private $moto_id;
    private $usuario_id;

    /**
     * Construtor da classe
     * 
     * @param mysqli $conn Conexão com o banco de dados
     * @param int $moto_id ID da moto
     * @param int $usuario_id ID do usuário que está fazendo a alteração
     */
    public function __construct($conn, $moto_id, $usuario_id) {
        $this->conn = $conn;
        $this->moto_id = $moto_id;
        $this->usuario_id = $usuario_id;
    }

    /**
     * Registra uma alteração no histórico
     * 
     * @param string $campo Campo que foi alterado
     * @param mixed $valor_antigo Valor anterior do campo
     * @param mixed $valor_novo Novo valor do campo
     * @return bool True se o registro foi criado com sucesso
     */
    public function registrarAlteracao($campo, $valor_antigo, $valor_novo) {
        $sql = "INSERT INTO historico_motos (moto_id, campo_alterado, valor_antigo, valor_novo, usuario_id) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('isssi', 
            $this->moto_id, 
            $campo, 
            $valor_antigo, 
            $valor_novo, 
            $this->usuario_id
        );
        
        return $stmt->execute();
    }

    /**
     * Busca o histórico de alterações de uma moto
     * 
     * @param int $limit Limite de registros (opcional)
     * @param int $offset Offset para paginação (opcional)
     * @return array Array com os registros do histórico
     */
    public function buscarHistorico($limit = null, $offset = 0) {
        $sql = "SELECT h.*, u.nome as usuario_nome 
                FROM historico_motos h
                INNER JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.moto_id = ?
                ORDER BY h.data_alteracao DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if ($limit !== null) {
            $stmt->bind_param('iii', $this->moto_id, $limit, $offset);
        } else {
            $stmt->bind_param('i', $this->moto_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $historico = [];
        while ($row = $result->fetch_assoc()) {
            $historico[] = $row;
        }
        
        return $historico;
    }

    /**
     * Conta o total de registros no histórico de uma moto
     * 
     * @return int Total de registros
     */
    public function contarRegistros() {
        $sql = "SELECT COUNT(*) as total FROM historico_motos WHERE moto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $this->moto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    /**
     * Formata o valor para exibição
     * 
     * @param string $campo Nome do campo
     * @param mixed $valor Valor a ser formatado
     * @return string Valor formatado
     */
    public function formatarValor($campo, $valor) {
        switch ($campo) {
            case 'km':
                return number_format($valor, 0, ',', '.') . ' km';
            case 'valor':
                return 'R$ ' . number_format($valor, 2, ',', '.');
            case 'data_aquisicao':
            case 'data_venda':
                return date('d/m/Y', strtotime($valor));
            default:
                return $valor;
        }
    }
} 