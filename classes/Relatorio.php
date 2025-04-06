<?php
/**
 * Classe para gerenciamento de relatórios
 */
class Relatorio {
    private $conn;
    private $ordem;
    private $data;
    
    public function __construct($conn, $ordem) {
        $this->conn = $conn;
        $this->ordem = $ordem;
        $this->carregarDados();
    }
    
    /**
     * Carrega todos os dados necessários para o relatório
     */
    private function carregarDados() {
        // Informações da ordem
        $sql = "SELECT os.*, m.modelo, m.placa, m.marca, m.ano, p.nome as proprietario, p.telefone, p.email 
                FROM ordem_servicos os
                JOIN motocicletas m ON os.motoID = m.motoId
                JOIN proprietarios p ON m.proprietario = p.id
                WHERE os.Codigo = ?";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->ordem);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->data['ordem'] = $result->fetch_assoc();
        
        // Itens da ordem
        $sql = "SELECT * FROM item_ordem WHERE Ordem = ? ORDER BY Categoria, item_ordemID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->ordem);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $this->data['itens'] = [
            'pecas' => [],
            'servicos' => [],
            'adiantamentos' => []
        ];
        
        while ($item = $result->fetch_assoc()) {
            switch ($item['Categoria']) {
                case '1':
                    $this->data['itens']['servicos'][] = $item;
                    break;
                case '2':
                    $this->data['itens']['pecas'][] = $item;
                    break;
                case '3':
                    $this->data['itens']['adiantamentos'][] = $item;
                    break;
            }
        }
        
        // Medições
        $this->data['medicoes'] = $this->carregarMedicoes();
        
        // Totais
        $this->data['totais'] = $this->calcularTotais();
    }
    
    /**
     * Carrega todas as medições da ordem
     */
    private function carregarMedicoes() {
        $medicoes = [];
        $tipos = ['cabecote', 'bomba', 'motor', 'virabrequim', 'embreagem'];
        
        foreach ($tipos as $tipo) {
            $sql = "SELECT * FROM {$tipo} WHERE ordem = ? ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $this->ordem);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $medicoes[$tipo] = $row;
            }
        }
        
        return $medicoes;
    }
    
    /**
     * Calcula os totais da ordem
     */
    private function calcularTotais() {
        $totais = [
            'pecas' => 0,
            'servicos' => 0,
            'adiantamentos' => 0,
            'total' => 0
        ];
        
        foreach ($this->data['itens'] as $tipo => $items) {
            foreach ($items as $item) {
                $valor = $item['Valor'] * $item['Quantidade'];
                $totais[$tipo] += $valor;
                if ($tipo !== 'adiantamentos') {
                    $totais['total'] += $valor;
                }
            }
        }
        
        $totais['saldo'] = $totais['total'] - $totais['adiantamentos'];
        
        return $totais;
    }
    
    /**
     * Gera o HTML do relatório
     */
    public function gerarHTML() {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Relatório OS <?php echo $this->ordem; ?></title>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { text-align: center; margin-bottom: 20px; }
                .logo { max-width: 200px; }
                .info { margin-bottom: 20px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
                .section { margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { padding: 8px; border: 1px solid #ddd; }
                th { background-color: #f5f5f5; }
                .total { font-weight: bold; }
                .medicoes-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
                .medicao-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="assets/css/images/logo-branco-crop.png" alt="Subrider" class="logo">
                <h1>Relatório de Ordem de Serviço</h1>
                <h2>OS: <?php echo $this->ordem; ?></h2>
            </div>
            
            <div class="info">
                <div class="info-grid">
                    <div>
                        <h3>Informações da Moto</h3>
                        <p><strong>Modelo:</strong> <?php echo $this->data['ordem']['modelo']; ?></p>
                        <p><strong>Marca:</strong> <?php echo $this->data['ordem']['marca']; ?></p>
                        <p><strong>Ano:</strong> <?php echo $this->data['ordem']['ano']; ?></p>
                        <p><strong>Placa:</strong> <?php echo $this->data['ordem']['placa']; ?></p>
                        <p><strong>KM:</strong> <?php echo $this->data['ordem']['KM']; ?></p>
                    </div>
                    <div>
                        <h3>Informações do Cliente</h3>
                        <p><strong>Nome:</strong> <?php echo $this->data['ordem']['proprietario']; ?></p>
                        <p><strong>Telefone:</strong> <?php echo $this->data['ordem']['telefone']; ?></p>
                        <p><strong>Email:</strong> <?php echo $this->data['ordem']['email']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>Serviços Realizados</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Quantidade</th>
                            <th>Valor Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->data['itens']['servicos'] as $servico): ?>
                        <tr>
                            <td><?php echo $servico['Tipo'] . ' - ' . $servico['Item']; ?></td>
                            <td><?php echo $servico['Quantidade']; ?></td>
                            <td>R$ <?php echo number_format($servico['Valor'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($servico['Valor'] * $servico['Quantidade'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <h3>Peças Utilizadas</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Código</th>
                            <th>Quantidade</th>
                            <th>Valor Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->data['itens']['pecas'] as $peca): ?>
                        <tr>
                            <td><?php echo $peca['Grupo'] . ' - ' . $peca['Item'] . ($peca['Parte'] ? ' - ' . $peca['Parte'] : ''); ?></td>
                            <td><?php echo $peca['Codigo']; ?></td>
                            <td><?php echo $peca['Quantidade']; ?></td>
                            <td>R$ <?php echo number_format($peca['Valor'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($peca['Valor'] * $peca['Quantidade'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($this->data['medicoes'])): ?>
            <div class="section">
                <h3>Medições Realizadas</h3>
                <div class="medicoes-grid">
                    <?php foreach ($this->data['medicoes'] as $tipo => $medicao): ?>
                    <div class="medicao-card">
                        <h4><?php echo ucfirst($tipo); ?></h4>
                        <?php foreach ($medicao as $campo => $valor): ?>
                            <?php if ($campo !== 'id' && $campo !== 'ordem'): ?>
                            <p><strong><?php echo ucfirst(str_replace('_', ' ', $campo)); ?>:</strong> <?php echo $valor; ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="section">
                <h3>Resumo Financeiro</h3>
                <table>
                    <tr>
                        <td><strong>Total de Peças:</strong></td>
                        <td>R$ <?php echo number_format($this->data['totais']['pecas'], 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total de Serviços:</strong></td>
                        <td>R$ <?php echo number_format($this->data['totais']['servicos'], 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td>R$ <?php echo number_format($this->data['totais']['total'], 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Adiantamentos:</strong></td>
                        <td>R$ <?php echo number_format($this->data['totais']['adiantamentos'], 2, ',', '.'); ?></td>
                    </tr>
                    <tr class="total">
                        <td><strong>Saldo a Pagar:</strong></td>
                        <td>R$ <?php echo number_format($this->data['totais']['saldo'], 2, ',', '.'); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="section">
                <p style="text-align: center; margin-top: 50px;">
                    _____________________________________________<br>
                    Assinatura do Responsável
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Gera o PDF do relatório
     */
    public function gerarPDF() {
        require_once 'vendor/autoload.php';
        
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15
        ]);
        
        $mpdf->WriteHTML($this->gerarHTML());
        
        return $mpdf;
    }
    
    /**
     * Salva o relatório no banco de dados
     */
    public function salvar($observacoes = '') {
        $html = $this->gerarHTML();
        $data = date('Y-m-d H:i:s');
        $usuario = $_SESSION['user_id'] ?? 0;
        
        $sql = "INSERT INTO relatorios (ordem, html, data, usuario, observacoes) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssis", $this->ordem, $html, $data, $usuario, $observacoes);
        
        return $stmt->execute();
    }
}