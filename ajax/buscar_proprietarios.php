<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../repositories/ProprietarioRepository.php';

$termo = isset($_GET['termo']) ? $_GET['termo'] : '';

if (empty($termo)) {
    echo json_encode([]);
    exit;
}

$proprietarioRepo = new ProprietarioRepository();
$proprietarios = $proprietarioRepo->buscar($termo);

echo json_encode($proprietarios); 