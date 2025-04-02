<?php

// Verificação inicial do parâmetro ordem
if (!isset($_GET['ordem'])) {
    die("<div class='error-msg'>Erro: Parâmetro 'ordem' não foi especificado na URL.</div>");
}

// Usar a ordem como string
$ordem = $_GET['ordem'];

// Verificação da conexão com o banco de dados
if (!isset($conn) || !$conn) {
    die("<div class='error-msg'>Erro: Conexão com o banco de dados não estabelecida.</div>");
}

// Verificar se existem dados de referência para a ordem especificada
$checkQuery = "SELECT COUNT(*) as count FROM cabecote WHERE is_reference = 1 AND ordem = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($checkStmt, "s", $ordem);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$checkRow = mysqli_fetch_assoc($checkResult);

// Funções auxiliares
function calcularValorMedio($min, $max) {
    return (floatval($min) + floatval($max)) / 2;
}

function formatarIntervalo($min, $max) {
    return number_format(floatval($min), 2, ',', '.') . " a " . number_format(floatval($max), 2, ',', '.');
}

function calcularPC($folga, $referencia, $pastilha_antiga) {
    return (floatval($folga) - floatval($referencia)) + floatval($pastilha_antiga);
}

function displayTableData($conn, $tableName, $tableTitle) {
    if (!isset($_GET['ordem'])) {
        echo "<p>Parâmetro 'ordem' inválido ou não fornecido.</p>";
        return;
    }

    $ordem = $_GET['ordem'];

    // Handle form submission for updating values
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === $tableName) {
        if (isset($_POST['update'])) {
            // Buscar o ID do registro de referência
            $refQuery = "SELECT id FROM $tableName WHERE is_reference = 1 AND ordem = ?";
            $refStmt = mysqli_prepare($conn, $refQuery);
            mysqli_stmt_bind_param($refStmt, "s", $ordem);
            mysqli_stmt_execute($refStmt);
            $refResult = mysqli_stmt_get_result($refStmt);
            $refRow = mysqli_fetch_assoc($refResult);
            
            if ($refRow) {
                $refId = $refRow['id'];
                
                // Atualizar valores na referência
                foreach ($_POST['measured'] as $fields) {
                    $updates = [];
                    $params = [];
                    $types = '';
                    
                    foreach ($fields as $field => $value) {
                        // Converter vírgula para ponto se for um número
                        if (is_numeric(str_replace(',', '.', $value))) {
                            $value = str_replace(',', '.', $value);
                        }
                        
                        $updates[] = "`$field` = ?";
                        $params[] = $value;
                        $types .= is_numeric($value) ? 'd' : 's';
                    }
                    
                    $params[] = $refId;
                    $types .= 'i';

                    $updateQuery = "UPDATE " . $tableName . 
                                  " SET " . implode(', ', $updates) . 
                                  " WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $updateQuery);
                    if ($stmt) {
                        if (count($params) > 0) {
                            mysqli_stmt_bind_param($stmt, $types, ...$params);
                        }
                        if (mysqli_stmt_execute($stmt)) {
                            echo "<p class='success-msg'>Alterações salvas com sucesso!</p>";
                        } else {
                            echo "<p class='error-msg'>Erro ao salvar alterações: " . mysqli_error($conn) . "</p>";
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            }
        }
    }

    // Query for reference values
    $refQuery = "SELECT * FROM $tableName WHERE is_reference = 1 AND ordem = ?";
    $refStmt = mysqli_prepare($conn, $refQuery);
    mysqli_stmt_bind_param($refStmt, "s", $ordem);
    mysqli_stmt_execute($refStmt);
    $refResult = mysqli_stmt_get_result($refStmt);

    // Query for measured values
    $measQuery = "SELECT * FROM $tableName WHERE is_reference = 0 AND ordem = ? ORDER BY id DESC";
    $measStmt = mysqli_prepare($conn, $measQuery);
    mysqli_stmt_bind_param($measStmt, "s", $ordem);
    mysqli_stmt_execute($measStmt);
    $measResult = mysqli_stmt_get_result($measStmt);

    echo "<div class='card'>";
    echo "<h2 class='card-title'>" . htmlspecialchars($tableTitle) . "</h2>";
    echo "<div class='card-content'>";

    if (mysqli_num_rows($refResult) > 0) {
        $refRow = mysqli_fetch_assoc($refResult);

        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='" . htmlspecialchars($tableName) . "'>";
        echo "<input type='hidden' name='update' value='1'>";

        echo "<div class='table-container'>";
        echo "<table>";
        echo "<thead><tr><th>Parâmetro</th><th>Referência</th><th>Novo Valor</th></tr></thead>";
        echo "<tbody>";

        foreach ($refRow as $key => $value) {
            $keyLower = strtolower($key);
            
            // Verificar se é um campo que deve ser ignorado para rolamento
            if ($tableName === 'virabrequim') {
                $tipoAtual = '';
                foreach ($refRow as $k => $v) {
                    if (strtolower($k) === 'tipo') {
                        $tipoAtual = $v;
                        break;
                    }
                }
                
                // Se for rolamento e for um dos campos específicos, pular
                if ($tipoAtual === 'Rolamento' && 
                    in_array($keyLower, [
                        'folga_lateral_biela',
                        'folga_lateral_eixo_min',
                        'folga_lateral_eixo_max',
                        'empenamento'
                    ])) {
                    continue;
                }
            }
            
            if ($keyLower !== 'id' && 
                $keyLower !== 'ordem' && 
                $keyLower !== 'is_reference' && 
                $value !== null &&
                !($tableName === 'cabecote' && ($keyLower === 'motor_tipo' || $keyLower === 'tipo_val')) &&
                !($tableName === 'motor' && ($keyLower === 'created_at' || $keyLower === 'updated_at'))) {
                
                echo "<tr>";
                echo "<td class='data-label'>" . 
                     htmlspecialchars(ucfirst(str_replace("_", " ", $key))) . 
                     "</td>";
                
                // Tratamento especial para o campo tucho
                if ($tableName === 'cabecote' && $keyLower === 'tucho') {
                    $displayValue = ($value == 1) ? 'Sim' : 'Não';
                    echo "<td class='ref-value'>" . 
                         htmlspecialchars($displayValue) . 
                         "</td>";
                    
                    echo "<td class='meas-values'>";
                    echo "<select name='measured[" . $refRow['id'] . "][" . htmlspecialchars($key) . "]' " .
                         "class='meas-input first'>";
                    echo "<option value='1' " . ($value == 1 ? 'selected' : '') . ">Sim</option>";
                    echo "<option value='0' " . ($value == 0 ? 'selected' : '') . ">Não</option>";
                    echo "</select>";
                    echo "</td>";
                } 
                // Tratamento especial para o campo tipo do virabrequim
                else if ($tableName === 'virabrequim' && $keyLower === 'tipo') {
                    echo "<td class='ref-value'>" . 
                         htmlspecialchars($value) . 
                         "</td>";
                    
                    echo "<td class='meas-values'>";
                    echo "<select name='measured[" . $refRow['id'] . "][" . htmlspecialchars($key) . "]' " .
                         "class='meas-input first' " .
                         "onchange='toggleVirabrequimFields(this.value)'>";
                    echo "<option value='Rolamento' " . ($value == 'Rolamento' ? 'selected' : '') . ">Rolamento</option>";
                    echo "<option value='Bronzina' " . ($value == 'Bronzina' ? 'selected' : '') . ">Bronzina</option>";
                    echo "</select>";
                    echo "</td>";
                }
                else {
                    echo "<td class='ref-value'>" . 
                         htmlspecialchars($value) . 
                         "</td>";
                    
                    echo "<td class='meas-values'>";
                    echo "<input type='text' " .
                         "name='measured[" . $refRow['id'] . "][" . htmlspecialchars($key) . "]' " .
                         "value='" . htmlspecialchars($value) . "' " .
                         "class='meas-input first'>";
                    echo "</td>";
                }
                
                echo "</tr>";
            }
        }

        echo "</tbody></table>";
        echo "</div>";

        echo "<button type='submit' class='save-btn'>Salvar Alterações</button>";
        echo "</form>";
    } else {
        echo "<div class='info-msg'>Nenhum dado de referência encontrado para esta ordem de serviço.</div>";
        echo "<div class='info-msg'>Por favor, adicione os dados de referência primeiro.</div>";
    }

    echo "</div>";
    echo "</div>";

    mysqli_stmt_close($refStmt);
    mysqli_stmt_close($measStmt);
}

function displayCabecoteMedicoes($conn, $ordem) {
    try {
        // Processar salvamento se for POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === 'cabecote') {
            $queryMed = "SELECT medicoes FROM cabecote WHERE is_reference = 0 AND ordem = ?";
            $stmtMed = mysqli_prepare($conn, $queryMed);
            mysqli_stmt_bind_param($stmtMed, "s", $ordem);
            mysqli_stmt_execute($stmtMed);
            $resultMed = mysqli_stmt_get_result($stmtMed);
            $cabecoteMed = mysqli_fetch_assoc($resultMed);
            
            $medicoesExistentes = $cabecoteMed && $cabecoteMed['medicoes'] ? json_decode($cabecoteMed['medicoes'], true) : [];
            $medicoes = $medicoesExistentes;
            
            if (isset($_POST['medida'])) {
                foreach ($_POST['medida'] as $param => $cilindros) {
                    foreach ($cilindros as $cilindro => $valor) {
                        $medicoes[$param][$cilindro] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                    }
                }
            }
            
            $checkQuery = "SELECT id FROM cabecote WHERE is_reference = 0 AND ordem = ?";
            $stmtCheck = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($stmtCheck, "s", $ordem);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            
            if (mysqli_num_rows($resultCheck) > 0) {
                $updateQuery = "UPDATE cabecote SET medicoes = ? WHERE is_reference = 0 AND ordem = ?";
                $stmtUpdate = mysqli_prepare($conn, $updateQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtUpdate, "ss", $jsonMedicoes, $ordem);
                if (!mysqli_stmt_execute($stmtUpdate)) {
                    throw new Exception("Erro ao atualizar medições: " . mysqli_stmt_error($stmtUpdate));
                }
            } else {
                $insertQuery = "INSERT INTO cabecote (ordem, is_reference, medicoes) VALUES (?, 0, ?)";
                $stmtInsert = mysqli_prepare($conn, $insertQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtInsert, "ss", $ordem, $jsonMedicoes);
                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Erro ao inserir medições: " . mysqli_stmt_error($stmtInsert));
                }
            }
            echo "<div class='success-msg'>Medições salvas com sucesso!</div>";
        }

        // Buscar dados de referência do cabeçote
        $queryRef = "SELECT * FROM cabecote WHERE is_reference = 1 AND ordem = ?";
        $stmtRef = mysqli_prepare($conn, $queryRef);
        if (!$stmtRef) {
            throw new Exception("Erro ao preparar consulta de referência: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmtRef, "s", $ordem);
        mysqli_stmt_execute($stmtRef);
        $resultRef = mysqli_stmt_get_result($stmtRef);
        $cabecoteRef = mysqli_fetch_assoc($resultRef);

        // Se não houver referência, usar valores padrão
        if (!$cabecoteRef) {
            $cabecoteRef = [
                'motor_tipo' => '',
                'cilindros' => 0,
                'val_adm' => 0,
                'val_esc' => 0,
                'val_adm_limite_min' => 0.00,
                'val_adm_limite_max' => 0.00,
                'val_esc_limite_min' => 0.00,
                'val_esc_limite_max' => 0.00,
                'cames_adm_diam_max' => 0.00,
                'cames_esc_diam_max' => 0.00,  // Adicionado para evitar o aviso
                'cames_diam_min' => 0.00,
                'compressao_min' => 0.00,
                'compressao_max' => 0.00,
                'tucho' => 0,
                'came_adm_altura_min' => 0.00,
                'came_esc_altura_min' => 0.00
            ];
            echo "<div class='warning-msg'>Nenhuma referência encontrada para ordem #" . htmlspecialchars($ordem) . ". Exibindo tabela vazia.</div>";
        }

        // Buscar medições existentes
        $queryMed = "SELECT medicoes FROM cabecote WHERE is_reference = 0 AND ordem = ?";
        $stmtMed = mysqli_prepare($conn, $queryMed);
        mysqli_stmt_bind_param($stmtMed, "s", $ordem);
        mysqli_stmt_execute($stmtMed);
        $resultMed = mysqli_stmt_get_result($stmtMed);
        $cabecoteMed = mysqli_fetch_assoc($resultMed);
        
        $medicoes = $cabecoteMed && $cabecoteMed['medicoes'] ? json_decode($cabecoteMed['medicoes'], true) : [];

        // Calcular cilindros de trás e frente
        $cilindros_tras = ceil($cabecoteRef['cilindros'] / 2);
        $cilindros_frente = $cabecoteRef['cilindros'] - $cilindros_tras;

        // Exibir interface
        echo "<div class='card cabecote-medicoes'>";
        echo "<h2 class='card-title'>MENU MEDIÇÕES CABEÇOTE</h2>";
        echo "<div class='legenda'>Medição de válvulas para cada cilindro</div>";
        echo "<div> Tipo do motor: <div class='subtitulo'> " . htmlspecialchars($cabecoteRef['motor_tipo']) . "</div></div>";
        
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='cabecote'>";
        echo "<input type='hidden' name='ordem' value='" . htmlspecialchars($ordem) . "'>";
        echo "<table>";
        
        // Cabeçalho da tabela
        echo "<thead><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        for ($i = 1; $i <= $cilindros_tras; $i++) {
            echo "<th class='cilindro-tras'>CILINDRO " . $i . "</th>";
        }
        for ($i = $cilindros_tras + 1; $i <= $cabecoteRef['cilindros']; $i++) {
            echo "<th class='cilindro-frente'>CILINDRO " . $i . "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";

        // Válvulas de admissão
        for ($i = 1; $i <= $cabecoteRef['val_adm']; $i++) {
            $lado = ($i == 1) ? 'direita' : 'esquerda';
            if ($cabecoteRef['val_adm_limite_min'] > 0 && $cabecoteRef['val_adm_limite_max'] > 0) {
                echo "<tr class='valvula-admissao'>";
                echo "<td>Folga válvula admissão " . $lado . "</td>";
                echo "<td>" . formatarIntervalo($cabecoteRef['val_adm_limite_min'], $cabecoteRef['val_adm_limite_max']) . "</td>";
                for ($c = 1; $c <= $cabecoteRef['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    $valor = isset($medicoes['adm_folga_' . $lado][$c]) && $medicoes['adm_folga_' . $lado][$c] !== null ? number_format($medicoes['adm_folga_' . $lado][$c], 2, ',', '.') : '';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<input type='text' name='medida[adm_folga_" . $lado . "][" . $c . "]' class='meas-input folga-input' data-cilindro='" . $c . "' data-tipo='adm' data-lado='" . $lado . "' onchange='calcularPastilha(this)' value='$valor'>";
                    echo "</td>";
                }
                echo "</tr>";
            }

            if (isset($cabecoteRef['tucho']) && $cabecoteRef['tucho'] == 1) {
                echo "<tr class='valvula-admissao'>";
                echo "<td>Pastilha válvula admissão " . $lado . "</td>";
                echo "<td>-</td>";
                for ($c = 1; $c <= $cabecoteRef['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    $valor = isset($medicoes['adm_pastilha_' . $lado][$c]) && $medicoes['adm_pastilha_' . $lado][$c] !== null ? number_format($medicoes['adm_pastilha_' . $lado][$c], 2, ',', '.') : '';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<div class='pastilha-container'>";
                    echo "<input type='text' name='medida[adm_pastilha_" . $lado . "][" . $c . "]' class='meas-input pastilha-input' data-cilindro='" . $c . "' data-tipo='adm' data-lado='" . $lado . "' value='$valor'>";
                    echo "<div class='pastilha-corrigida' id='pc_adm_" . $lado . "_" . $c . "'>-</div>";
                    echo "</div>";
                    echo "</td>";
                }
                echo "</tr>";
            }
        }

        // Válvulas de escape
        for ($i = 1; $i <= $cabecoteRef['val_esc']; $i++) {
            $lado = ($i == 1) ? 'direita' : 'esquerda';
            if ($cabecoteRef['val_esc_limite_min'] > 0 && $cabecoteRef['val_esc_limite_max'] > 0) {
                echo "<tr class='valvula-escape'>";
                echo "<td>Folga válvula escape " . $lado . "</td>";
                echo "<td>" . formatarIntervalo($cabecoteRef['val_esc_limite_min'], $cabecoteRef['val_esc_limite_max']) . "</td>";
                for ($c = 1; $c <= $cabecoteRef['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    $valor = isset($medicoes['esc_folga_' . $lado][$c]) && $medicoes['esc_folga_' . $lado][$c] !== null ? number_format($medicoes['esc_folga_' . $lado][$c], 2, ',', '.') : '';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<input type='text' name='medida[esc_folga_" . $lado . "][" . $c . "]' class='meas-input folga-input' data-cilindro='" . $c . "' data-tipo='esc' data-lado='" . $lado . "' onchange='calcularPastilha(this)' value='$valor'>";
                    echo "</td>";
                }
                echo "</tr>";
            }

            if (isset($cabecoteRef['tucho']) && $cabecoteRef['tucho'] == 1) {
                echo "<tr class='valvula-escape'>";
                echo "<td>Pastilha válvula escape " . $lado . "</td>";
                echo "<td>-</td>";
                for ($c = 1; $c <= $cabecoteRef['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    $valor = isset($medicoes['esc_pastilha_' . $lado][$c]) && $medicoes['esc_pastilha_' . $lado][$c] !== null ? number_format($medicoes['esc_pastilha_' . $lado][$c], 2, ',', '.') : '';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<div class='pastilha-container'>";
                    echo "<input type='text' name='medida[esc_pastilha_" . $lado . "][" . $c . "]' class='meas-input pastilha-input' data-cilindro='" . $c . "' data-tipo='esc' data-lado='" . $lado . "' value='$valor'>";
                    echo "<div class='pastilha-corrigida' id='pc_esc_" . $lado . "_" . $c . "'>-</div>";
                    echo "</div>";
                    echo "</td>";
                }
                echo "</tr>";
            }
        }

        // Compressão
        if ($cabecoteRef['compressao_min'] > 0 && $cabecoteRef['compressao_max'] > 0) {
            echo "<tr class='valvula-admissao'>";
            echo "<td>Compressão</td>";
            echo "<td>" . formatarIntervalo($cabecoteRef['compressao_min'], $cabecoteRef['compressao_max']) . "</td>";
            for ($c = 1; $c <= $cabecoteRef['cilindros']; $c++) {
                $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                $valor = isset($medicoes['compressao'][$c]) && $medicoes['compressao'][$c] !== null ? number_format($medicoes['compressao'][$c], 2, ',', '.') : '';
                echo "<td class='" . $classe_cilindro . "'>";
                echo "<input type='text' name='medida[compressao][" . $c . "]' class='meas-input' value='$valor'>";
                echo "</td>";
            }
            echo "</tr>";
        }

        // Separador
        echo "<tr class='separador'>";
        echo "<td colspan='" . ($cabecoteRef['cilindros'] + 2) . "'>MEDIÇÕES GERAIS DO CABEÇOTE</td>";
        echo "</tr>";

        // Itens fixos
        if ($cabecoteRef['cames_adm_diam_max'] > 0) {
            echo "<tr class='item-fixo'>";
            echo "<td>Diâmetro eixo cames admissão</td>";
            echo "<td>" . number_format($cabecoteRef['cames_adm_diam_max'], 2, ',', '.') . "</td>";
            for ($cil = 1; $cil <= $cabecoteRef['cilindros']; $cil++) {
                $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                echo "<td class='" . $classe_cilindro . "'>-</td>";
            }
            echo "</tr>";
        }

        if ($cabecoteRef['cames_esc_diam_max'] > 0) {
            echo "<tr class='item-fixo'>";
            echo "<td>Diâmetro eixo cames escape</td>";
            echo "<td>" . number_format($cabecoteRef['cames_esc_diam_max'], 2, ',', '.') . "</td>";
            for ($cil = 1; $cil <= $cabecoteRef['cilindros']; $cil++) {
                $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                echo "<td class='" . $classe_cilindro . "'>-</td>";
            }
            echo "</tr>";
        }

        echo "<tr class='item-fixo'>";
        echo "<td>Empenamento eixo cames adm/esc</td>";
        echo "<td>0,10</td>";
        for ($cil = 1; $cil <= $cabecoteRef['cilindros']; $cil++) {
            $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['eixo_cames_lim_empen'][$cil]) && $medicoes['eixo_cames_lim_empen'][$cil] !== null ? number_format($medicoes['eixo_cames_lim_empen'][$cil], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'>";
            echo "<input type='text' name='medida[eixo_cames_lim_empen][" . $cil . "]' class='meas-input' value='$valor'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Folga eixo de cames/mancal</td>";
        echo "<td>0,15</td>";
        for ($cil = 1; $cil <= $cabecoteRef['cilindros']; $cil++) {
            $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['folga_eixo_mancal'][$cil]) && $medicoes['folga_eixo_mancal'][$cil] !== null ? number_format($medicoes['folga_eixo_mancal'][$cil], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'>";
            echo "<input type='text' name='medida[folga_eixo_mancal][" . $cil . "]' class='meas-input' value='$valor'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Came admissão altura min</td>";
        echo "<td>" . number_format($cabecoteRef['came_adm_altura_min'], 2, ',', '.') . "</td>";
        for ($cil = 1; $cil <= $cabecoteRef['cilindros']; $cil++) {
            $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['came_adm_altura_min'][$cil]) && $medicoes['came_adm_altura_min'][$cil] !== null ? number_format($medicoes['came_adm_altura_min'][$cil], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'>";
            echo "<input type='text' name='medida[came_adm_altura_min][" . $cil . "]' class='meas-input' value='$valor'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Came escape altura min</td>";
        echo "<td>" . number_format($cabecoteRef['came_esc_altura_min'], 2, ',', '.') . "</td>";
        for ($cil = 1; $cil <= $cabecoteRef['cilindros']; $cil++) {
            $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['came_esc_altura_min'][$cil]) && $medicoes['came_esc_altura_min'][$cil] !== null ? number_format($medicoes['came_esc_altura_min'][$cil], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'>";
            echo "<input type='text' name='medida[came_esc_altura_min][" . $cil . "]' class='meas-input' value='$valor'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "</tbody></table>";
        echo "<button type='submit' class='save-btn'>Salvar Medições</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro ao exibir medições do cabeçote: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

function displayEmbreagemMedicoes($conn, $ordem) {
    try {
        // Processar salvamento se for POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === 'embreagem') {
            // Buscar medições existentes antes de processar o POST
            $queryMed = "SELECT medicoes_friccao, medicoes_separador FROM embreagem WHERE is_reference = 0 AND ordem = ?";
            $stmtMed = mysqli_prepare($conn, $queryMed);
            mysqli_stmt_bind_param($stmtMed, "s", $ordem);
            mysqli_stmt_execute($stmtMed);
            $resultMed = mysqli_stmt_get_result($stmtMed);
            $embreagemMed = mysqli_fetch_assoc($resultMed);
            
            $medicoesFriccaoExistentes = $embreagemMed && $embreagemMed['medicoes_friccao'] ? json_decode($embreagemMed['medicoes_friccao'], true) : [];
            $medicoesSeparadorExistentes = $embreagemMed && $embreagemMed['medicoes_separador'] ? json_decode($embreagemMed['medicoes_separador'], true) : [];
            
            $medicoesFriccao = $medicoesFriccaoExistentes;
            $medicoesSeparador = $medicoesSeparadorExistentes;
            
            // Verificar se 'medida' existe e mesclar com valores existentes
            if (isset($_POST['medida'])) {
                if (isset($_POST['medida']['disco_friccao_espes'])) {
                    foreach ($_POST['medida']['disco_friccao_espes'] as $i => $valor) {
                        $medicoesFriccao[$i] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                    }
                }
                if (isset($_POST['medida']['disco_separador_emp'])) {
                    foreach ($_POST['medida']['disco_separador_emp'] as $i => $valor) {
                        $medicoesSeparador[$i] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                    }
                }
            }
            
            // Verificar se já existe registro de medições
            $checkQuery = "SELECT id FROM embreagem WHERE is_reference = 0 AND ordem = ?";
            $stmtCheck = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($stmtCheck, "s", $ordem);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            
            if (mysqli_num_rows($resultCheck) > 0) {
                // Atualizar registro existente
                $updateQuery = "UPDATE embreagem SET medicoes_friccao = ?, medicoes_separador = ? WHERE is_reference = 0 AND ordem = ?";
                $stmtUpdate = mysqli_prepare($conn, $updateQuery);
                $jsonFriccao = json_encode($medicoesFriccao);
                $jsonSeparador = json_encode($medicoesSeparador);
                mysqli_stmt_bind_param($stmtUpdate, "sss", $jsonFriccao, $jsonSeparador, $ordem);
                if (!mysqli_stmt_execute($stmtUpdate)) {
                    throw new Exception("Erro ao atualizar medições: " . mysqli_stmt_error($stmtUpdate));
                }
            } else {
                // Inserir novo registro
                $insertQuery = "INSERT INTO embreagem (ordem, is_reference, medicoes_friccao, medicoes_separador) VALUES (?, 0, ?, ?)";
                $stmtInsert = mysqli_prepare($conn, $insertQuery);
                $jsonFriccao = json_encode($medicoesFriccao);
                $jsonSeparador = json_encode($medicoesSeparador);
                mysqli_stmt_bind_param($stmtInsert, "sss", $ordem, $jsonFriccao, $jsonSeparador);
                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Erro ao inserir medições: " . mysqli_stmt_error($stmtInsert));
                }
            }
            echo "<div class='success-msg'>Medições salvas com sucesso!</div>";
        }

        // Buscar dados de referência
        $queryRef = "SELECT * FROM embreagem WHERE is_reference = 1 AND ordem = ?";
        $stmtRef = mysqli_prepare($conn, $queryRef);
        if (!$stmtRef) {
            throw new Exception("Erro ao preparar consulta de referência: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmtRef, "s", $ordem);
        mysqli_stmt_execute($stmtRef);
        $resultRef = mysqli_stmt_get_result($stmtRef);
        $embreagemRef = mysqli_fetch_assoc($resultRef);

        // Se não houver referência, usar valores padrão
        if (!$embreagemRef) {
            $embreagemRef = [
                'disco_friccao' => 0,
                'disco_friccao_espes_min' => 0.0,
                'disco_separador' => 0,
                'disco_separador_emp_max' => 0.0
            ];
            echo "<div class='warning-msg'>Nenhuma referência encontrada para ordem #" . htmlspecialchars($ordem) . ". Exibindo tabela vazia.</div>";
        }

        // Buscar medições existentes
        $queryMed = "SELECT medicoes_friccao, medicoes_separador FROM embreagem WHERE is_reference = 0 AND ordem = ?";
        $stmtMed = mysqli_prepare($conn, $queryMed);
        mysqli_stmt_bind_param($stmtMed, "s", $ordem);
        mysqli_stmt_execute($stmtMed);
        $resultMed = mysqli_stmt_get_result($stmtMed);
        $embreagemMed = mysqli_fetch_assoc($resultMed);
        
        $medicoesFriccao = $embreagemMed && $embreagemMed['medicoes_friccao'] ? json_decode($embreagemMed['medicoes_friccao'], true) : [];
        $medicoesSeparador = $embreagemMed && $embreagemMed['medicoes_separador'] ? json_decode($embreagemMed['medicoes_separador'], true) : [];

        // Exibir interface
        echo "<div class='card embreagem-medicoes'>";
        echo "<h2 class='card-title'>MENU MEDIÇÕES EMBREAGEM</h2>";
        echo "<div class='legenda'>Medição de discos de fricção e separadores</div>";
        
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='embreagem'>";
        echo "<input type='hidden' name='ordem' value='" . htmlspecialchars($ordem) . "'>";
        echo "<table>";
        
        // Cabeçalho para discos de fricção
        echo "<thead><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        for ($i = 1; $i <= $embreagemRef['disco_friccao']; $i++) {
            echo "<th>DISCO " . $i . "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";

        // Discos de fricção
        echo "<tr class='disco-friccao'>";
        echo "<td>Espessura mínima disco fricção</td>";
        echo "<td>" . number_format($embreagemRef['disco_friccao_espes_min'], 2, ',', '.') . "</td>";
        
        for ($i = 1; $i <= $embreagemRef['disco_friccao']; $i++) {
            $valor = isset($medicoesFriccao[$i]) && $medicoesFriccao[$i] !== null ? number_format($medicoesFriccao[$i], 2, ',', '.') : '';
            echo "<td><input type='text' 
                name='medida[disco_friccao_espes][" . $i . "]' 
                class='meas-input' 
                value='$valor'></td>";
        }
        echo "</tr>";

        // Separador
        echo "<tr class='separador'>";
        echo "<td colspan='" . ($embreagemRef['disco_friccao'] + 2) . "'>MEDIÇÕES DOS DISCOS SEPARADORES</td>";
        echo "</tr>";

        // Cabeçalho para discos separadores
        echo "<thead><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        for ($i = 1; $i <= $embreagemRef['disco_separador']; $i++) {
            echo "<th>DISCO " . $i . "</th>";
        }
        echo "</tr></thead>";

        // Discos separadores
        echo "<tr class='disco-separador'>";
        echo "<td>Empenamento máximo disco separador</td>";
        echo "<td>" . number_format($embreagemRef['disco_separador_emp_max'], 2, ',', '.') . "</td>";
        
        for ($i = 1; $i <= $embreagemRef['disco_separador']; $i++) {
            $valor = isset($medicoesSeparador[$i]) && $medicoesSeparador[$i] !== null ? number_format($medicoesSeparador[$i], 2, ',', '.') : '';
            echo "<td><input type='text' 
                name='medida[disco_separador_emp][" . $i . "]' 
                class='meas-input' 
                value='$valor'></td>";
        }
        echo "</tr>";

        echo "</tbody></table>";
        echo "<button type='submit' class='save-btn'>Salvar Medições</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
function displayBombaMedicoes($conn, $ordem) {
    try {
        // Processar salvamento se for POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === 'bomba') {
            // Buscar medições existentes antes de processar o POST
            $queryMed = "SELECT medicoes FROM bomba WHERE is_reference = 0 AND ordem = ?";
            $stmtMed = mysqli_prepare($conn, $queryMed);
            mysqli_stmt_bind_param($stmtMed, "s", $ordem);
            mysqli_stmt_execute($stmtMed);
            $resultMed = mysqli_stmt_get_result($stmtMed);
            $bombaMed = mysqli_fetch_assoc($resultMed);
            
            $medicoesExistentes = $bombaMed && $bombaMed['medicoes'] ? json_decode($bombaMed['medicoes'], true) : [];
            $medicoes = $medicoesExistentes;
            
            // Verificar se 'medida' existe e mesclar com valores existentes
            if (isset($_POST['medida'])) {
                foreach ($_POST['medida'] as $param => $valor) {
                    $medicoes[$param] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                }
            }
            
            // Verificar se já existe registro de medições
            $checkQuery = "SELECT id FROM bomba WHERE is_reference = 0 AND ordem = ?";
            $stmtCheck = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($stmtCheck, "s", $ordem);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            
            if (mysqli_num_rows($resultCheck) > 0) {
                // Atualizar registro existente
                $updateQuery = "UPDATE bomba SET medicoes = ? WHERE is_reference = 0 AND ordem = ?";
                $stmtUpdate = mysqli_prepare($conn, $updateQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtUpdate, "ss", $jsonMedicoes, $ordem);
                if (!mysqli_stmt_execute($stmtUpdate)) {
                    throw new Exception("Erro ao atualizar medições: " . mysqli_stmt_error($stmtUpdate));
                }
            } else {
                // Inserir novo registro
                $insertQuery = "INSERT INTO bomba (ordem, is_reference, medicoes) VALUES (?, 0, ?)";
                $stmtInsert = mysqli_prepare($conn, $insertQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtInsert, "ss", $ordem, $jsonMedicoes);
                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Erro ao inserir medições: " . mysqli_stmt_error($stmtInsert));
                }
            }
            echo "<div class='success-msg'>Medições salvas com sucesso!</div>";
        }

        // Buscar dados de referência
        $queryRef = "SELECT * FROM bomba WHERE is_reference = 1 AND ordem = ?";
        $stmtRef = mysqli_prepare($conn, $queryRef);
        if (!$stmtRef) {
            throw new Exception("Erro ao preparar consulta de referência: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmtRef, "s", $ordem);
        mysqli_stmt_execute($stmtRef);
        $resultRef = mysqli_stmt_get_result($stmtRef);
        $bombaRef = mysqli_fetch_assoc($resultRef);

        // Se não houver referência, usar valores padrão
        if (!$bombaRef) {
            $bombaRef = [
                'pressao_oleo_min' => 0.0,
                'pressao_oleo_max' => 0.0,
                'vazao_min' => 0.0,
                'vazao_max' => 0.0,
                'comb_pressao' => 0.0
            ];
            echo "<div class='warning-msg'>Nenhuma referência encontrada para ordem #" . htmlspecialchars($ordem) . ". Exibindo tabela vazia.</div>";
        }

        // Buscar medições existentes
        $queryMed = "SELECT medicoes FROM bomba WHERE is_reference = 0 AND ordem = ?";
        $stmtMed = mysqli_prepare($conn, $queryMed);
        mysqli_stmt_bind_param($stmtMed, "s", $ordem);
        mysqli_stmt_execute($stmtMed);
        $resultMed = mysqli_stmt_get_result($stmtMed);
        $bombaMed = mysqli_fetch_assoc($resultMed);
        
        $medicoes = $bombaMed && $bombaMed['medicoes'] ? json_decode($bombaMed['medicoes'], true) : [];

        // Exibir interface
        echo "<div class='card bomba-medicoes'>";
        echo "<h2 class='card-title'>MENU MEDIÇÕES BOMBA</h2>";
        echo "<div class='legenda'>Medição de pressão e vazão da bomba</div>";
        
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='bomba'>";
        echo "<input type='hidden' name='ordem' value='" . htmlspecialchars($ordem) . "'>";
        echo "<table>";
        
        // Cabeçalho da tabela
        echo "<thead><tr><th>PARÂMETRO</th><th>REFERÊNCIA</th><th>MEDIDA</th></tr></thead>";
        echo "<tbody>";

        // Pressão de óleo mínima
        echo "<tr class='pressao-oleo'>";
        echo "<td>Pressão de óleo mínima</td>";
        echo "<td>" . number_format($bombaRef['pressao_oleo_min'], 2, ',', '.') . "</td>";
        echo "<td>";
        $valorPressaoOleoMin = isset($medicoes['pressao_oleo_min']) && $medicoes['pressao_oleo_min'] !== null ? number_format($medicoes['pressao_oleo_min'], 2, ',', '.') : '';
        echo "<input type='text' name='medida[pressao_oleo_min]' class='meas-input' value='$valorPressaoOleoMin'>";
        echo "</td>";
        echo "</tr>";

        // Pressão de óleo máxima (Corrigido o name)
        echo "<tr class='pressao-oleo'>";
        echo "<td>Pressão de óleo máxima</td>";
        echo "<td>" . number_format($bombaRef['pressao_oleo_max'], 2, ',', '.') . "</td>";
        echo "<td>";
        $valorPressaoOleoMax = isset($medicoes['pressao_oleo_max']) && $medicoes['pressao_oleo_max'] !== null ? number_format($medicoes['pressao_oleo_max'], 2, ',', '.') : '';
        echo "<input type='text' name='medida[pressao_oleo_max]' class='meas-input' value='$valorPressaoOleoMax'>";
        echo "</td>";
        echo "</tr>";

        // Vazão mínima
        echo "<tr class='vazao'>";
        echo "<td>Vazão mínima</td>";
        echo "<td>" . number_format($bombaRef['vazao_min'], 2, ',', '.') . "</td>";
        echo "<td>";
        $valorVazaoMin = isset($medicoes['vazao_min']) && $medicoes['vazao_min'] !== null ? number_format($medicoes['vazao_min'], 2, ',', '.') : '';
        echo "<input type='text' name='medida[vazao_min]' class='meas-input' value='$valorVazaoMin'>";
        echo "</td>";
        echo "</tr>";

        // Vazão máxima
        echo "<tr class='vazao'>";
        echo "<td>Vazão máxima</td>";
        echo "<td>" . number_format($bombaRef['vazao_max'], 2, ',', '.') . "</td>";
        echo "<td>";
        $valorVazaoMax = isset($medicoes['vazao_max']) && $medicoes['vazao_max'] !== null ? number_format($medicoes['vazao_max'], 2, ',', '.') : '';
        echo "<input type='text' name='medida[vazao_max]' class='meas-input' value='$valorVazaoMax'>";
        echo "</td>";
        echo "</tr>";

        // Pressão de combustível
        echo "<tr class='combustivel'>";
        echo "<td>Pressão de combustível</td>";
        echo "<td>" . number_format($bombaRef['comb_pressao'], 2, ',', '.') . "</td>";
        echo "<td>";
        $valorCombPressao = isset($medicoes['comb_pressao']) && $medicoes['comb_pressao'] !== null ? number_format($medicoes['comb_pressao'], 2, ',', '.') : '';
        echo "<input type='text' name='medida[comb_pressao]' class='meas-input' value='$valorCombPressao'>";
        echo "</td>";
        echo "</tr>";

        echo "</tbody></table>";
        echo "<button type='submit' class='save-btn'>Salvar Medições</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro ao exibir medições da bomba: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

function displayMotorMedicoes($conn, $ordem) {
    try {
        // Processar salvamento se for POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === 'motor') {
            // Buscar medições existentes antes de processar o POST
            $queryMed = "SELECT medicoes FROM motor WHERE is_reference = 0 AND ordem = ?";
            $stmtMed = mysqli_prepare($conn, $queryMed);
            mysqli_stmt_bind_param($stmtMed, "s", $ordem);
            mysqli_stmt_execute($stmtMed);
            $resultMed = mysqli_stmt_get_result($stmtMed);
            $motorMed = mysqli_fetch_assoc($resultMed);
            
            $medicoesExistentes = $motorMed && $motorMed['medicoes'] ? json_decode($motorMed['medicoes'], true) : [];
            $medicoes = $medicoesExistentes;
            
            // Verificar se 'medida' existe e mesclar com valores existentes
            if (isset($_POST['medida'])) {
                foreach ($_POST['medida'] as $param => $cilindros) {
                    foreach ($cilindros as $cilindro => $valor) {
                        $medicoes[$param][$cilindro] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                    }
                }
            }
            
            // Verificar se já existe registro de medições
            $checkQuery = "SELECT id FROM motor WHERE is_reference = 0 AND ordem = ?";
            $stmtCheck = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($stmtCheck, "s", $ordem);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            
            if (mysqli_num_rows($resultCheck) > 0) {
                // Atualizar registro existente
                $updateQuery = "UPDATE motor SET medicoes = ? WHERE is_reference = 0 AND ordem = ?";
                $stmtUpdate = mysqli_prepare($conn, $updateQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtUpdate, "ss", $jsonMedicoes, $ordem);
                if (!mysqli_stmt_execute($stmtUpdate)) {
                    throw new Exception("Erro ao atualizar medições: " . mysqli_stmt_error($stmtUpdate));
                }
            } else {
                // Inserir novo registro
                $insertQuery = "INSERT INTO motor (ordem, is_reference, medicoes) VALUES (?, 0, ?)";
                $stmtInsert = mysqli_prepare($conn, $insertQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtInsert, "ss", $ordem, $jsonMedicoes);
                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Erro ao inserir medições: " . mysqli_stmt_error($stmtInsert));
                }
            }
            echo "<div class='success-msg'>Medições salvas com sucesso!</div>";
        }

        // Buscar dados de referência
        $queryRef = "SELECT * FROM motor WHERE is_reference = 1 AND ordem = ?";
        $stmtRef = mysqli_prepare($conn, $queryRef);
        if (!$stmtRef) {
            throw new Exception("Erro ao preparar consulta de referência: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmtRef, "s", $ordem);
        mysqli_stmt_execute($stmtRef);
        $resultRef = mysqli_stmt_get_result($stmtRef);
        $motorRef = mysqli_fetch_assoc($resultRef);

        // Se não houver referência, usar valores padrão
        if (!$motorRef) {
            $motorRef = [
                'nr_cilindros' => 0,
                'curso_pistao' => 0.0,
                'diametro_cilindro_max' => 0.0,
                'conicidade_max' => 0.0,
                'ovalizacao_max' => 0.0,
                'diametro_pistao_min' => 0.0,
                'folga_cil_pis_max' => 0.0,
                'aber_anel_1_max' => 0.0,
                'aber_anel_2_max' => 0.0,
                'aber_anel_1_pres_min' => 0.0,
                'aber_anel_2_pres_min' => 0.0,
                'larg_anel_1_min' => 0.0,
                'larg_anel_2_min' => 0.0,
                'dia_furo_pis_min' => 0.0,
                'dia_pino_pis_min' => 0.0,
                'folga_pino_pis_max' => 0.0
            ];
            echo "<div class='warning-msg'>Nenhuma referência encontrada para ordem #" . htmlspecialchars($ordem) . ". Exibindo tabela vazia.</div>";
        }

        // Buscar medições existentes
        $queryMed = "SELECT medicoes FROM motor WHERE is_reference = 0 AND ordem = ?";
        $stmtMed = mysqli_prepare($conn, $queryMed);
        mysqli_stmt_bind_param($stmtMed, "s", $ordem);
        mysqli_stmt_execute($stmtMed);
        $resultMed = mysqli_stmt_get_result($stmtMed);
        $motorMed = mysqli_fetch_assoc($resultMed);
        
        $medicoes = $motorMed && $motorMed['medicoes'] ? json_decode($motorMed['medicoes'], true) : [];

        // Exibir interface
        echo "<div class='card motor-medicoes'>";
        echo "<h2 class='card-title'>MENU MEDIÇÕES MOTOR</h2>";
        echo "<div class='legenda'>Medição de parâmetros do motor para cada cilindro</div>";
        echo "<div> Número de cilindros: <div class='subtitulo'> " . htmlspecialchars($motorRef['nr_cilindros']) . "</div></div>";
        
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='motor'>";
        echo "<input type='hidden' name='ordem' value='" . htmlspecialchars($ordem) . "'>";
        echo "<table>";
        
        // Cabeçalho da tabela
        echo "<thead><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            echo "<th>CILINDRO " . $i . "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";

        // Medição de curso do pistão
        echo "<tr class='curso-pistao'>";
        echo "<td>Curso do pistão</td>";
        echo "<td>" . number_format($motorRef['curso_pistao'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['curso_pistao'][$i]) && $medicoes['curso_pistao'][$i] !== null ? number_format($medicoes['curso_pistao'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[curso_pistao][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de diâmetro do cilindro
        echo "<tr class='diametro-cilindro'>";
        echo "<td>Diâmetro do cilindro máximo</td>";
        echo "<td>" . number_format($motorRef['diametro_cilindro_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['diametro_cilindro_max'][$i]) && $medicoes['diametro_cilindro_max'][$i] !== null ? number_format($medicoes['diametro_cilindro_max'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[diametro_cilindro_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de conicidade
        echo "<tr class='conicidade'>";
        echo "<td>Conicidade máxima</td>";
        echo "<td>" . number_format($motorRef['conicidade_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['conicidade_max'][$i]) && $medicoes['conicidade_max'][$i] !== null ? number_format($medicoes['conicidade_max'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[conicidade_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de ovalização
        echo "<tr class='ovalizacao'>";
        echo "<td>Ovalização máxima</td>";
        echo "<td>" . number_format($motorRef['ovalizacao_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['ovalizacao_max'][$i]) && $medicoes['ovalizacao_max'][$i] !== null ? number_format($medicoes['ovalizacao_max'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[ovalizacao_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de diâmetro do pistão
        echo "<tr class='diametro-pistao'>";
        echo "<td>Diâmetro do pistão mínimo</td>";
        echo "<td>" . number_format($motorRef['diametro_pistao_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['diametro_pistao_min'][$i]) && $medicoes['diametro_pistao_min'][$i] !== null ? number_format($medicoes['diametro_pistao_min'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[diametro_pistao_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de folga cilindro/pistão
        echo "<tr class='folga-cil-pis'>";
        echo "<td>Folga cilindro/pistão máxima</td>";
        echo "<td>" . number_format($motorRef['folga_cil_pis_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['folga_cil_pis_max'][$i]) && $medicoes['folga_cil_pis_max'][$i] !== null ? number_format($medicoes['folga_cil_pis_max'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[folga_cil_pis_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Separador para medições dos anéis
        echo "<tr class='separador'>";
        echo "<td colspan='" . ($motorRef['nr_cilindros'] + 2) . "'>MEDIÇÕES DOS ANÉIS</td>";
        echo "</tr>";

        // Medição de abertura do anel 1
        echo "<tr class='anel-1'>";
        echo "<td>Abertura do anel 1 máxima</td>";
        echo "<td>" . number_format($motorRef['aber_anel_1_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['aber_anel_1_max'][$i]) && $medicoes['aber_anel_1_max'][$i] !== null ? number_format($medicoes['aber_anel_1_max'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[aber_anel_1_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de abertura do anel 2
        echo "<tr class='anel-2'>";
        echo "<td>Abertura do anel 2 máxima</td>";
        echo "<td>" . number_format($motorRef['aber_anel_2_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['aber_anel_2_max'][$i]) && $medicoes['aber_anel_2_max'][$i] !== null ? number_format($medicoes['aber_anel_2_max'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[aber_anel_2_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de pressão do anel 1
        echo "<tr class='anel-1'>";
        echo "<td>Pressão do anel 1 mínima</td>";
        echo "<td>" . number_format($motorRef['aber_anel_1_pres_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['aber_anel_1_pres_min'][$i]) && $medicoes['aber_anel_1_pres_min'][$i] !== null ? number_format($medicoes['aber_anel_1_pres_min'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[aber_anel_1_pres_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de pressão do anel 2
        echo "<tr class='anel-2'>";
        echo "<td>Pressão do anel 2 mínima</td>";
        echo "<td>" . number_format($motorRef['aber_anel_2_pres_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['aber_anel_2_pres_min'][$i]) && $medicoes['aber_anel_2_pres_min'][$i] !== null ? number_format($medicoes['aber_anel_2_pres_min'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[aber_anel_2_pres_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de largura do anel 1
        echo "<tr class='anel-1'>";
        echo "<td>Largura do anel 1 mínima</td>";
        echo "<td>" . number_format($motorRef['larg_anel_1_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['larg_anel_1_min'][$i]) && $medicoes['larg_anel_1_min'][$i] !== null ? number_format($medicoes['larg_anel_1_min'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[larg_anel_1_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de largura do anel 2
        echo "<tr class='anel-2'>";
        echo "<td>Largura do anel 2 mínima</td>";
        echo "<td>" . number_format($motorRef['larg_anel_2_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['larg_anel_2_min'][$i]) && $medicoes['larg_anel_2_min'][$i] !== null ? number_format($medicoes['larg_anel_2_min'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[larg_anel_2_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Separador para medições do pino
        echo "<tr class='separador'>";
        echo "<td colspan='" . ($motorRef['nr_cilindros'] + 2) . "'>MEDIÇÕES DO PINO</td>";
        echo "</tr>";

        // Medição de diâmetro do furo do pistão
        echo "<tr class='pino'>";
        echo "<td>Diâmetro do furo do pistão mínimo</td>";
        echo "<td>" . number_format($motorRef['dia_furo_pis_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['dia_furo_pis_min'][$i]) && $medicoes['dia_furo_pis_min'][$i] !== null ? number_format($medicoes['dia_furo_pis_min'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[dia_furo_pis_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de diâmetro do pino do pistão
        echo "<tr class='pino'>";
        echo "<td>Diâmetro do pino do pistão mínimo</td>";
        echo "<td>" . number_format($motorRef['dia_pino_pis_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['dia_pino_pis_min'][$i]) && $medicoes['dia_pino_pis_min'][$i] !== null ? number_format($medicoes['dia_pino_pis_min'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[dia_pino_pis_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de folga do pino do pistão
        echo "<tr class='pino'>";
        echo "<td>Folga do pino do pistão máxima</td>";
        echo "<td>" . number_format($motorRef['folga_pino_pis_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $valor = isset($medicoes['folga_pino_pis_max'][$i]) && $medicoes['folga_pino_pis_max'][$i] !== null ? number_format($medicoes['folga_pino_pis_max'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[folga_pino_pis_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        echo "</tbody></table>";
        echo "<button type='submit' class='save-btn'>Salvar Medições</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro ao exibir medições do motor: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Processamento do formulário do motor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === 'motor') {
    try {
        // Verificar se já existe medição
        $checkQuery = "SELECT COUNT(*) as count FROM motor WHERE is_reference = 0 AND ordem = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $ordem);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        $checkRow = mysqli_fetch_assoc($checkResult);

        if ($checkRow['count'] == 0) {
            // Inserir nova medição
            $insertQuery = "INSERT INTO motor (ordem, is_reference) VALUES (?, 0)";
            $insertStmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, "s", $ordem);
            mysqli_stmt_execute($insertStmt);
            $medicao_id = mysqli_insert_id($conn);
        }

        // Atualizar medições
        if (isset($_POST['medida'])) {
            foreach ($_POST['medida'] as $tipo => $cilindros) {
                foreach ($cilindros as $cilindro => $valor) {
                    // Converter vírgula para ponto e garantir formato decimal
                    $valor = str_replace(',', '.', $valor);
                    $valor = floatval($valor);
                    
                    $updateQuery = "UPDATE motor SET $tipo = ? WHERE ordem = ? AND is_reference = 0";
                    $updateStmt = mysqli_prepare($conn, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "ds", $valor, $ordem);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);
                }
            }
            echo "<div class='success-msg'>Medições salvas com sucesso!</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro ao salvar medições: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

function displayVirabrequimMedicoes($conn, $ordem) {
    try {
        // Processar salvamento se for POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === 'virabrequim') {
            // Buscar medições existentes antes de processar o POST
            $queryMed = "SELECT medicoes FROM virabrequim WHERE is_reference = 0 AND ordem = ?";
            $stmtMed = mysqli_prepare($conn, $queryMed);
            mysqli_stmt_bind_param($stmtMed, "s", $ordem);
            mysqli_stmt_execute($stmtMed);
            $resultMed = mysqli_stmt_get_result($stmtMed);
            $virabrequimMed = mysqli_fetch_assoc($resultMed);
            
            $medicoesExistentes = $virabrequimMed && $virabrequimMed['medicoes'] ? json_decode($virabrequimMed['medicoes'], true) : [];
            $medicoes = $medicoesExistentes;
            
            // Verificar se 'medida' existe e mesclar com valores existentes
            if (isset($_POST['medida'])) {
                foreach ($_POST['medida'] as $param => $cilindros) {
                    foreach ($cilindros as $cilindro => $valor) {
                        $medicoes[$param][$cilindro] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                    }
                }
            }
            
            // Verificar se já existe registro de medições
            $checkQuery = "SELECT id FROM virabrequim WHERE is_reference = 0 AND ordem = ?";
            $stmtCheck = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($stmtCheck, "s", $ordem);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            
            if (mysqli_num_rows($resultCheck) > 0) {
                // Atualizar registro existente
                $updateQuery = "UPDATE virabrequim SET medicoes = ? WHERE is_reference = 0 AND ordem = ?";
                $stmtUpdate = mysqli_prepare($conn, $updateQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtUpdate, "ss", $jsonMedicoes, $ordem);
                if (!mysqli_stmt_execute($stmtUpdate)) {
                    throw new Exception("Erro ao atualizar medições: " . mysqli_stmt_error($stmtUpdate));
                }
            } else {
                // Inserir novo registro
                $insertQuery = "INSERT INTO virabrequim (ordem, is_reference, medicoes) VALUES (?, 0, ?)";
                $stmtInsert = mysqli_prepare($conn, $insertQuery);
                $jsonMedicoes = json_encode($medicoes);
                mysqli_stmt_bind_param($stmtInsert, "ss", $ordem, $jsonMedicoes);
                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Erro ao inserir medições: " . mysqli_stmt_error($stmtInsert));
                }
            }
            echo "<div class='success-msg'>Medições salvas com sucesso!</div>";
        }

        // Buscar dados de referência do virabrequim
        $queryRef = "SELECT * FROM virabrequim WHERE is_reference = 1 AND ordem = ?";
        $stmtRef = mysqli_prepare($conn, $queryRef);
        if (!$stmtRef) {
            throw new Exception("Erro ao preparar consulta de referência: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmtRef, "s", $ordem);
        mysqli_stmt_execute($stmtRef);
        $resultRef = mysqli_stmt_get_result($stmtRef);
        $virabrequimRef = mysqli_fetch_assoc($resultRef);

        // Se não houver referência, usar valores padrão
        if (!$virabrequimRef) {
            $virabrequimRef = [
                'tipo' => '',
                'folga_mancal' => 0.0,
                'folga_bronzina' => 0.0,
                'folga_lateral_biela' => 0.0,
                'folga_lateral_eixo_min' => 0.0,
                'folga_lateral_eixo_max' => 0.0,
                'empenamento' => 0.0
            ];
            echo "<div class='warning-msg'>Nenhuma referência encontrada para ordem #" . htmlspecialchars($ordem) . ". Exibindo tabela vazia.</div>";
        }

        // Buscar número de cilindros do cabeçote
        $queryCilindros = "SELECT cilindros FROM cabecote WHERE is_reference = 1 AND ordem = ?";
        $stmtCilindros = mysqli_prepare($conn, $queryCilindros);
        mysqli_stmt_bind_param($stmtCilindros, "s", $ordem);
        mysqli_stmt_execute($stmtCilindros);
        $resultCilindros = mysqli_stmt_get_result($stmtCilindros);
        $cabecote = mysqli_fetch_assoc($resultCilindros);
        $nr_cilindros = $cabecote ? $cabecote['cilindros'] : 0;

        // Buscar medições existentes
        $queryMed = "SELECT medicoes FROM virabrequim WHERE is_reference = 0 AND ordem = ?";
        $stmtMed = mysqli_prepare($conn, $queryMed);
        mysqli_stmt_bind_param($stmtMed, "s", $ordem);
        mysqli_stmt_execute($stmtMed);
        $resultMed = mysqli_stmt_get_result($stmtMed);
        $virabrequimMed = mysqli_fetch_assoc($resultMed);
        
        $medicoes = $virabrequimMed && $virabrequimMed['medicoes'] ? json_decode($virabrequimMed['medicoes'], true) : [];

        // Exibir interface
        echo "<div class='card virabrequim-medicoes'>";
        echo "<h2 class='card-title'>MENU MEDIÇÕES VIRABREQUIM</h2>";
        echo "<div class='legenda'>Medição de parâmetros do virabrequim para cada cilindro</div>";
        echo "<div> Tipo: <div class='subtitulo'> " . htmlspecialchars($virabrequimRef['tipo']) . "</div></div>";
        
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='virabrequim'>";
        echo "<input type='hidden' name='ordem' value='" . htmlspecialchars($ordem) . "'>";
        echo "<table>";
        
        // Cabeçalho da tabela
        echo "<thead><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        for ($i = 1; $i <= $nr_cilindros; $i++) {
            echo "<th>CILINDRO " . $i . "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";

        // Campos comuns para ambos os tipos
        echo "<tr class='folga-mancal'>";
        echo "<td>Folga mancal</td>";
        echo "<td>" . number_format($virabrequimRef['folga_mancal'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $nr_cilindros; $i++) {
            $valor = isset($medicoes['folga_mancal'][$i]) && $medicoes['folga_mancal'][$i] !== null ? number_format($medicoes['folga_mancal'][$i], 2, ',', '.') : '';
            echo "<td><input type='text' name='medida[folga_mancal][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Campos específicos para Bronzina
        if ($virabrequimRef['tipo'] === 'Bronzina') {
            echo "<tr class='folga-bronzina'>";
            echo "<td>Folga bronzina</td>";
            echo "<td>" . number_format($virabrequimRef['folga_bronzina'], 2, ',', '.') . "</td>";
            for ($i = 1; $i <= $nr_cilindros; $i++) {
                $valor = isset($medicoes['folga_bronzina'][$i]) && $medicoes['folga_bronzina'][$i] !== null ? number_format($medicoes['folga_bronzina'][$i], 2, ',', '.') : '';
                echo "<td><input type='text' name='medida[folga_bronzina][" . $i . "]' class='meas-input' value='$valor'></td>";
            }
            echo "</tr>";
        }

        // Campos específicos para Rolamento
        if ($virabrequimRef['tipo'] === 'Rolamento') {
            echo "<tr class='folga-lateral-biela'>";
            echo "<td>Folga lateral biela</td>";
            echo "<td>" . number_format($virabrequimRef['folga_lateral_biela'], 2, ',', '.') . "</td>";
            for ($i = 1; $i <= $nr_cilindros; $i++) {
                $valor = isset($medicoes['folga_lateral_biela'][$i]) && $medicoes['folga_lateral_biela'][$i] !== null ? number_format($medicoes['folga_lateral_biela'][$i], 2, ',', '.') : '';
                echo "<td><input type='text' name='medida[folga_lateral_biela][" . $i . "]' class='meas-input' value='$valor'></td>";
            }
            echo "</tr>";

            echo "<tr class='folga-lateral-eixo-min'>";
            echo "<td>Folga lateral eixo mínima</td>";
            echo "<td>" . number_format($virabrequimRef['folga_lateral_eixo_min'], 2, ',', '.') . "</td>";
            for ($i = 1; $i <= $nr_cilindros; $i++) {
                $valor = isset($medicoes['folga_lateral_eixo_min'][$i]) && $medicoes['folga_lateral_eixo_min'][$i] !== null ? number_format($medicoes['folga_lateral_eixo_min'][$i], 2, ',', '.') : '';
                echo "<td><input type='text' name='medida[folga_lateral_eixo_min][" . $i . "]' class='meas-input' value='$valor'></td>";
            }
            echo "</tr>";

            echo "<tr class='folga-lateral-eixo-max'>";
            echo "<td>Folga lateral eixo máxima</td>";
            echo "<td>" . number_format($virabrequimRef['folga_lateral_eixo_max'], 2, ',', '.') . "</td>";
            for ($i = 1; $i <= $nr_cilindros; $i++) {
                $valor = isset($medicoes['folga_lateral_eixo_max'][$i]) && $medicoes['folga_lateral_eixo_max'][$i] !== null ? number_format($medicoes['folga_lateral_eixo_max'][$i], 2, ',', '.') : '';
                echo "<td><input type='text' name='medida[folga_lateral_eixo_max][" . $i . "]' class='meas-input' value='$valor'></td>";
            }
            echo "</tr>";

            echo "<tr class='empenamento'>";
            echo "<td>Empenamento</td>";
            echo "<td>" . number_format($virabrequimRef['empenamento'], 2, ',', '.') . "</td>";
            for ($i = 1; $i <= $nr_cilindros; $i++) {
                $valor = isset($medicoes['empenamento'][$i]) && $medicoes['empenamento'][$i] !== null ? number_format($medicoes['empenamento'][$i], 2, ',', '.') : '';
                echo "<td><input type='text' name='medida[empenamento][" . $i . "]' class='meas-input' value='$valor'></td>";
            }
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "<button type='submit' class='save-btn'>Salvar Medições</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro ao exibir medições do virabrequim: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}


// Chamar a função de medições do cabeçote
if (isset($_GET['ordem'])) {
    displayTableData($conn, "embreagem", "Embreagem");
    displayEmbreagemMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "bomba", "Bomba");
    displayBombaMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "motor", "Motor");
    displayMotorMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "virabrequim", "Virabrequim");
    displayVirabrequimMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "cabecote", "Cabeçote");
    displayCabecoteMedicoes($conn, $_GET['ordem']);
} else {
    echo "<div class='error-msg'>Erro: Parâmetro 'ordem' não foi especificado.</div>";
}

echo '<a class="button primary" id="closeModal3">Sair</a>';
?>

<link rel="stylesheet" href="assets/css/ordemservico/menus/dados.css">

<?php
// Buscar dados de referência do cabeçote para os valores de limite
$query = "SELECT val_adm_limite_min, val_adm_limite_max, val_esc_limite_min, val_esc_limite_max 
          FROM cabecote 
          WHERE is_reference = 1 AND ordem = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $ordem);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cabecote_ref = mysqli_fetch_assoc($result);
?>

<input type="hidden" id="val_adm_limite_min" value="<?php echo $cabecote_ref['val_adm_limite_min']; ?>">
<input type="hidden" id="val_adm_limite_max" value="<?php echo $cabecote_ref['val_adm_limite_max']; ?>">
<input type="hidden" id="val_esc_limite_min" value="<?php echo $cabecote_ref['val_esc_limite_min']; ?>">
<input type="hidden" id="val_esc_limite_max" value="<?php echo $cabecote_ref['val_esc_limite_max']; ?>">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="pages\ordemservico\modal\calcularPastilha.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se os valores de referência estão presentes
    const valAdmMin = document.getElementById('val_adm_limite_min').value;
    const valAdmMax = document.getElementById('val_adm_limite_max').value;
    const valEscMin = document.getElementById('val_esc_limite_min').value;
    const valEscMax = document.getElementById('val_esc_limite_max').value;

    console.log('Valores de referência carregados:', {
        valAdmMin,
        valAdmMax,
        valEscMin,
        valEscMax
    });

    // Vincular o evento de cálculo a todos os inputs de folga
    const folgaInputs = document.querySelectorAll('.folga-input');
    folgaInputs.forEach(input => {
        input.addEventListener('change', function() {
            calcularPastilha(this);
        });
        input.addEventListener('input', function() {
            calcularPastilha(this);
        });
    });

    // Inicializar cálculos para inputs que já têm valores
    folgaInputs.forEach(input => {
        if (input.value) {
            calcularPastilha(input);
        }
    });
});
</script>