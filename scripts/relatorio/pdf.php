<?php
// Geração de PDF profissional do relatório via Dompdf
session_start();

// Verificar autenticação
if (!isset($_SESSION["user"])) {
    http_response_code(401);
    echo "Não autenticado";
    exit;
}

$ordem_id = isset($_GET['ordem']) ? $_GET['ordem'] : '';
if (empty($ordem_id)) {
    http_response_code(400);
    echo "ID da ordem não especificado";
    exit;
}

require_once '../../connection/connection.php';
require_once '../../pdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!$conn) {
    http_response_code(500);
    echo "Erro de conexão com o banco de dados: " . mysqli_connect_error();
    exit;
}

// Escapar ordem id
$ordem_id = mysqli_real_escape_string($conn, $ordem_id);

// Buscar ordem
$query_ordem = "SELECT * FROM ordem_servicos WHERE Codigo = '$ordem_id'";
$result_ordem = mysqli_query($conn, $query_ordem);
if (!$result_ordem || mysqli_num_rows($result_ordem) == 0) {
    http_response_code(404);
    echo "Ordem de serviço não encontrada";
    exit;
}
$ordem = mysqli_fetch_assoc($result_ordem);

// Buscar moto (se existir)
$moto = [];
if ($ordem && isset($ordem['motoID']) && intval($ordem['motoID']) > 0) {
    $motoID = intval($ordem['motoID']);
    $result_moto = mysqli_query($conn, "SELECT * FROM motocicletas WHERE motoId = $motoID");
    if ($result_moto && mysqli_num_rows($result_moto) > 0) {
        $moto = mysqli_fetch_assoc($result_moto);
    }
}

// Buscar relatório
$stmt = mysqli_prepare($conn, "SELECT * FROM relatorios WHERE ordem_id = ?");
mysqli_stmt_bind_param($stmt, "s", $ordem_id);
mysqli_stmt_execute($stmt);
$result_rel = mysqli_stmt_get_result($stmt);
if (!$result_rel || mysqli_num_rows($result_rel) == 0) {
    http_response_code(404);
    echo "Relatório não encontrado";
    exit;
}
$relatorio = mysqli_fetch_assoc($result_rel);
mysqli_stmt_close($stmt);

// Dados
$numeroOrdem = $ordem['Codigo'];
$dataOrdem = isset($ordem['Data']) ? date('d/m/Y', strtotime($ordem['Data'])) : 'N/A';
$kmOrdem = isset($ordem['KM']) ? $ordem['KM'] : 'N/A';
$clienteOrdem = isset($moto['proprietario']) ? $moto['proprietario'] : (isset($ordem['proprietario_ordem']) ? $ordem['proprietario_ordem'] : 'N/A');
$motoOrdem = (isset($moto['marca']) && isset($moto['modelo'])) ? ($moto['marca'] . ' ' . $moto['modelo'] . ' (' . (isset($moto['ano']) ? $moto['ano'] : 'N/A') . ')' . (isset($moto['placa']) ? ' - ' . $moto['placa'] : '')) : 'N/A';
$dataConclusao = isset($relatorio['data_conclusao']) && !empty($relatorio['data_conclusao']) ? date('d/m/Y', strtotime($relatorio['data_conclusao'])) : 'N/A';
$conteudoEditor = isset($relatorio['conteudo']) ? $relatorio['conteudo'] : '';
$observacoesFinais = isset($relatorio['observacoes_finais']) ? $relatorio['observacoes_finais'] : '';

// Detectar se obs/conteudo são HTML ou texto puro
$hasHtmlEditor = preg_match('/<\/?[a-z][\s\S]*>/i', $conteudoEditor);
$hasHtmlObs = preg_match('/<\/?[a-z][\s\S]*>/i', $observacoesFinais);

// Sanear minimamente: permitir HTML salvo, mas evitar scripts
$conteudoSeguro = $hasHtmlEditor ? preg_replace('#<script[\s\S]*?</script>#i', '', $conteudoEditor) : nl2br(htmlspecialchars($conteudoEditor));
$obsSeguro = $hasHtmlObs ? preg_replace('#<script[\s\S]*?</script>#i', '', $observacoesFinais) : nl2br(htmlspecialchars($observacoesFinais));

// Avaliar densidade do conteúdo para ajustar estilos automaticamente
function avaliarDensidade($html) {
    $paragrafos = preg_match_all('#<p[\s\S]*?>#i', $html);
    $listas = preg_match_all('#<(ul|ol)[\s\S]*?>#i', $html);
    $itens = preg_match_all('#<li[\s\S]*?>#i', $html);
    $titulos = preg_match_all('#<h[1-6][\s\S]*?>#i', $html);
    $imagens = preg_match_all('#<img[\s\S]*?>#i', $html);
    $texto = trim(strip_tags($html));
    $caracteres = strlen($texto);
    $densidade = ($caracteres / 1200.0) + ($paragrafos * 0.1) + ($listas * 0.2) + ($itens * 0.1) + ($titulos * 0.3) + ($imagens * 1.5);
    if ($densidade <= 1.2) return 'normal';
    if ($densidade <= 2.0) return 'compacto';
    return 'muitoCompacto';
}

$nivel = avaliarDensidade($conteudoSeguro . $obsSeguro);

$profiles = [
  'normal' => [
    'pageMargin' => '18mm 15mm',
    'bodySize' => '11pt', 'lineHeight' => '1.35',
    'h1' => '18pt', 'h2' => '13pt', 'h3' => '12pt',
    'h1m' => '0 0 6px 0', 'h2m' => '10px 0 6px', 'h3m' => '8px 0 4px',
    'infoTd' => '10.5pt', 'contentSize' => '10.5pt', 'pMargin' => '4px 0',
    'listMargin' => '4px 0 8px 18px', 'liMargin' => '2px 0'
  ],
  'compacto' => [
    'pageMargin' => '15mm 12mm',
    'bodySize' => '10.5pt', 'lineHeight' => '1.28',
    'h1' => '16pt', 'h2' => '12pt', 'h3' => '11pt',
    'h1m' => '0 0 5px 0', 'h2m' => '8px 0 5px', 'h3m' => '6px 0 3px',
    'infoTd' => '10pt', 'contentSize' => '10pt', 'pMargin' => '3px 0',
    'listMargin' => '3px 0 6px 16px', 'liMargin' => '1px 0'
  ],
  'muitoCompacto' => [
    'pageMargin' => '12mm 10mm',
    'bodySize' => '10pt', 'lineHeight' => '1.22',
    'h1' => '15pt', 'h2' => '11.5pt', 'h3' => '10.5pt',
    'h1m' => '0 0 4px 0', 'h2m' => '6px 0 4px', 'h3m' => '5px 0 2px',
    'infoTd' => '9.8pt', 'contentSize' => '9.5pt', 'pMargin' => '2px 0',
    'listMargin' => '2px 0 5px 14px', 'liMargin' => '1px 0'
  ]
];

$s = $profiles[$nivel];

// CSS dinâmico conforme densidade
$css = "@page { size: A4; margin: {$s['pageMargin']}; }\n" .
       "body { font-family: DejaVu Sans, Arial, sans-serif; color: #000; font-size: {$s['bodySize']}; line-height: {$s['lineHeight']}; }\n" .
       "h1, h2, h3 { color: #5a4a96; margin: 0; }\n" .
       "h1 { font-size: {$s['h1']}; margin: {$s['h1m']}; }\n" .
       "h2 { font-size: {$s['h2']}; margin: {$s['h2m']}; }\n" .
       "h3 { font-size: {$s['h3']}; margin: {$s['h3m']}; }\n" .
       "/* Evitar quebra após títulos para manter título + primeiro conteúdo juntos */\n" .
       "h2, h3 { page-break-after: avoid; }\n" .
       ".header { text-align:center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid #5a4a96; }\n" .
       ".info table { width: 100%; border-collapse: collapse; }\n" .
       "/* Evitar quebra no meio da tabela de informações */\n" .
       ".info table, .info tr, .info td { page-break-inside: avoid; }\n" .
       ".info td { padding: 6px 0; border-bottom: 1px solid #e5e5e5; font-size: {$s['infoTd']}; }\n" .
       ".label { font-weight: bold; width: 28%; }\n" .
       ".section { margin-top: 12px; }\n" .
       ".content { font-size: {$s['contentSize']}; }\n" .
       ".content p { margin: {$s['pMargin']}; orphans: 2; widows: 2; }\n" .
       "/* Evitar quebra imediatamente antes do conteúdo de Observações Finais */\n" .
       ".obs .content { page-break-before: avoid; }\n" .
       "/* Melhorar quebra de palavras para evitar estouro de linha */\n" .
       ".content { word-break: break-word; }\n" .
       "ul, ol { margin: {$s['listMargin']}; }\n" .
       "li { margin: {$s['liMargin']}; }\n" .
       ".obs { margin-top: 12px; padding-top: 8px; border-top: 1px dashed #bbb; }\n" .
       ".footer { margin-top: 12px; text-align: right; font-size: {$s['contentSize']}; }\n";

// Montar HTML do PDF
$html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>' . $css . '</style>
</head>
<body>
  <div class="header">
    <h1>RELATÓRIO DE SERVIÇO</h1>
    <div>Ordem de Serviço: ' . htmlspecialchars($numeroOrdem) . '</div>
  </div>
  <div class="info">
    <table>
      <tr><td class="label">Cliente:</td><td>' . htmlspecialchars($clienteOrdem) . '</td></tr>
      <tr><td class="label">Motocicleta:</td><td>' . htmlspecialchars($motoOrdem) . '</td></tr>
      <tr><td class="label">Data:</td><td>' . htmlspecialchars($dataOrdem) . '</td></tr>
      <tr><td class="label">Quilometragem:</td><td>' . htmlspecialchars($kmOrdem) . '</td></tr>
    </table>
  </div>
  <div class="section">
    <h2>DETALHES DO SERVIÇO</h2>
    <div class="content editor-content">' . $conteudoSeguro . '</div>
  </div>';

if (!empty($observacoesFinais)) {
    $html .= '<div class="section obs">
      <h3>OBSERVAÇÕES FINAIS</h3>
      <div class="content obs-content">' . $obsSeguro . '</div>
    </div>';
}

$html .= '<div class="footer"><strong>Data de conclusão:</strong> ' . htmlspecialchars($dataConclusao) . '</div>
</body>
</html>';

// Dompdf options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$fileName = 'relatorio_' . preg_replace('#[/\\]+#', '-', $numeroOrdem) . '.pdf';
$pdfOutput = $dompdf->output();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
echo $pdfOutput;

mysqli_close($conn);
?>