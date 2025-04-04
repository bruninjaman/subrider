<?php

// Funções auxiliares
function formatarIntervalo($min, $max) {
    return number_format(floatval($min), 2, ',', '.') . " a " . number_format(floatval($max), 2, ',', '.');
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