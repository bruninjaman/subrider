<?php
require_once(__DIR__ . "/../config.php");

/**
 * Função para sanitizar e validar os inputs
 * @param mixed $value Valor a ser sanitizado
 * @param string $type Tipo de dado (int, float, string, bool)
 * @param mixed $default Valor padrão se for inválido
 * @return mixed Valor sanitizado
 */
function sanitizeInput($value, $type = 'string', $default = null) {
    if (!isset($value)) {
        return $default;
    }
    
    switch ($type) {
        case 'int':
            return intval($value);
        case 'float':
            return floatval($value);
        case 'bool':
            return (bool)$value;
        case 'string':
        default:
            return isset($conn) ? mysqli_real_escape_string($GLOBALS['conn'], (string)$value) : (string)$value;
    }
}

/**
 * Função para verificar se um registro existe
 * @param string $table Nome da tabela
 * @param string $where Condição WHERE
 * @param array $params Parâmetros para o prepared statement
 * @param string $types Tipos de dados dos parâmetros
 * @return int|null ID do registro encontrado ou null
 */
function checkRecordExists($table, $where, $params, $types) {
    global $conn;
    
    $query = "SELECT id FROM $table WHERE $where LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    
    if ($exists) {
        mysqli_stmt_bind_result($stmt, $id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $id;
    }
    
    mysqli_stmt_close($stmt);
    return null;
}

/**
 * Função para processar a inserção ou atualização de registros
 * @param string $table Nome da tabela
 * @param array $fields Lista de campos
 * @param array $values Lista de valores
 * @param string $types Tipos de dados dos parâmetros
 * @param string|null $where Condição WHERE para update (null para insert)
 * @param int|null $id ID do registro para update (null para insert)
 * @return bool Resultado da operação
 */
function processRecord($table, $fields, $values, $types, $where = null, $id = null) {
    global $conn;
    
    // Determina se é uma operação de INSERT ou UPDATE
    $isUpdate = ($where !== null && $id !== null);
    
    if ($isUpdate) {
        // Prepara a query de UPDATE
        $setFields = implode(' = ?, ', $fields) . ' = ?';
        $query = "UPDATE $table SET $setFields WHERE $where";
        
        // Adiciona o ID aos valores para a cláusula WHERE
        if (is_numeric($id)) {
            $values[] = $id;
            $types .= 'i';
        } else {
            $values[] = $id;
            $types .= 's';
        }
    } else {
        // Prepara a query de INSERT
        $placeholders = rtrim(str_repeat('?, ', count($fields)), ', ');
        $fieldsList = implode(', ', $fields);
        $query = "INSERT INTO $table ($fieldsList) VALUES ($placeholders)";
    }
    
    // Prepara e executa a query
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, $types, ...$values);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

/**
 * Processa as informações do motor
 * @param array $post Dados do POST
 * @param string $ordem Número da ordem
 * @return bool Resultado da operação
 */
function processInfoMotor($post, $ordem) {
    global $conn;
    
    // Busca informações da motocicleta no banco de dados
    $motocicletas_query = "SELECT * FROM motocicletas WHERE (SELECT ordem_servicos.motoID FROM ordem_servicos WHERE ordem_servicos.Codigo = ?) = motocicletas.motoId";
    $stmt = mysqli_prepare($conn, $motocicletas_query);
    mysqli_stmt_bind_param($stmt, "s", $ordem);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $moto = mysqli_fetch_assoc($result);
    
    if ($moto) {
        $ano = sanitizeInput($moto['ano'], 'int');
        $modelo = sanitizeInput($moto['modelo']);
        $marca = sanitizeInput($moto['marca']);
        
        // Verifica se a ordem já existe
        $recordId = checkRecordExists('infomotor', 'ordem = ?', [$ordem], 's');
        
        // Se não existe, insere um novo registro
        if ($recordId === null) {
            $fields = ['ano', 'modelo', 'marca', 'ordem'];
            $values = [$ano, $modelo, $marca, $ordem];
            return processRecord('infomotor', $fields, $values, 'isss');
        }
    }
    
    return true;
}

/**
 * Processa informações da bomba
 * @param array $post Dados do POST
 * @param string $ordem Número da ordem
 * @return bool Resultado da operação
 */
function processBomba($post, $ordem) {
    // Sanitiza os dados de entrada
    $pressao_oleo_min = sanitizeInput($post['pressao_oleo_min'] ?? 0, 'float');
    $pressao_oleo_max = sanitizeInput($post['pressao_oleo_max'] ?? 0, 'float');
    $pressao_combustao = sanitizeInput($post['pressao_combustao'] ?? 0, 'float');
    $vazao_combustao_min = sanitizeInput($post['vazao_combustao_min'] ?? 0, 'float');
    $vazao_combustao_max = sanitizeInput($post['vazao_combustao_max'] ?? 0, 'float');
    $is_reference = 1;
    
    // Verifica se existe um registro com is_reference = 1
    $recordId = checkRecordExists('bomba', 'is_reference = 1', [], '');
    
    // Define campos e valores para a operação
    $fields = [
        'comb_pressao', 'pressao_oleo_min', 'pressao_oleo_max', 
        'vazao_min', 'vazao_max', 'ordem'
    ];
    
    $values = [
        $pressao_combustao, $pressao_oleo_min, $pressao_oleo_max,
        $vazao_combustao_min, $vazao_combustao_max, $ordem
    ];
    
    // Se for um insert, adiciona is_reference
    if ($recordId === null) {
        $fields[] = 'is_reference';
        $values[] = $is_reference;
        return processRecord('bomba', $fields, $values, 'dddddsi');
    } else {
        // Se for um update, usa o ID para a cláusula WHERE
        return processRecord('bomba', $fields, $values, 'dddddsi', 'id = ?', $recordId);
    }
}

/**
 * Processa informações do cabeçote
 * @param array $post Dados do POST
 * @param string $ordem Número da ordem
 * @return bool Resultado da operação
 */
function processCabecote($post, $ordem) {
    // Sanitiza os dados de entrada
    $engine_type = sanitizeInput($post['engine_type'] ?? '');
    $valve_type = sanitizeInput($post['valve_type'] ?? '');
    $num_cilindros = sanitizeInput($post['num_cilindros'] ?? 0, 'int');
    $num_val_adm = sanitizeInput($post['num_val_adm'] ?? 0, 'int');
    $num_val_esc = sanitizeInput($post['num_val_esc'] ?? 0, 'int');
    $val_adm_limite_min = sanitizeInput($post['val_adm_limite_min'] ?? 0, 'float');
    $val_adm_limite_max = sanitizeInput($post['val_adm_limite_max'] ?? 0, 'float');
    $val_esc_limite_min = sanitizeInput($post['val_esc_limite_min'] ?? 0, 'float');
    $val_esc_limite_max = sanitizeInput($post['val_esc_limite_max'] ?? 0, 'float');
    $compressao_min = sanitizeInput($post['compressao_min'] ?? 0, 'float');
    $compressao_max = sanitizeInput($post['compressao_max'] ?? 0, 'float');
    $tucho = isset($post['tucho']) ? 1 : 0;
    $is_reference = 1;
    $came_adm_altura_min = sanitizeInput($post['came_adm_altura_min'] ?? 0, 'float');
    $came_esc_altura_min = sanitizeInput($post['came_esc_altura_min'] ?? 0, 'float');
    $eixo_cames_lim_empen = sanitizeInput($post['eixo_cames_lim_empen'] ?? 0, 'float');
    $folga_eixo_mancal = sanitizeInput($post['folga_eixo_mancal'] ?? 0, 'float');
    
    // Define valores baseados no tipo de válvula
    $cames_adm_diam_min = null;
    $cames_adm_diam_max = null;
    $came_diam_min = null;
    
    if ($valve_type === 'dohc') {
        $cames_adm_diam_min = !empty($post['cames_adm_diam_min']) ? sanitizeInput($post['cames_adm_diam_min'], 'float') : null;
        $cames_adm_diam_max = !empty($post['cames_adm_diam_max']) ? sanitizeInput($post['cames_adm_diam_max'], 'float') : null;
    } elseif ($valve_type === 'ohc') {
        $came_diam_min = !empty($post['came_diam_min']) ? sanitizeInput($post['came_diam_min'], 'float') : null;
    }
    
    // Verifica se existe um registro com a mesma ordem e is_reference = 1
    $recordId = checkRecordExists('cabecote', 'ordem = ? AND is_reference = 1', [$ordem], 's');
    
    // Define campos e valores para a operação
    $fields = [
        'motor_tipo', 'tipo_val', 'cilindros', 'val_adm', 'val_esc',
        'cames_diam_min', 'cames_adm_diam_min', 'cames_adm_diam_max',
        'val_adm_limite_min', 'val_adm_limite_max', 'val_esc_limite_min',
        'val_esc_limite_max', 'compressao_min', 'compressao_max', 'tucho',
        'came_adm_altura_min', 'came_esc_altura_min', 'eixo_cames_lim_empen',
        'folga_eixo_mancal'
    ];
    
    $values = [
        $engine_type, $valve_type, $num_cilindros, $num_val_adm, $num_val_esc,
        $came_diam_min, $cames_adm_diam_min, $cames_adm_diam_max,
        $val_adm_limite_min, $val_adm_limite_max, $val_esc_limite_min,
        $val_esc_limite_max, $compressao_min, $compressao_max, $tucho,
        $came_adm_altura_min, $came_esc_altura_min, $eixo_cames_lim_empen,
        $folga_eixo_mancal
    ];
    
    $types = 'ssiiidddddddddsdddd';
    
    if ($recordId === null) {
        // Se for um insert, adiciona ordem e is_reference
        $fields[] = 'ordem';
        $fields[] = 'is_reference';
        $values[] = $ordem;
        $values[] = $is_reference;
        $types .= 'si';
        return processRecord('cabecote', $fields, $values, $types);
    } else {
        // Se for um update, usa a ordem e is_reference para a cláusula WHERE
        return processRecord('cabecote', $fields, $values, $types, 'ordem = ? AND is_reference = 1', $ordem);
    }
}

/**
 * Processa informações do virabrequim
 * @param array $post Dados do POST
 * @param string $ordem Número da ordem
 * @return bool Resultado da operação
 */
function processVirabrequim($post, $ordem) {
    // Sanitiza os dados de entrada
    $rolamento_type = sanitizeInput($post['rolamento_type'] ?? '');
    $folga_lateral_biela = sanitizeInput($post['folga_lateral_biela'] ?? 0, 'int');
    $folga_eixo_bronzina = sanitizeInput($post['folga_eixo_bronzina'] ?? 0, 'int');
    $folga_eixo_mancal = sanitizeInput($post['folga_eixo_mancal'] ?? 0, 'int');
    $folga_lateral_eixo_min = sanitizeInput($post['folga_lateral_eixo_min'] ?? 0, 'int');
    $folga_lateral_eixo_max = sanitizeInput($post['folga_lateral_eixo_max'] ?? 0, 'int');
    $empenamento_max = sanitizeInput($post['empenamento_max'] ?? 0, 'int');
    $is_reference = 1;
    
    // Ajusta valores para o tipo bronzina
    if ($rolamento_type == 'bronzina') {
        $folga_lateral_biela_max = null;
        $folga_lateral_eixo_max = null;
        $empenamento_max = null;
    }
    
    // Verifica se existe um registro com is_reference = 1
    $recordId = checkRecordExists('virabrequim', 'is_reference = 1', [], '');
    
    // Define campos e valores para a operação
    $fields = [
        'tipo', 'folga_lateral_biela', 'folga_bronzina', 'folga_mancal',
        'folga_lateral_eixo_min', 'folga_lateral_eixo_max', 'empenamento', 'ordem'
    ];
    
    $values = [
        $rolamento_type, $folga_lateral_biela, $folga_eixo_bronzina, $folga_eixo_mancal,
        $folga_lateral_eixo_min, $folga_lateral_eixo_max, $empenamento_max, $ordem
    ];
    
    $types = 'siiiiiis';
    
    if ($recordId === null) {
        // Se for um insert, adiciona is_reference
        $fields[] = 'is_reference';
        $values[] = $is_reference;
        $types .= 'i';
        return processRecord('virabrequim', $fields, $values, $types);
    } else {
        // Se for um update, usa o ID para a cláusula WHERE
        return processRecord('virabrequim', $fields, $values, $types, 'id = ?', $recordId);
    }
}

/**
 * Processa informações da embreagem
 * @param array $post Dados do POST
 * @param string $ordem Número da ordem
 * @return bool Resultado da operação
 */
function processEmbreagem($post, $ordem) {
    // Sanitiza os dados de entrada
    $nr_discos = sanitizeInput($post['nr_discos'] ?? 0, 'int');
    $nr_discos_sep = sanitizeInput($post['nr_discos_sep'] ?? 0, 'int');
    $disco_fric_esp_min = sanitizeInput($post['disco_fric_esp_min'] ?? 0, 'float');
    $disco_sep_emp_max = sanitizeInput($post['disco_sep_emp_max'] ?? 0, 'float');
    $is_reference = 1;
    
    // Verifica se existe um registro com a mesma ordem e is_reference = 1
    $recordId = checkRecordExists('embreagem', 'ordem = ? AND is_reference = 1', [$ordem], 's');
    
    // Define campos e valores para a operação
    $fields = [
        'disco_friccao', 'disco_separador', 
        'disco_friccao_espes_min', 'disco_separador_emp_max'
    ];
    
    $values = [
        $nr_discos, $nr_discos_sep, 
        $disco_fric_esp_min, $disco_sep_emp_max
    ];
    
    $types = 'iidd';
    
    if ($recordId === null) {
        // Se for um insert, adiciona ordem e is_reference
        $fields[] = 'ordem';
        $fields[] = 'is_reference';
        $values[] = $ordem;
        $values[] = $is_reference;
        $types .= 'si';
        return processRecord('embreagem', $fields, $values, $types);
    } else {
        // Se for um update, usa a ordem e is_reference para a cláusula WHERE
        return processRecord('embreagem', $fields, $values, $types, 'ordem = ? AND is_reference = 1', $ordem);
    }
}

/**
 * Processa informações do motor
 * @param array $post Dados do POST
 * @param string $ordem Número da ordem
 * @return bool Resultado da operação
 */
function processMotor($post, $ordem) {
    // Sanitiza os dados de entrada
    $nr_cilindros = sanitizeInput($post['nr_cilindros'] ?? 0, 'int');
    $curso_pistao = sanitizeInput($post['curso_pistao'] ?? 0, 'float');
    $diametro_cilindro_max = sanitizeInput($post['diametro_cilindro_max'] ?? 0, 'float');
    $conicidade_max = sanitizeInput($post['conicidade_max'] ?? 0, 'float');
    $ovalizacao_max = sanitizeInput($post['ovalizacao_max'] ?? 0, 'float');
    $diametro_pistao_min = sanitizeInput($post['diametro_pistao_min'] ?? 0, 'float');
    $folga_cil_pis_max = sanitizeInput($post['folga_cil_pis_max'] ?? 0, 'float');
    $aber_anel_1_max = sanitizeInput($post['aber_anel_1_max'] ?? 0, 'float');
    $aber_anel_2_max = sanitizeInput($post['aber_anel_2_max'] ?? 0, 'float');
    $aber_anel_1_pres_min = sanitizeInput($post['aber_anel_1_pres_min'] ?? 0, 'float');
    $aber_anel_2_pres_min = sanitizeInput($post['aber_anel_2_pres_min'] ?? 0, 'float');
    $larg_anel_1_min = sanitizeInput($post['larg_anel_1_min'] ?? 0, 'float');
    $larg_anel_2_min = sanitizeInput($post['larg_anel_2_min'] ?? 0, 'float');
    $dia_furo_pis_min = sanitizeInput($post['dia_furo_pis_min'] ?? 0, 'float');
    $dia_pino_pis_min = sanitizeInput($post['dia_pino_pis_min'] ?? 0, 'float');
    $folga_pino_pis_max = sanitizeInput($post['folga_pino_pis_max'] ?? 0, 'float');
    $is_reference = 1;
    
    // Valida campos obrigatórios
    if (empty($ordem)) {
        return false;
    }
    
    // Verifica se existe um registro com a mesma ordem e is_reference = 1
    $recordId = checkRecordExists('motor', 'ordem = ? AND is_reference = 1', [$ordem], 's');
    
    // Define campos e valores para a operação
    $fields = [
        'nr_cilindros', 'curso_pistao', 'diametro_cilindro_max', 'conicidade_max',
        'ovalizacao_max', 'diametro_pistao_min', 'folga_cil_pis_max', 'aber_anel_1_max',
        'aber_anel_2_max', 'aber_anel_1_pres_min', 'aber_anel_2_pres_min', 'larg_anel_1_min',
        'larg_anel_2_min', 'dia_furo_pis_min', 'dia_pino_pis_min', 'folga_pino_pis_max'
    ];
    
    $values = [
        $nr_cilindros, $curso_pistao, $diametro_cilindro_max, $conicidade_max,
        $ovalizacao_max, $diametro_pistao_min, $folga_cil_pis_max, $aber_anel_1_max,
        $aber_anel_2_max, $aber_anel_1_pres_min, $aber_anel_2_pres_min, $larg_anel_1_min,
        $larg_anel_2_min, $dia_furo_pis_min, $dia_pino_pis_min, $folga_pino_pis_max
    ];
    
    $types = 'iddddddddddddddd';
    
    if ($recordId === null) {
        // Se for um insert, adiciona ordem e is_reference
        $fields[] = 'ordem';
        $fields[] = 'is_reference';
        $values[] = $ordem;
        $values[] = $is_reference;
        $types .= 'si';
        return processRecord('motor', $fields, $values, $types);
    } else {
        // Se for um update, usa a ordem e is_reference para a cláusula WHERE
        return processRecord('motor', $fields, $values, $types, 'ordem = ? AND is_reference = 1', $ordem);
    }
}

// Obtém o número da ordem de serviço
$ordem = isset($_GET['ordem']) ? sanitizeInput($_GET['ordem']) : null;

// Processa as informações básicas do motor
processInfoMotor($_POST, $ordem);

// Processa o componente selecionado
$option = isset($_POST['selected_option']) ? sanitizeInput($_POST['selected_option']) : null;

$success = false;

switch ($option) {
    case 'bomba':
        $success = processBomba($_POST, $ordem);
        break;
    case 'cabecote':
        $success = processCabecote($_POST, $ordem);
        break;
    case 'virabrequim':
        $success = processVirabrequim($_POST, $ordem);
        break;
    case 'embreagem':
        $success = processEmbreagem($_POST, $ordem);
        break;
    case 'motor':
        $success = processMotor($_POST, $ordem);
        break;
    default:
        echo "<br>Opção não definida: " . var_export($option, true) . PHP_EOL;
        break;
}

// Mensagem com o resultado da operação (será ignorada pelo redirecionamento)
if ($success) {
    echo "Dados salvos com sucesso!";
} else if ($option !== null) {
    echo "Erro ao processar os dados.";
}

// Redireciona após o processamento
header('Location: ../../ordemservico.php?ordem=' . (string)$ordem);
exit;
