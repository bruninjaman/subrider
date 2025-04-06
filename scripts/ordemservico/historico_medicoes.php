<?php
/**
 * Funções para gerenciar o histórico de medições
 */

/**
 * Salva uma medição no histórico
 * @param string $ordem Número da ordem de serviço
 * @param string $tipo Tipo de medição (cabecote, bomba, motor, virabrequim, embreagem)
 * @param array $dados Dados da medição
 * @param string $observacao Observação opcional
 * @return bool Resultado da operação
 */
function salvarHistoricoMedicao($ordem, $tipo, $dados, $observacao = '') {
    global $conn;
    
    $data = date('Y-m-d H:i:s');
    $dados_json = json_encode($dados);
    $usuario = $_SESSION['user_id'] ?? 0;
    
    $query = "INSERT INTO historico_medicoes (ordem, tipo, dados, data, usuario, observacao) 
              VALUES (?, ?, ?, ?, ?, ?)";
              
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 'ssssss', $ordem, $tipo, $dados_json, $data, $usuario, $observacao);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

/**
 * Busca o histórico de medições de uma ordem
 * @param string $ordem Número da ordem de serviço
 * @param string|null $tipo Tipo específico de medição (opcional)
 * @return array Array com o histórico de medições
 */
function buscarHistoricoMedicoes($ordem, $tipo = null) {
    global $conn;
    
    $query = "SELECT h.*, u.nome as nome_usuario 
              FROM historico_medicoes h 
              LEFT JOIN usuarios u ON h.usuario = u.id 
              WHERE h.ordem = ?";
              
    if ($tipo) {
        $query .= " AND h.tipo = ?";
    }
    
    $query .= " ORDER BY h.data DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return [];
    }
    
    if ($tipo) {
        mysqli_stmt_bind_param($stmt, 'ss', $ordem, $tipo);
    } else {
        mysqli_stmt_bind_param($stmt, 's', $ordem);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $historico = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['dados'] = json_decode($row['dados'], true);
        $historico[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    
    return $historico;
}

/**
 * Compara medições com valores de referência
 * @param array $medicao Dados da medição
 * @param string $tipo Tipo de medição
 * @return array Array com as diferenças encontradas
 */
function compararComReferencia($medicao, $tipo) {
    global $conn;
    
    $diferencas = [];
    
    // Busca valores de referência
    $query = "SELECT * FROM valores_referencia WHERE tipo = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return $diferencas;
    }
    
    mysqli_stmt_bind_param($stmt, 's', $tipo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $referencia = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$referencia) {
        return $diferencas;
    }
    
    // Compara cada campo com sua referência
    foreach ($medicao as $campo => $valor) {
        if (isset($referencia[$campo . '_min']) && isset($referencia[$campo . '_max'])) {
            if ($valor < $referencia[$campo . '_min'] || $valor > $referencia[$campo . '_max']) {
                $diferencas[$campo] = [
                    'valor' => $valor,
                    'min' => $referencia[$campo . '_min'],
                    'max' => $referencia[$campo . '_max']
                ];
            }
        }
    }
    
    return $diferencas;
}

/**
 * Gera um relatório de medições
 * @param string $ordem Número da ordem de serviço
 * @return array Array com o relatório completo
 */
function gerarRelatorioMedicoes($ordem) {
    $tipos = ['cabecote', 'bomba', 'motor', 'virabrequim', 'embreagem'];
    $relatorio = [];
    
    foreach ($tipos as $tipo) {
        $historico = buscarHistoricoMedicoes($ordem, $tipo);
        
        if (!empty($historico)) {
            $ultima_medicao = $historico[0];
            $diferencas = compararComReferencia($ultima_medicao['dados'], $tipo);
            
            $relatorio[$tipo] = [
                'ultima_medicao' => $ultima_medicao,
                'diferencas' => $diferencas,
                'historico' => $historico
            ];
        }
    }
    
    return $relatorio;
}