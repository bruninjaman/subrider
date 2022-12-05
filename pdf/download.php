<?php
//AUTOLOAD COMPOSER
require __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

//CONNECTION
require_once("./../connection/connection.php");
//FUNCTIONS
require_once("./../scripts/functions.php");

$options = new Options();
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);

//Pegar todos items pertencentes a esta ordem de serviço
$items_ordem_query = "SELECT * FROM item_ordem ";
$items_ordem_query .= "WHERE item_ordem.Ordem = '" . $_GET['ordem'] . "' ";
$result = mysqli_query($conn, $items_ordem_query);

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
    header("location: tabelaOrdensEdit.php");
    $km = KMFormat($ordeminfo['KM']);
    $data = $ordeminfo['Data'];
}

//Pegar informações sobre a moto
while ($motoinfo = mysqli_fetch_assoc($result2)) {
  $nome = $motoinfo['proprietario'];
  $fone = "61 91111-1111";
  $endereco = $motoinfo['endereco'];
  $marca = $motoinfo['marca'];
  $placa = $motoinfo['placa'];
  $modelo = $motoinfo['modelo'];
  $ano = $motoinfo['ano'];
}

//Inicializar total e adiantamento em 0
$total = 0;
$adiantamento = 0;


//HTML antes de adicionar items
$loadhtmlstring = '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arquivo PDF</title>

    <style>
      .total {
        font-size: 12px;
      }
      .valores {
        color: black;
      }
      .valores-totais {
        color: black;
        font-style: bold;
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
      th,
      td {
        padding: 1.3px;
      }
      table {
        width: 100%;
        table-layout: fixed;
        border-spacing: 1px;
        font-size: 10px;
        background-color: rgb(218, 218, 218);
        border-radius: 5px;
      }
      table thead {
        background-color: #dadde6;
        font-size: 11px;
        color: rgb(3, 3, 3);
        font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
      }
      table,
      th,
      td {
        border: 0.1px solid white;
      }
      table tbody {
        background-color: #eee;
        text-align: center;
        font-family: "ptsans", serif;
      }
      /*  */
      table tbody tr td {
        padding: 1px;
        color: #1f274d;
      }
      tr:last-child {
        background-color: #eee;
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

$loadhtmlstring .=  $data;

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
    <tr>
      <th class="main-th">Ordem</th>
      <th class="main-th" colspan="2">Descrição</th>
      <th class="main-th">Quantidade</th>
      <th class="main-th">Valor unitário</th>
      <th class="main-th">Valor Total</th>
    </tr>
  </thead>
  <tbody>';




$total;
$adiantamento;
$itemcount = 0;


while ($item = mysqli_fetch_assoc($result)) {
  $itemcount++;
  if ($item["Categoria"] != '3') {
    $total = $total + $item['Valor'] * $item['Quantidade'];
  } else {
    $adiantamento = $adiantamento  + $item['Valor'] * $item['Quantidade'];
  }
  if ($item['Categoria'] == '3')
    continue;

  //item da tabela
  $loadhtmlstring .= '<tr>
    <td>'. $itemcount . '</td>
    <td colspan="2">';


  $loadhtmlstring .= $item['Tipo'] != '0' ? "" . $item['Tipo'] . " - " : "";
  $loadhtmlstring .= $item['Grupo'] != '0' ? "" . $item['Grupo'] . " - " : "";
  $loadhtmlstring .= $item['Item'] != '0' ? "" . $item['Item'] . "" : "";
  $loadhtmlstring .= $item['Parte'] != '0' ? " / " . $item['Parte'] : "";
  $loadhtmlstring .= $item['Descricao'] != '0' ? "" . $item['Descricao'] : "";
  if ($item['Categoria'] == '2')
    $loadhtmlstring .= $item['Codigo'] != '0' ? "(" . $item['Codigo'] . ")" : "";

  $loadhtmlstring .= '</td>
    <td>';

  $loadhtmlstring .= $item['Quantidade'];

  $loadhtmlstring .= '</td>
    <td>';

  $loadhtmlstring .= realFormat($item['Valor']);

  $loadhtmlstring .= '</td>
    <td>';

  $loadhtmlstring .= realFormat($item['Valor'] * $item['Quantidade']);

  $loadhtmlstring .= '</td>
    </tr>';
  //
}
$saldo = $total - $adiantamento;

$loadhtmlstring .= '<tr class="total">
<td colspan="4"></td>
<td class="valores-totais">SubTotal:</td>
<td class="valores-totais">';

$loadhtmlstring .= realFormat($total);

$loadhtmlstring .= '</td>
</tr>
</tbody>
</table>
<br />
<table class="table">
<tbody>';

$loadhtmlstring .= $total;

$result = mysqli_query($conn, $items_ordem_query);
while ($item = mysqli_fetch_assoc($result)) {
  if ($item['Categoria'] != '3')
    continue;
  $loadhtmlstring .= '<tr>
        <td class="adiantamento" colspan="2">';
  $loadhtmlstring .= $item['Descricao'];
  $loadhtmlstring .= '</td>
        <td class="valores">';
  $loadhtmlstring .= realFormat($item['Valor'] * $item['Quantidade']);
  $loadhtmlstring .= '</td>
        </tr>';
}
$loadhtmlstring .= '<tr>
<td class="saldo">Saldo:</td>
<td class="valores-totais" colspan="2">';

$loadhtmlstring .= realFormat($saldo);

$loadhtmlstring .= '</td>
</tr>
</tbody>
</table>
</div>
</body>
</html>';

// var_dump(htmlspecialchars($loadhtmlstring));
// die();

$dompdf->loadHtml($loadhtmlstring);

//testing new pdf
//$dompdf->loadHtmlFile(__DIR__ . '/ordem.html');


$dompdf->render();

header('content-type: application/pdf');

//echo $dompdf->stream();
echo $dompdf->output();

mysqli_close($conn);
