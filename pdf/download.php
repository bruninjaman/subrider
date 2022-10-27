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

$items_ordem_query = 'SELECT 1 as type, pecas.grupo grupo, pecas.parte parte, pecas.item item, pecas.foto foto, pecas.valor, pecas.quantidade,pecas.pecaId ID FROM pecas ';
$items_ordem_query .= "WHERE pecas.ordem = '" . $_GET['ordem'] . "' ";
$items_ordem_query .= "UNION ";
$items_ordem_query .= "SELECT 2, Null, Null, servicos.item, Null, servicos.valor, 1, servicos.servicoId FROM servicos ";
$items_ordem_query .= "WHERE servicos.ordem = '" . $_GET['ordem'] . "' ";
$items_ordem_query .= "UNION ";
$items_ordem_query .= "SELECT 3, null, null, adiantamento.descricao, Null, adiantamento.valor, 1, adiantamento.IDadiantamento FROM adiantamento ";
$items_ordem_query .= "WHERE adiantamento.ordem = '" . $_GET['ordem'] . "' ";
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

$loadhtmlstring .= '
<table class="table">
<thead>
    <tr>
        <th colspan=3>Grupo/Parte/Item</th>
        <th>Quantidade</th>
        <th>Valor unitário</th>
        <th>Valor Total</th>
    </tr>
</thead>
<tbody>
    ';

    while ($item = mysqli_fetch_assoc($result)) {
        $grupo = $item['grupo'] != null ? "" . $item['grupo'] : "";
        $parte = $item['parte'] != null ? "/" . $item['parte'] . "/" : "";
        $item2 = $item['item'] != null ? "" . $item['item'] : "";
        $loadhtmlstring .= '
        <tr>
            <td colspan=3>' . $grupo.$parte.$item2 . '</td>
            <td>' . $item['quantidade'] . '</td>
            <td>' . realFormat($item['valor']) . '</td>
            <td>' . realFormat($item['valor'] * $item['quantidade']) . '</td>
        </tr>
    ';
    }
    $loadhtmlstring .= '
</tbody>
</table>
';

$dompdf->loadHtml($loadhtmlstring);
$dompdf->render();

header('content-type: application/pdf');
echo $dompdf->stream();
