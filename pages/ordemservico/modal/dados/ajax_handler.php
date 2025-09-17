<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Verificar se os dados foram enviados
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dados não recebidos']);
    exit;
}

// Verificar parâmetros obrigatórios
if (!isset($input['table']) || !isset($input['ordem']) || !isset($input['medicoes'])) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros obrigatórios não fornecidos']);
    exit;
}

$table = $input['table'];
$ordem = $input['ordem'];
$medicoes = $input['medicoes'];

// Incluir conexão com banco de dados
require_once '../../../../config/database.php';

// Verificar se a conexão existe
if (!isset($conn) || !$conn) {
    // Tentar incluir arquivo de conexão alternativo
    if (file_exists('../../../../conexao.php')) {
        require_once '../../../../conexao.php';
    } elseif (file_exists('../../../conexao.php')) {
        require_once '../../../conexao.php';
    } elseif (file_exists('../../conexao.php')) {
        require_once '../../conexao.php';
    } elseif (file_exists('../conexao.php')) {
        require_once '../conexao.php';
    }
    
    if (!isset($conn) || !$conn) {
        echo json_encode(['success' => false, 'message' => 'Erro: Conexão com o banco de dados não estabelecida']);
        exit;
    }
}

// Validar nome da tabela para segurança
$tabelas_permitidas = ['bomba', 'cabecote', 'embreagem', 'motor', 'virabrequim'];
if (!in_array($table, $tabelas_permitidas)) {
    echo json_encode(['success' => false, 'message' => 'Tabela não permitida']);
    exit;
}

try {
    // Buscar medições existentes antes de processar
    $queryMed = "SELECT medicoes FROM $table WHERE is_reference = 0 AND ordem = ?";
    $stmtMed = mysqli_prepare($conn, $queryMed);
    if (!$stmtMed) {
        throw new Exception("Erro ao preparar consulta: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmtMed, "s", $ordem);
    mysqli_stmt_execute($stmtMed);
    $resultMed = mysqli_stmt_get_result($stmtMed);
    $dadosMed = mysqli_fetch_assoc($resultMed);
    
    // Mesclar medições existentes com novas
    $medicoesExistentes = $dadosMed && $dadosMed['medicoes'] ? json_decode($dadosMed['medicoes'], true) : [];
    
    // Atualizar apenas os campos enviados
    foreach ($medicoes as $param => $valor) {
        if ($valor !== null && $valor !== '') {
            $medicoesExistentes[$param] = is_numeric($valor) ? floatval(str_replace(',', '.', $valor)) : $valor;
        } else {
            $medicoesExistentes[$param] = null;
        }
    }
    
    // Verificar se já existe registro de medições
    $checkQuery = "SELECT id FROM $table WHERE is_reference = 0 AND ordem = ?";
    $stmtCheck = mysqli_prepare($conn, $checkQuery);
    if (!$stmtCheck) {
        throw new Exception("Erro ao preparar verificação: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmtCheck, "s", $ordem);
    mysqli_stmt_execute($stmtCheck);
    $resultCheck = mysqli_stmt_get_result($stmtCheck);
    
    $jsonMedicoes = json_encode($medicoesExistentes);
    
    if (mysqli_num_rows($resultCheck) > 0) {
        // Atualizar registro existente
        $updateQuery = "UPDATE $table SET medicoes = ? WHERE is_reference = 0 AND ordem = ?";
        $stmtUpdate = mysqli_prepare($conn, $updateQuery);
        if (!$stmtUpdate) {
            throw new Exception("Erro ao preparar atualização: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmtUpdate, "ss", $jsonMedicoes, $ordem);
        if (!mysqli_stmt_execute($stmtUpdate)) {
            throw new Exception("Erro ao atualizar medições: " . mysqli_stmt_error($stmtUpdate));
        }
    } else {
        // Inserir novo registro
        $insertQuery = "INSERT INTO $table (ordem, is_reference, medicoes) VALUES (?, 0, ?)";
        $stmtInsert = mysqli_prepare($conn, $insertQuery);
        if (!$stmtInsert) {
            throw new Exception("Erro ao preparar inserção: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmtInsert, "ss", $ordem, $jsonMedicoes);
        if (!mysqli_stmt_execute($stmtInsert)) {
            throw new Exception("Erro ao inserir medições: " . mysqli_stmt_error($stmtInsert));
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Medições salvas com sucesso!',
        'medicoes' => $medicoesExistentes
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao salvar: ' . $e->getMessage()
    ]);
}
?>