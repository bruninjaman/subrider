<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/../classes/CalculadoraOrdem.php");

if (!isset($_GET['ordem'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ordem não especificada']);
    exit;
}

$calculadora = new CalculadoraOrdem($conn, $_GET['ordem']);
$totais = $calculadora->getTotais();

header('Content-Type: application/json');
echo json_encode($totais);