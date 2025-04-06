<?php
/**
 * Classe responsável por gerar relatórios personalizados
 */
class RelatorioPersonalizado {
    private $conn;
    private $filtros;
    private $campos;
    private $agrupamento;
    private $ordenacao;

    /**
     * Construtor da classe
     * 
     * @param mysqli $conn Conexão com o banco de dados
     */
    public function __construct($conn) {
        $this->conn = $conn;
        $this->resetar();
    }

    /**
     * Reseta as configurações do relatório
     */
    public function resetar() {
        $this->filtros = [];
        $this->campos = [];
        $this->agrupamento = [];
        $this->ordenacao = [];
    }

    /**
     * Adiciona um campo ao relatório
     * 
     * @param string $tabela Nome da tabela
     * @param string $campo Nome do campo
     * @param string $alias Alias para o campo (opcional)
     * @param string $funcao Função de agregação (opcional)
     * @return RelatorioPersonalizado
     */
    public function adicionarCampo($tabela, $campo, $alias = null, $funcao = null) {
        $campo_sql = $tabela . '.' . $campo;
        if ($funcao) {
            $campo_sql = $funcao . '(' . $campo_sql . ')';
        }
        if ($alias) {
            $campo_sql .= ' AS ' . $alias;
        }
        $this->campos[] = $campo_sql;
        return $this;
    }

    /**
     * Adiciona um filtro ao relatório
     * 
     * @param string $tabela Nome da tabela
     * @param string $campo Nome do campo
     * @param string $operador Operador de comparação
     * @param mixed $valor Valor para comparação
     * @return RelatorioPersonalizado
     */
    public function adicionarFiltro($tabela, $campo, $operador, $valor) {
        $this->filtros[] = [
            'campo' => $tabela . '.' . $campo,
            'operador' => $operador,
            'valor' => $valor
        ];
        return $this;
    }

    /**
     * Adiciona um campo para agrupamento
     * 
     * @param string $tabela Nome da tabela
     * @param string $campo Nome do campo
     * @return RelatorioPersonalizado
     */
    public function adicionarAgrupamento($tabela, $campo) {
        $this->agrupamento[] = $tabela . '.' . $campo;
        return $this;
    }

    /**
     * Adiciona um campo para ordenação
     * 
     * @param string $tabela Nome da tabela
     * @param string $campo Nome do campo
     * @param string $direcao ASC ou DESC
     * @return RelatorioPersonalizado
     */
    public function adicionarOrdenacao($tabela, $campo, $direcao = 'ASC') {
        $this->ordenacao[] = $tabela . '.' . $campo . ' ' . $direcao;
        return $this;
    }

    /**
     * Gera o SQL do relatório
     * 
     * @return string SQL gerado
     */
    private function gerarSQL() {
        // Campos
        $campos = implode(', ', $this->campos);
        
        // Tabelas (extraídas dos campos)
        $tabelas = [];
        foreach ($this->campos as $campo) {
            if (preg_match('/^([a-zA-Z_]+)\./', $campo, $matches)) {
                $tabelas[] = $matches[1];
            }
        }
        $tabelas = array_unique($tabelas);
        
        // Construir SQL base
        $sql = "SELECT $campos FROM " . array_shift($tabelas);
        
        // Joins (assumindo que existe uma relação entre as tabelas)
        foreach ($tabelas as $tabela) {
            $sql .= " LEFT JOIN $tabela ON 1=1";
        }
        
        // Filtros
        if (!empty($this->filtros)) {
            $where = [];
            foreach ($this->filtros as $filtro) {
                $valor = is_string($filtro['valor']) ? "'" . $this->conn->real_escape_string($filtro['valor']) . "'" : $filtro['valor'];
                $where[] = "{$filtro['campo']} {$filtro['operador']} $valor";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        // Agrupamento
        if (!empty($this->agrupamento)) {
            $sql .= " GROUP BY " . implode(', ', $this->agrupamento);
        }
        
        // Ordenação
        if (!empty($this->ordenacao)) {
            $sql .= " ORDER BY " . implode(', ', $this->ordenacao);
        }
        
        return $sql;
    }

    /**
     * Executa o relatório e retorna os resultados
     * 
     * @return array Resultados do relatório
     */
    public function executar() {
        $sql = $this->gerarSQL();
        $result = $this->conn->query($sql);
        
        if (!$result) {
            throw new Exception("Erro ao executar relatório: " . $this->conn->error);
        }
        
        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
        
        return $dados;
    }

    /**
     * Exporta os resultados para CSV
     * 
     * @param string $arquivo Nome do arquivo
     * @return string Caminho do arquivo gerado
     */
    public function exportarCSV($arquivo) {
        $dados = $this->executar();
        
        if (empty($dados)) {
            throw new Exception("Nenhum dado encontrado para exportar");
        }
        
        $arquivo = __DIR__ . '/../relatorios/' . $arquivo;
        $fp = fopen($arquivo, 'w');
        
        // Cabeçalho
        fputcsv($fp, array_keys($dados[0]));
        
        // Dados
        foreach ($dados as $linha) {
            fputcsv($fp, $linha);
        }
        
        fclose($fp);
        
        return $arquivo;
    }

    /**
     * Exporta os resultados para PDF
     * 
     * @param string $arquivo Nome do arquivo
     * @param string $titulo Título do relatório
     * @return string Caminho do arquivo gerado
     */
    public function exportarPDF($arquivo, $titulo) {
        require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $dados = $this->executar();
        
        if (empty($dados)) {
            throw new Exception("Nenhum dado encontrado para exportar");
        }
        
        // Cria o PDF
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('SubRider');
        $pdf->SetAuthor('SubRider System');
        $pdf->SetTitle($titulo);
        
        // Remove cabeçalho e rodapé padrão
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Adiciona página
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
        $pdf->Ln(5);
        
        // Cabeçalho da tabela
        $pdf->SetFont('helvetica', 'B', 10);
        $colunas = array_keys($dados[0]);
        $larguras = array_fill(0, count($colunas), 180/count($colunas));
        
        foreach ($colunas as $i => $coluna) {
            $pdf->Cell($larguras[$i], 7, $coluna, 1);
        }
        $pdf->Ln();
        
        // Dados
        $pdf->SetFont('helvetica', '', 10);
        foreach ($dados as $linha) {
            foreach ($linha as $i => $valor) {
                $pdf->Cell($larguras[$i], 6, $valor, 1);
            }
            $pdf->Ln();
        }
        
        // Salva o arquivo
        $arquivo = __DIR__ . '/../relatorios/' . $arquivo;
        $pdf->Output($arquivo, 'F');
        
        return $arquivo;
    }
} 