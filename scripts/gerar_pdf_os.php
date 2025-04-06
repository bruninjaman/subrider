<?php
session_start();
require_once '../config.php';
require_once '../classes/OrdemServicoPDF.php';

// Verifica se foi passado o ID da ordem
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID da ordem não informado');
}

$id = intval($_GET['id']);

// Busca os dados da ordem
$sql = "SELECT o.*, 
               c.nome as nome_cliente, 
               c.cpf as cpf_cliente, 
               c.telefone as telefone_cliente,
               m.marca as marca_moto,
               m.modelo as modelo_moto,
               m.placa as placa_moto
        FROM ordens_servico o
        INNER JOIN clientes c ON o.cliente_id = c.id
        INNER JOIN motos m ON o.moto_id = m.id
        WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Ordem não encontrada');
}

$ordem = $result->fetch_assoc();

// Busca os serviços da ordem
$sql = "SELECT s.*, i.quantidade, i.valor_unitario, (i.quantidade * i.valor_unitario) as valor_total
        FROM itens_ordem i
        INNER JOIN servicos s ON i.servico_id = s.id
        WHERE i.ordem_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

$ordem['servicos'] = [];
$ordem['valor_total'] = 0;

while ($servico = $result->fetch_assoc()) {
    $ordem['servicos'][] = $servico;
    $ordem['valor_total'] += $servico['valor_total'];
}

// Cria o diretório pdf se não existir
if (!file_exists('../pdf')) {
    mkdir('../pdf', 0777, true);
}

// Gera o PDF
$pdf = new OrdemServicoPDF($ordem, $conn);
$nomeArquivo = $pdf->gerarPDF();

// Redireciona para o arquivo
header('Location: ../pdf/' . $nomeArquivo);
exit; 