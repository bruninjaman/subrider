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

//$dompdf->loadHtml('Teste');
//$dompdf->loadHtmlFile(__DIR__ . '/ordem.html');

$items_ordem_query = "SELECT * FROM item_ordem ";
$items_ordem_query .= "WHERE item_ordem.Ordem = '" . $_GET['ordem'] . "' ";
$result = mysqli_query($conn, $items_ordem_query);

$loadhtmlstring = '<style>
table {
    border: 1px solid #ccc;
    border-collapse: collapse;
    margin: 0;
    padding: 0;
    width: 100%;
    table-layout: fixed;
}

table caption {
    font-size: 1.5em;
    margin: .5em 0 .75em;
}

table tr {
    background-color: #f8f8f8;
    border: 1px solid #ddd;
    padding: .35em;
}

table th,
table td {
    padding: .625em;
    text-align: center;
}

table th {
    font-size: .85em;
    letter-spacing: .1em;
    text-transform: uppercase;
}

@media screen and (max-width: 600px) {
    table {
        border: 0;
    }

    table caption {
        font-size: 1.3em;
    }

    table thead {
        border: none;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
    }

    table tr {
        border-bottom: 3px solid #ddd;
        display: block;
        margin-bottom: .625em;
    }

    table td {
        border-bottom: 1px solid #ddd;
        display: block;
        font-size: .8em;
        text-align: right;
    }

    table td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-transform: uppercase;
    }

    table td:last-child {
        border-bottom: 0;
    }
}
body {
    font-family: "Open Sans", sans-serif;
    line-height: 1.25;
}
</style> ';

$loadhtmlstring .= '<img style="width: 200px; height: 100px;" src="https://www.subrider.com.br/assets/css/images/logo.png">';

$loadhtmlstring .= '
<table class="table">
<thead>
    <tr>
        <th colspan=3>Descrição</th>
        <th>Quantidade</th>
        <th>Valor unitário</th>
        <th>Valor Total</th>
    </tr>
</thead>
<tbody>
    ';
    $total = 0;
    $adiantamento = 0;
    while ($item = mysqli_fetch_assoc($result)) {
        $grupo = $item['Grupo'] != '0' ? "" . $item['Grupo'] : "";
        $parte = $item['Parte'] != '0' ? "/" . $item['Parte'] . "/" : "";
        $item2 = $item['Item'] != '0' ? "" . $item['Item'] : "";
        $descricao = $item['Descricao'] != '0' ? "" . $item['Descricao'] : "";
        $loadhtmlstring .= '
        <tr>
            <td colspan=3>' . $grupo.$parte.$item2.$descricao . '</td>
            <td>' . $item['Quantidade'] . '</td>
            <td>' . realFormat($item['Valor']) . '</td>
            <td>' . realFormat($item['Valor'] * $item['Quantidade']) . '</td>
        </tr>
        ';
        if ($item["Descricao"] == '0') {
            $total = $total + $item['Valor'] * $item['Quantidade'];
        } else {
            $adiantamento = $adiantamento  + $item['Valor'] * $item['Quantidade'];
        }
    }
    $subtotal = $total - $adiantamento;
    $loadhtmlstring .= ' <tr class="total">
    <td colspan="2"></td>
    <td>Subtotal:</td>
    <td>'. realFormat($subtotal) .'</td>
    <td>Total:</td>
    <td>'. realFormat($total) .'</td>
</tr>';
    $loadhtmlstring .= '
</tbody>
</table>
';

$dompdf->loadHtml($loadhtmlstring);
$dompdf->render();

mysqli_close($conn);
header('content-type: application/pdf');
echo $dompdf->stream();
