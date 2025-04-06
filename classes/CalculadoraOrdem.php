<?php
class CalculadoraOrdem {
    private $conn;
    private $ordem_id;
    private $totais;

    public function __construct($conn, $ordem_id) {
        $this->conn = $conn;
        $this->ordem_id = $ordem_id;
        $this->totais = [
            'pecas' => 0,
            'servicos' => 0,
            'adiantamentos' => 0,
            'total' => 0,
            'saldo' => 0
        ];
        $this->calcularTotais();
    }

    /**
     * Calcula os totais da ordem de serviço
     */
    private function calcularTotais() {
        $query = "SELECT 
                    Categoria,
                    SUM(Quantidade * Valor) as total
                 FROM item_ordem 
                 WHERE Ordem = ?
                 GROUP BY Categoria";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $this->ordem_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            switch ($row['Categoria']) {
                case 1: // Serviços
                    $this->totais['servicos'] = $row['total'];
                    break;
                case 2: // Peças
                    $this->totais['pecas'] = $row['total'];
                    break;
                case 3: // Adiantamentos
                    $this->totais['adiantamentos'] = $row['total'];
                    break;
            }
        }

        $this->totais['total'] = $this->totais['pecas'] + $this->totais['servicos'];
        $this->totais['saldo'] = $this->totais['total'] - $this->totais['adiantamentos'];
    }

    /**
     * Retorna os totais calculados
     */
    public function getTotais() {
        return $this->totais;
    }

    /**
     * Calcula o valor total de um item específico
     */
    public static function calcularValorItem($quantidade, $valor_unitario) {
        return $quantidade * $valor_unitario;
    }

    /**
     * Atualiza os valores de um item
     */
    public function atualizarValorItem($item_id, $quantidade, $valor_unitario) {
        $query = "UPDATE item_ordem 
                 SET Quantidade = ?, 
                     Valor = ?
                 WHERE itemId = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ddi', $quantidade, $valor_unitario, $item_id);
        
        if ($stmt->execute()) {
            $this->calcularTotais();
            return true;
        }
        
        return false;
    }

    /**
     * Formata um valor monetário
     */
    public static function formatarMoeda($valor) {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}