<?php

/**
 * Classe responsável por gerar PDFs de Ordens de Serviço
 */
class OrdemServicoPDF {
    private $pdf;
    private $ordem;
    private $conn;

    /**
     * Construtor da classe
     * 
     * @param array $ordem Dados da ordem de serviço
     * @param mysqli $conn Conexão com o banco de dados
     */
    public function __construct($ordem, $conn) {
        require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $this->ordem = $ordem;
        $this->conn = $conn;
        
        // Inicializa o PDF
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        // Configura o documento
        $this->setupDocument();
    }

    /**
     * Configura as propriedades básicas do documento
     */
    private function setupDocument() {
        // Informações do documento
        $this->pdf->SetCreator('SubRider');
        $this->pdf->SetAuthor('SubRider System');
        $this->pdf->SetTitle('Ordem de Serviço #' . $this->ordem['id']);

        // Remove cabeçalho e rodapé padrão
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        // Configura margens
        $this->pdf->SetMargins(15, 15, 15);

        // Adiciona primeira página
        $this->pdf->AddPage();
    }

    /**
     * Gera o cabeçalho da OS
     */
    private function gerarCabecalho() {
        // Logo da empresa
        $this->pdf->Image('assets/images/logo.png', 15, 15, 50);
        
        // Informações da empresa
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'ORDEM DE SERVIÇO #' . $this->ordem['id'], 0, 1, 'R');
        
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(0, 5, 'Data: ' . date('d/m/Y', strtotime($this->ordem['data_criacao'])), 0, 1, 'R');
        
        // Linha separadora
        $this->pdf->Line(15, 45, 195, 45);
    }

    /**
     * Gera a seção de informações do cliente e da moto
     */
    private function gerarInformacoesClienteMoto() {
        $this->pdf->SetY(50);
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Informações do Cliente', 0, 1, 'L');
        
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(50, 5, 'Nome: ' . $this->ordem['nome_cliente'], 0, 1);
        $this->pdf->Cell(50, 5, 'CPF: ' . $this->ordem['cpf_cliente'], 0, 1);
        $this->pdf->Cell(50, 5, 'Telefone: ' . $this->ordem['telefone_cliente'], 0, 1);
        
        $this->pdf->Ln(5);
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Informações da Moto', 0, 1, 'L');
        
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(50, 5, 'Marca: ' . $this->ordem['marca_moto'], 0, 1);
        $this->pdf->Cell(50, 5, 'Modelo: ' . $this->ordem['modelo_moto'], 0, 1);
        $this->pdf->Cell(50, 5, 'Placa: ' . $this->ordem['placa_moto'], 0, 1);
    }

    /**
     * Gera a seção de serviços realizados
     */
    private function gerarServicos() {
        $this->pdf->Ln(10);
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Serviços Realizados', 0, 1, 'L');
        
        // Cabeçalho da tabela
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(100, 7, 'Descrição', 1);
        $this->pdf->Cell(30, 7, 'Quantidade', 1);
        $this->pdf->Cell(30, 7, 'Valor Unit.', 1);
        $this->pdf->Cell(30, 7, 'Total', 1);
        $this->pdf->Ln();
        
        // Dados dos serviços
        $this->pdf->SetFont('helvetica', '', 10);
        foreach ($this->ordem['servicos'] as $servico) {
            $this->pdf->Cell(100, 6, $servico['descricao'], 1);
            $this->pdf->Cell(30, 6, $servico['quantidade'], 1, 0, 'C');
            $this->pdf->Cell(30, 6, 'R$ ' . number_format($servico['valor_unitario'], 2, ',', '.'), 1, 0, 'R');
            $this->pdf->Cell(30, 6, 'R$ ' . number_format($servico['valor_total'], 2, ',', '.'), 1, 0, 'R');
            $this->pdf->Ln();
        }
    }

    /**
     * Gera a seção de totais e observações
     */
    private function gerarTotaisObservacoes() {
        $this->pdf->Ln(10);
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(160, 7, 'Total:', 0, 0, 'R');
        $this->pdf->Cell(30, 7, 'R$ ' . number_format($this->ordem['valor_total'], 2, ',', '.'), 0, 1, 'R');
        
        if (!empty($this->ordem['observacoes'])) {
            $this->pdf->Ln(5);
            $this->pdf->SetFont('helvetica', 'B', 12);
            $this->pdf->Cell(0, 10, 'Observações:', 0, 1, 'L');
            $this->pdf->SetFont('helvetica', '', 10);
            $this->pdf->MultiCell(0, 5, $this->ordem['observacoes'], 0, 'L');
        }
    }

    /**
     * Gera a seção de assinaturas
     */
    private function gerarAssinaturas() {
        $this->pdf->Ln(20);
        $this->pdf->Line(30, $this->pdf->GetY(), 90, $this->pdf->GetY());
        $this->pdf->Line(120, $this->pdf->GetY(), 180, $this->pdf->GetY());
        
        $this->pdf->Ln(5);
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(90, 5, 'Assinatura do Cliente', 0, 0, 'C');
        $this->pdf->Cell(90, 5, 'Assinatura do Responsável', 0, 1, 'C');
    }

    /**
     * Gera o PDF completo da ordem de serviço
     * 
     * @return string Nome do arquivo PDF gerado
     */
    public function gerarPDF() {
        $this->gerarCabecalho();
        $this->gerarInformacoesClienteMoto();
        $this->gerarServicos();
        $this->gerarTotaisObservacoes();
        $this->gerarAssinaturas();
        
        // Gera o nome do arquivo
        $nomeArquivo = 'OS_' . $this->ordem['id'] . '_' . date('YmdHis') . '.pdf';
        
        // Salva o PDF
        $this->pdf->Output(__DIR__ . '/../pdf/' . $nomeArquivo, 'F');
        
        return $nomeArquivo;
    }
} 