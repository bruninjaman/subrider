<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/Relatorio.php';

// Verifica se foi passado o número da ordem
if (!isset($_GET['ordem'])) {
    die('Ordem não especificada');
}

// Cria uma instância do relatório
$relatorio = new Relatorio($conn, $_GET['ordem']);

// Gera o PDF
$mpdf = $relatorio->gerarPDF();

// Define o nome do arquivo
$filename = 'OS_' . $_GET['ordem'] . '_' . date('Y-m-d') . '.pdf';

// Envia o PDF para download
$mpdf->Output($filename, 'D');