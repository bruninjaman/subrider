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

$ordens = [];

// Query to fetch summary data for all orders
$sql = "SELECT Codigo, Data, proprietario_ordem FROM ordem_servicos ORDER BY Data DESC"; 

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ordens[] = [
            'codigo' => $row['Codigo'],
            'data' => $row['Data'], 
            'nomeCliente' => $row['proprietario_ordem']
        ];
    }
    mysqli_free_result($result);
    http_response_code(200 ); // OK
    echo json_encode($ordens);
} else {
    error_log("MySQL Error: " . mysqli_error($conn)); 
    http_response_code(500 ); // Internal Server Error
    echo json_encode(['error' => 'Failed to retrieve order list from database.']);
}

// O seu script connection.php deve lidar com o fechamento da conexão se necessário,
// ou podemos adicionar mysqli_close($conn); aqui se ele não o fizer.
// mysqli_close($conn); 

?>
