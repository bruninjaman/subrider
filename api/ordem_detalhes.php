<?php
// Use o seu arquivo de conexão - ajuste o path se necessário
require_once(__DIR__ . '/../connection/connection.php'); 

header('Content-Type: application/json; charset=utf-8');

// Verifica se a variável $conn foi criada pelo seu script de conexão
if (!isset($conn) || !$conn) {
    http_response_code(500 );
    echo json_encode(['error' => 'Database connection failed or variable $conn not set in connection.php']);
    exit();
}

// Get the order code from the query parameter
$codigo_ordem = isset($_GET['codigo']) ? $_GET['codigo'] : null;

if (!$codigo_ordem) {
    http_response_code(400 ); // Bad Request
    echo json_encode(['error' => 'Order code (codigo) is required.']);
    exit();
}

$response = [];

// --- 1. Fetch Order Details --- 
$ordem_sql = "SELECT * FROM ordem_servicos WHERE Codigo = ?";
$stmt_ordem = mysqli_prepare($conn, $ordem_sql);

if ($stmt_ordem) {
    mysqli_stmt_bind_param($stmt_ordem, "s", $codigo_ordem);
    mysqli_stmt_execute($stmt_ordem);
    $result_ordem = mysqli_stmt_get_result($stmt_ordem);
    $ordem_info = mysqli_fetch_assoc($result_ordem);
    mysqli_stmt_close($stmt_ordem);

    if (!$ordem_info) {
        http_response_code(404 ); // Not Found
        echo json_encode(['error' => 'Order not found.']);
        // mysqli_close($conn); // Fechar conexão em caso de erro
        exit();
    }

    $response['codigo'] = $ordem_info['Codigo'];
    $response['data'] = $ordem_info['Data'];
    $response['km'] = $ordem_info['KM'];
    $response['cliente'] = [
        'nome' => $ordem_info['proprietario_ordem'],
        'fone' => null, 
        'endereco' => null 
    ];
    $moto_id = $ordem_info['motoID'];

} else {
    error_log("MySQL Error (Order Query): " . mysqli_error($conn));
    http_response_code(500 );
    echo json_encode(['error' => 'Failed to retrieve order details.']);
    // mysqli_close($conn); // Fechar conexão em caso de erro
    exit();
}

// --- 2. Fetch Motorcycle Details --- 
$moto_sql = "SELECT * FROM motocicletas WHERE motoId = ?";
$stmt_moto = mysqli_prepare($conn, $moto_sql);
$moto_info = null;

if ($stmt_moto && $moto_id) {
    mysqli_stmt_bind_param($stmt_moto, "s", $moto_id);
    mysqli_stmt_execute($stmt_moto);
    $result_moto = mysqli_stmt_get_result($stmt_moto);
    $moto_info = mysqli_fetch_assoc($result_moto);
    mysqli_stmt_close($stmt_moto);
}

$response['moto'] = [
    'marca' => $moto_info['marca'] ?? 'N/A',
    'placa' => $moto_info['placa'] ?? 'N/A',
    'modelo' => $moto_info['modelo'] ?? 'N/A',
    'ano' => $moto_info['ano'] ?? 'N/A'
];
$response['cliente']['fone'] = $moto_info['contato'] ?? 'Não informado';
$response['cliente']['endereco'] = $moto_info['endereco'] ?? 'Não informado';

// --- 3. Fetch Order Items --- 
$items_sql = "SELECT * FROM item_ordem WHERE Ordem = ?";
$stmt_items = mysqli_prepare($conn, $items_sql);
$items = [];

if ($stmt_items) {
    mysqli_stmt_bind_param($stmt_items, "s", $codigo_ordem);
    mysqli_stmt_execute($stmt_items);
    $result_items = mysqli_stmt_get_result($stmt_items);

    while ($item_row = mysqli_fetch_assoc($result_items)) {
        $categoria = 'SERVICO';
        if ($item_row['Categoria'] == '2') {
            $categoria = 'PECA';
        } elseif ($item_row['Categoria'] == '3') {
            $categoria = 'ADIANTAMENTO';
        }

        $descricao = '';
        $descricao .= ($item_row['Tipo'] != '0' && !empty($item_row['Tipo'])) ? $item_row['Tipo'] . " - " : "";
        $descricao .= ($item_row['Grupo'] != '0' && !empty($item_row['Grupo'])) ? $item_row['Grupo'] . " - " : "";
        $descricao .= ($item_row['Item'] != '0' && !empty($item_row['Item'])) ? $item_row['Item'] . "" : "";
        $descricao .= ($item_row['Parte'] != '0' && !empty($item_row['Parte'])) ? " / " . $item_row['Parte'] : "";
        $descricao .= ($item_row['Descricao'] != '0' && !empty($item_row['Descricao'])) ? " " . $item_row['Descricao'] : "";
        $descricao = trim($descricao, " - ");
        if (empty($descricao) && $categoria == 'ADIANTAMENTO') {
             $descricao = 'Adiantamento / Pagamento';
        }
        if (empty($descricao)) {
             $descricao = 'Item sem descrição';
        }

        $items[] = [
            'id' => $item_row['id'] ?? uniqid('item_'),
            'descricao' => $descricao,
            'quantidade' => (float)($item_row['Quantidade'] ?? 0),
            'valorUnitario' => (float)($item_row['Valor'] ?? 0),
            'categoria' => $categoria,
            'imagemUrl' => ($categoria == 'PECA' && $item_row['Foto'] != '0' && !empty($item_row['Foto'])) ? 'https://www.subrider.com.br/' . $item_row['Foto'] : null, 
            'codigoPeca' => ($categoria == 'PECA' && $item_row['Codigo'] != '0' && !empty($item_row['Codigo'] )) ? $item_row['Codigo'] : null
        ];
    }
    mysqli_stmt_close($stmt_items);
    $response['itens'] = $items;

} else {
    error_log("MySQL Error (Items Query): " . mysqli_error($conn));
    http_response_code(500 );
    echo json_encode(['error' => 'Failed to retrieve order items.']);
    // mysqli_close($conn); // Fechar conexão em caso de erro
    exit();
}

// --- 4. Calculate Totals --- 
$totalServicos = 0;
$totalPecas = 0;
$totalAdiantamentos = 0;

foreach ($items as $item) {
    $valorTotalItem = $item['quantidade'] * $item['valorUnitario'];
    if ($item['categoria'] == 'SERVICO') {
        $totalServicos += $valorTotalItem;
    } elseif ($item['categoria'] == 'PECA') {
        $totalPecas += $valorTotalItem;
    } elseif ($item['categoria'] == 'ADIANTAMENTO') {
        $totalAdiantamentos += $valorTotalItem;
    }
}

$totalGeral = $totalServicos + $totalPecas;
$saldo = $totalGeral - $totalAdiantamentos;

$response['calculados'] = [
    'totalServicos' => $totalServicos,
    'totalPecas' => $totalPecas,
    'totalAdiantamentos' => $totalAdiantamentos,
    'totalGeral' => $totalGeral,
    'saldo' => $saldo
];

// --- 5. Send Response --- 
http_response_code(200 ); // OK
echo json_encode($response);

// O seu script connection.php deve lidar com o fechamento da conexão se necessário,
// ou podemos adicionar mysqli_close($conn); aqui se ele não o fizer.
// mysqli_close($conn); 

?>
