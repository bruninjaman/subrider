<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//AUTOLOAD COMPOSER
require __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

//CONNECTION
require_once("./../connection/connection.php");
//FUNCTIONS
require_once("./../scripts/functions.php");

// Incluir o script de busca de imagens
require_once(__DIR__ . '/../scripts/peca_imagem.php');

$options = new Options();
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);

// Consulta todos os itens da ordem
$items_ordem_query = "SELECT * FROM item_ordem WHERE item_ordem.Ordem = '" . $_GET['ordem'] . "' ";
$result = mysqli_query($conn, $items_ordem_query);

// Separar itens por categoria
$pecas = [];
$servicos = [];
$adiantamentos = [];

while ($item = mysqli_fetch_assoc($result)) {
    if ($item['Categoria'] == '3') {
        $adiantamentos[] = $item;
    } 
    // Peças (categoria 2)
    else if ($item['Categoria'] == '2') {
        $pecas[] = $item;
    } 
    // Serviços (categoria 1)
    else {
        $servicos[] = $item;
    }
}

// Cálculos de totais
$total_pecas = 0;
$total_servicos = 0;
$total_adiantamentos = 0;

foreach ($pecas as $item) {
    $total_pecas += $item['Valor'] * $item['Quantidade'];
}

foreach ($servicos as $item) {
    $total_servicos += $item['Valor'] * $item['Quantidade'];
}

foreach ($adiantamentos as $item) {
    $total_adiantamentos += $item['Valor'] * $item['Quantidade'];
}

$total_geral = $total_pecas + $total_servicos;
$saldo = $total_geral - $total_adiantamentos;

//Pegar informações da moto vinculada a esta ordem de serviço
$motoinfo_query = "SELECT * FROM motocicletas ";
$motoinfo_query .= " WHERE motocicletas.motoId = (
    SELECT ordem_servicos.motoID FROM ordem_servicos 
    WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "')";

$result2 = mysqli_query($conn, $motoinfo_query);

//Pegar informações sobre esta ordem de serviço
$ordem_query = "SELECT * FROM ordem_servicos ";
$ordem_query .= " WHERE Codigo =  '". $_GET['ordem'] ."' ";
$result3 = mysqli_query($conn, $ordem_query);

//Pegar informações de ordem de serviço
while ($ordeminfo = mysqli_fetch_assoc($result3)) {
  if ($ordeminfo["Data"] == null || $ordeminfo["KM"] == NULL)
    header("location: ../tabelaOrdensEdit.php?ordem=" . $_GET['ordem']);
    $km = KMFormat($ordeminfo['KM']);
    $data = $ordeminfo['Data'];
    $nome = $ordeminfo['proprietario_ordem'];
}

//Pegar informações sobre a moto
while ($motoinfo = mysqli_fetch_assoc($result2)) {
  //$nome = $motoinfo['proprietario'];
  $fone = $motoinfo['contato'] ?? "61 91111-1111";
  $endereco = $motoinfo['endereco'];
  $marca = $motoinfo['marca'];
  $placa = $motoinfo['placa'];
  $modelo = $motoinfo['modelo'];
  $ano = $motoinfo['ano'];
}

//HTML antes de adicionar items
$loadhtmlstring = '<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ordem de Serviço - ' . $_GET['ordem'] . '</title>

    <style>
      * {
        box-sizing: border-box;
      }
      body {
        font-family: Arial, sans-serif;
        line-height: 1.3;
        color: #333;
        font-size: 10px;
      }
      .total {
        font-size: 12px;
      }
      .valores {
        color: black;
      }
      .valores-totais {
        color: black;
        font-weight: bold;
      }
      .section-title {
        background-color: #0a1e58;
        color: white;
        font-weight: bold;
        padding: 5px;
        font-size: 12px;
        margin-top: 15px;
        margin-bottom: 5px;
        border-radius: 3px;
      }
      #ordem {
        text-align: right;
        margin-left: 512px;
        position: absolute;
        margin-top: 12px;
        font-size: 12px;
      }
      .head-content {
        font-size: 9px;
        color: rgb(78, 78, 78);
      }
      .concluido {
        color: green;
      }
      .cancelado {
        color: red;
      }
      .saldo {
        font-size: 14px;
      }
      .main-th {
        color: #0a1e58;
      }
      thead tr:first-child th {
        height: 50px;
      }
      .logo img {
        width: 140px;
        height: 55px;
        position: absolute;
      }
      th, td {
        padding: 1.3px;
      }
      table {
        width: 100%;
        table-layout: fixed;
        border-spacing: 1px;
        font-size: 10px;
        background-color: rgb(218, 218, 218);
        border-radius: 5px;
        margin-bottom: 5px;
      }
      table thead {
        background-color: #dadde6;
        font-size: 11px;
        color: rgb(3, 3, 3);
        font-family: Arial, sans-serif;
      }
      table, th, td {
        border: 0.1px solid white;
      }
      table tbody {
        background-color: #eee;
        text-align: center;
        font-family: Arial, sans-serif;
      }
      table tbody tr td {
        padding: 1px;
        color: #1f274d;
      }
      tr:last-child {
        background-color: #eee;
      }
      .item-image {
        width: 30px;
        height: 30px;
        object-fit: cover;
        border-radius: 3px;
        margin-right: 5px;
        vertical-align: middle;
      }
      .item-description {
        display: flex;
        align-items: center;
        text-align: left;
      }
      .resumo-table td {
        text-align: left;
        padding: 5px;
      }
      .resumo-table tr:last-child {
        background-color: #dadde6;
        font-weight: bold;
        font-size: 12px;
      }
      .resumo-table tr:last-child td:last-child {
        color: #e44c65;
      }
    </style>
  </head>

  <body>
    <div>
      <table class="table">
        <thead>
          <tr>
            <th colspan="6" class="logo">
              <img
                src="https://www.subrider.com.br/assets/css/images/logo.png"
              />
              <a id="ordem">';

$loadhtmlstring .= $_GET['ordem'];

$loadhtmlstring .= '</a>
</th>
</tr>
<tr>
<th>Data:</th>
<th class="head-content" colspan="3">';

$loadhtmlstring .=  date("d/m/Y", strtotime($data));

$loadhtmlstring .= '<th>Km:</th>
<th class="head-content">';

$loadhtmlstring .= $km;

$loadhtmlstring .= '</th>
</tr>
<tr>
  <th colspan="1">Nome:</th>
  <th colspan="3" class="head-content">';

$loadhtmlstring .= $nome;

$loadhtmlstring .= '</th>
<th>Fone:</th>
<th class="head-content">';

$loadhtmlstring .= $fone;

$loadhtmlstring .= '</th>
  </tr>
  <tr>
    <th colspan="1">Endereço:</th>
    <th colspan="5" class="head-content">';

$loadhtmlstring .= $endereco;

$loadhtmlstring .= '</th>
  </tr>
  <tr>
    <th colspan="1">Marca:</th>
    <th colspan="2" class="head-content">';

$loadhtmlstring .= $marca;

$loadhtmlstring .= '</th>
    <th colspan="1">Placa:</th>
    <th colspan="2" class="head-content">';

$loadhtmlstring .= $placa;

$loadhtmlstring .= '</th>
    </tr>
    <tr>
      <th colspan="1">Modelo:</th>
      <th colspan="2" class="head-content">';

$loadhtmlstring .= $modelo;

$loadhtmlstring .= '</th>
    <th colspan="1">Ano:</th>
    <th colspan="2" class="head-content">';

$loadhtmlstring .= $ano;

$loadhtmlstring .= '</th>
    </tr>
  </thead>
</table>';

// Adicionar tabela de serviços
$loadhtmlstring .= '<div class="section-title">Serviços</div>
<table class="table">
  <thead>
    <tr>
      <th class="main-th" width="45%">Descrição</th>
      <th class="main-th" width="15%">Quantidade</th>
      <th class="main-th" width="20%">Valor unitário</th>
      <th class="main-th" width="20%">Valor Total</th>
    </tr>
  </thead>
  <tbody>';

if (count($servicos) > 0) {
  foreach ($servicos as $item) {
    $loadhtmlstring .= '<tr>
      <td style="text-align: left;">';
      
    $loadhtmlstring .= $item['Tipo'] != '0' ? "" . $item['Tipo'] . " - " : "";
    $loadhtmlstring .= $item['Grupo'] != '0' ? "" . $item['Grupo'] . " - " : "";
    $loadhtmlstring .= $item['Item'] != '0' ? "" . $item['Item'] . "" : "";
    $loadhtmlstring .= $item['Parte'] != '0' ? " / " . $item['Parte'] : "";
    $loadhtmlstring .= $item['Descricao'] != '0' ? "" . $item['Descricao'] : "";
      
    $loadhtmlstring .= '</td>
      <td>' . $item['Quantidade'] . '</td>
      <td>' . (($item['Valor'] <= 0) ? 'N/D' : realFormat($item['Valor'])) . '</td>
      <td>' . realFormat($item['Valor'] * $item['Quantidade']) . '</td>
    </tr>';
  }
} else {
  $loadhtmlstring .= '<tr><td colspan="4" style="text-align: center;">Nenhum serviço adicionado</td></tr>';
}

$loadhtmlstring .= '<tr class="total">
  <td colspan="2"></td>
  <td class="valores-totais">Total Serviços:</td>
  <td class="valores-totais">'. realFormat($total_servicos) .'</td>
</tr>
</tbody>
</table>';

// Adicionar tabela de peças
$loadhtmlstring .= '<div class="section-title">Peças</div>
<table class="table">
  <thead>
    <tr>
      <th class="main-th" width="45%">Descrição</th>
      <th class="main-th" width="15%">Quantidade</th>
      <th class="main-th" width="20%">Valor unitário</th>
      <th class="main-th" width="20%">Valor Total</th>
    </tr>
  </thead>
  <tbody>';

if (count($pecas) > 0) {
  foreach ($pecas as $item) {
    // Buscar imagem da peça
    $imagem_peca = '';
    
    // Primeiro, verificar se o item já tem um campo Foto
    if ($item['Foto'] != '0' && !empty($item['Foto'])) {
      // Usar a foto diretamente do item da ordem
      $imagem_peca = 'https://www.subrider.com.br/' . $item['Foto'];
    } 
    // Se não tem foto direta, tentar buscar pelo código
    else if ($item['Codigo'] != '0' && !empty($item['Codigo'])) {
      // Usar a função que criamos para buscar a imagem
      $imagem_temp = buscarImagemPeca($item['Codigo']);
      $imagem_peca = str_replace($baseAddress, 'https://www.subrider.com.br', $imagem_temp);
    } 
    // Se nada funcionar, usar a padrão
    else {
      $imagem_peca = 'https://www.subrider.com.br/assets/css/images/peca-padrao.png';
    }
    
    $loadhtmlstring .= '<tr>
      <td style="text-align: left;">
        <div class="item-description">
          <img src="' . $imagem_peca . '" alt="Imagem da Peça" class="item-image" />
          ';
      
    $loadhtmlstring .= $item['Tipo'] != '0' ? "" . $item['Tipo'] . " - " : "";
    $loadhtmlstring .= $item['Grupo'] != '0' ? "" . $item['Grupo'] . " - " : "";
    $loadhtmlstring .= $item['Item'] != '0' ? "" . $item['Item'] . "" : "";
    $loadhtmlstring .= $item['Parte'] != '0' ? " / " . $item['Parte'] : "";
    $loadhtmlstring .= $item['Descricao'] != '0' ? "" . $item['Descricao'] : "";
    $loadhtmlstring .= $item['Codigo'] != '0' ? " (" . $item['Codigo'] . ")" : "";
      
    $loadhtmlstring .= '</div>
      </td>
      <td>' . $item['Quantidade'] . '</td>
      <td>' . (($item['Valor'] <= 0) ? 'N/D' : realFormat($item['Valor'])) . '</td>
      <td>' . realFormat($item['Valor'] * $item['Quantidade']) . '</td>
    </tr>';
  }
} else {
  $loadhtmlstring .= '<tr><td colspan="4" style="text-align: center;">Nenhuma peça adicionada</td></tr>';
}

$loadhtmlstring .= '<tr class="total">
  <td colspan="2"></td>
  <td class="valores-totais">Total Peças:</td>
  <td class="valores-totais">'. realFormat($total_pecas) .'</td>
</tr>
</tbody>
</table>';

// Adicionar tabela de adiantamentos
$loadhtmlstring .= '<div class="section-title">Adiantamentos (Pagamentos Recebidos)</div>
<table class="table">
  <thead>
    <tr>
      <th class="main-th" width="45%">Descrição</th>
      <th class="main-th" width="15%">Quantidade</th>
      <th class="main-th" width="20%">Valor unitário</th>
      <th class="main-th" width="20%">Valor Total</th>
    </tr>
  </thead>
  <tbody>';

if (count($adiantamentos) > 0) {
  foreach ($adiantamentos as $item) {
    $loadhtmlstring .= '<tr>
      <td style="text-align: left;">';
      
    $loadhtmlstring .= $item['Descricao'] != '0' ? "" . $item['Descricao'] : "Pagamento";
      
    $loadhtmlstring .= '</td>
      <td>' . $item['Quantidade'] . '</td>
      <td>' . (($item['Valor'] <= 0) ? 'N/D' : realFormat($item['Valor'])) . '</td>
      <td>' . realFormat($item['Valor'] * $item['Quantidade']) . '</td>
    </tr>';
  }
} else {
  $loadhtmlstring .= '<tr><td colspan="4" style="text-align: center;">Nenhum adiantamento registrado</td></tr>';
}

$loadhtmlstring .= '<tr class="total">
  <td colspan="2"></td>
  <td class="valores-totais">Total Adiantamentos:</td>
  <td class="valores-totais">'. realFormat($total_adiantamentos) .'</td>
</tr>
</tbody>
</table>';

// Adicionar resumo
$loadhtmlstring .= '<div class="section-title">Resumo</div>
<table class="table resumo-table">
  <tbody>
    <tr>
      <td width="60%"><strong>Total de Serviços:</strong></td>
      <td width="40%">'. realFormat($total_servicos) .'</td>
    </tr>
    <tr>
      <td><strong>Total de Peças:</strong></td>
      <td>'. realFormat($total_pecas) .'</td>
    </tr>
    <tr>
      <td><strong>Valor Total:</strong></td>
      <td>'. realFormat($total_geral) .'</td>
    </tr>
    <tr>
      <td><strong>Total de Adiantamentos:</strong></td>
      <td>'. realFormat($total_adiantamentos) .'</td>
    </tr>
    <tr>
      <td><strong>Total a Pagar:</strong></td>
      <td>'. realFormat($saldo) .'</td>
    </tr>
  </tbody>
</table>';

$loadhtmlstring .= '</div>
</body>
</html>';

$dompdf->loadHtml($loadhtmlstring);

// Configurar tamanho do papel e orientação
$dompdf->setPaper('A4', 'portrait');

// Renderizar PDF
$dompdf->render();

// Enviar o PDF para o navegador
header('content-type: application/pdf');
echo $dompdf->output();

mysqli_close($conn);
