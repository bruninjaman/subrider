<?php

// Funções auxiliares
function formatarIntervalo($min, $max)
{
    return number_format(floatval($min), 3, ',', '.') . " a " . number_format(floatval($max), 3, ',', '.');
}

// Função para detectar se um valor precisa de correção centesimal
function needsCentesimalCorrection($inputValue, $referenceValue)
{
    // Converter vírgula para ponto
    $input = floatval(str_replace(',', '.', $inputValue));

    // Verificar se a referência é um range (formato "min a max")
    if (strpos($referenceValue, ' a ') !== false) {
        $parts = explode(' a ', $referenceValue);
        if (count($parts) == 2) {
            $min = floatval(str_replace(',', '.', trim($parts[0])));
            $max = floatval(str_replace(',', '.', trim($parts[1])));

            // Usar o valor mínimo como referência para detecção
            $reference = $min;

            // Se o valor de entrada é >= 10 e a referência é < 1
            if ($input >= 10 && $reference < 1) {
                return true;
            }

            // Se o valor de entrada é >= 100 vezes maior que a referência
            if ($reference > 0 && ($input / $reference) >= 100) {
                return true;
            }

            return false;
        }
    }

    // Para valores únicos
    $reference = floatval(str_replace(',', '.', $referenceValue));

    // Se o valor de entrada é >= 10 e a referência é < 1
    if ($input >= 10 && $reference < 1) {
        return true;
    }

    // Se o valor de entrada é >= 100 vezes maior que a referência
    if ($reference > 0 && ($input / $reference) >= 100) {
        return true;
    }

    return false;
}

// Função para aplicar correção centesimal baseada na referência
function applyCentesimalCorrection($inputValue, $referenceValue)
{
    $input = floatval(str_replace(',', '.', $inputValue));

    // Extrair valor de referência (usar mínimo se for range)
    $reference = $referenceValue;
    if (strpos($referenceValue, ' a ') !== false) {
        $parts = explode(' a ', $referenceValue);
        if (count($parts) == 2) {
            $reference = floatval(str_replace(',', '.', trim($parts[0])));
        }
    } else {
        $reference = floatval(str_replace(',', '.', $referenceValue));
    }

    // Calcular o fator de correção baseado na referência
    if ($reference > 0) {
        // Encontrar a potência de 10 mais próxima da referência
        $factor = 1;
        $tempRef = $reference;

        // Se referência < 1, encontrar quantas casas decimais precisamos
        while ($tempRef < 1 && $factor < 10000) {
            $tempRef *= 10;
            $factor *= 10;
        }

        // Aplicar correção se o valor de entrada for muito maior
        if ($input >= ($reference * $factor)) {
            return $input / $factor;
        }
    }

    return $input;
}

// Função para detectar se um valor está fora do range baseado na referência
function isValueOutOfRange($inputValue, $referenceValue)
{
    // Converter vírgula para ponto
    $input = floatval(str_replace(',', '.', $inputValue));

    // Verificar se a referência contém um range (formato "min a max")
    if (strpos($referenceValue, ' a ') !== false) {
        $parts = explode(' a ', $referenceValue);
        if (count($parts) == 2) {
            $min = floatval(str_replace(',', '.', trim($parts[0])));
            $max = floatval(str_replace(',', '.', trim($parts[1])));

            // Verificar se precisa de correção centesimal
            if (needsCentesimalCorrection($inputValue, $referenceValue)) {
                $input = applyCentesimalCorrection($inputValue, $referenceValue);
            }

            return $input < $min || $input > $max;
        }
    }

    // Para valores únicos, verificar se está muito distante
    $reference = floatval(str_replace(',', '.', $referenceValue));
    if (needsCentesimalCorrection($inputValue, $referenceValue)) {
        $input = applyCentesimalCorrection($inputValue, $referenceValue);
    }

    // Considerar fora do range se a diferença for muito grande (mais de 50% da referência)
    if ($reference > 0) {
        $tolerance = $reference * 0.5;
        return abs($input - $reference) > $tolerance;
    }

    return false;
}

function displayTableData($conn, $tableName, $tableTitle)
{
    if (!isset($_GET['ordem'])) {
        echo "<p>Parâmetro 'ordem' inválido ou não fornecido.</p>";
        return;
    }

    $ordem = $_GET['ordem'];

    // Handle form submission for updating values
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === $tableName) {
        if (isset($_POST['update'])) {
            // Buscar o ID do registro de referência
            $refQuery = "SELECT * FROM $tableName WHERE is_reference = 1 AND ordem = ?";
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

                    // Atualizar a parte do salvamento para usar a referência correta
                    foreach ($fields as $field => $value) {
                        // Converter vírgula para ponto se for um número
                        if (is_numeric(str_replace(',', '.', $value))) {
                            $value = str_replace(',', '.', $value);

                            // Aplicar correção centesimal se necessário
                            if (isset($refRow[$field])) {
                                $referenceValue = $refRow[$field];

                                // Debug: verificar se a detecção está funcionando
                                if (needsCentesimalCorrection($value, $referenceValue)) {
                                    $originalValue = $value;
                                    $value = applyCentesimalCorrection($value, $referenceValue); // Passar referência também
                                    error_log("Correção centesimal aplicada: $originalValue -> $value (ref: $referenceValue)");
                                }
                            }
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
    echo "<h2 class='card-title'>Referências do " . htmlspecialchars($tableTitle) . "</h2>";
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
                // Adicionar classes CSS para controle dinâmico
                $virabrequimClass = '';
                if (
                    in_array($keyLower, [
                        'folga_bronzina',
                        'folga_bronzinha',
                        'folga_eixo_biela',
                        'folga_eixo_mancal',
                        'folga_eixo_bronzina',
                        'folga_mancal',
                        'diametro_moente',
                        'diametro_munhao',
                        'diametro_munhoes',
                        'qtd_cilindros',
                        'qtd_munhoes'
                    ])
                ) {
                    $virabrequimClass = 'virabrequim-bronzina-field';
                } else if (
                    in_array($keyLower, [
                        'folga_lateral_biela',
                        'folga_lateral_eixo_min',
                        'folga_lateral_eixo_max',
                        'empenamento'
                    ])
                ) {
                    $virabrequimClass = 'virabrequim-rolamento-field';
                }
            }

            if (
                !in_array($keyLower, ['id', 'ordem', 'is_reference', 'medicoes', 'created_at', 'updated_at']) &&
                ($value !== null || $tableName === 'virabrequim') &&
                !($tableName === 'cabecote' && ($keyLower === 'motor_tipo' || $keyLower === 'tipo_val'))
            ) {

                echo "<tr" . (isset($virabrequimClass) && $virabrequimClass ? " class='$virabrequimClass'" : "") . ">";
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
                    $tipoBanco = trim(strtolower($value));
                    echo "<td class='ref-value'>" .
                        htmlspecialchars($value) .
                        "</td>";

                    echo "<td class='meas-values'>";
                    echo "<select name='measured[" . $refRow['id'] . "][" . htmlspecialchars($key) . "]' " .
                        "class='meas-input first' " .
                        "onchange='toggleVirabrequimFields(this.value)'>";
                    echo "<option value='Rolamento' " . ($tipoBanco == 'rolamento' ? 'selected' : '') . ">Rolamento</option>";
                    echo "<option value='Bronzina' " . ($tipoBanco == 'bronzina' ? 'selected' : '') . ">Bronzina</option>";
                    echo "</select>";
                    echo "</td>";
                } else {
                    echo "<td class='ref-value'>" .
                        htmlspecialchars($value) .
                        "</td>";

                    echo "<td class='meas-values'>";

                    // Adicionar classe para validação em tempo real
                    $inputClass = 'meas-input first';
                    if (isValueOutOfRange($value, $value)) {
                        $inputClass .= ' out-of-range-input';
                    }

                    echo "<input type='text' " .
                        "name='measured[" . $refRow['id'] . "][" . htmlspecialchars($key) . "]' " .
                        "value='" . htmlspecialchars($value) . "' " .
                        "class='$inputClass' " .
                        "data-reference='" . htmlspecialchars($value) . "' " .
                        "oninput='validateInput(this)'>";
                    echo "</td>";
                }

                echo "</tr>";
            }
        }

        echo "</tbody></table>";
        echo "</div>";

        echo "<button type='submit' class='save-btn'>Salvar Alterações</button>";
        echo "</form>";

        // Adicionar script para validação em tempo real
        echo "<script>
        function validateInput(input) {
            const reference = input.getAttribute('data-reference');
            const value = input.value;
            
            if (value && reference) {
                // Verificar se está fora do range
                if (isValueOutOfRangeJS(value, reference)) {
                    input.classList.add('out-of-range-input');
                } else {
                    input.classList.remove('out-of-range-input');
                }
            }
        }
        
        function needsCentesimalCorrectionJS(inputValue, referenceValue) {
            const input = parseFloat(inputValue.replace(',', '.'));
            const reference = parseFloat(referenceValue.replace(',', '.'));
            
            if (input >= 10 && reference < 1) {
                return true;
            }
            
            if (reference > 0 && (input / reference) >= 100) {
                return true;
            }
            
            return false;
        }
        
        function isValueOutOfRangeJS(inputValue, referenceValue) {
            let input = parseFloat(inputValue.replace(',', '.'));
            
            if (referenceValue.includes(' a ')) {
                const parts = referenceValue.split(' a ');
                if (parts.length === 2) {
                    const min = parseFloat(parts[0].replace(',', '.'));
                    const max = parseFloat(parts[1].replace(',', '.'));
                    
                    if (needsCentesimalCorrectionJS(inputValue, min.toString())) {
                        input = input / 100;
                    }
                    
                    return input < min || input > max;
                }
            }
            
            const reference = parseFloat(referenceValue.replace(',', '.'));
            if (needsCentesimalCorrectionJS(inputValue, reference.toString())) {
                input = input / 100;
            }
            
            if (reference > 0) {
                const tolerance = reference * 0.5;
                return Math.abs(input - reference) > tolerance;
            }
            
            return false;
        }
        </script>";

        // Adicionar script para alternar campos dinamicamente
        if ($tableName === 'virabrequim') {
            echo "<script>
            function toggleVirabrequimFields(tipo) {
                if (!tipo) return;
                var t = tipo.toLowerCase();
                var bronzinaFields = document.querySelectorAll('.virabrequim-bronzina-field');
                var rolamentoFields = document.querySelectorAll('.virabrequim-rolamento-field');
                if (t === 'bronzina') {
                    bronzinaFields.forEach(function(el) { el.style.display = ''; });
                    rolamentoFields.forEach(function(el) { el.style.display = 'none'; });
                } else if (t === 'rolamento') {
                    bronzinaFields.forEach(function(el) { el.style.display = 'none'; });
                    rolamentoFields.forEach(function(el) { el.style.display = ''; });
                } else {
                    bronzinaFields.forEach(function(el) { el.style.display = 'none'; });
                    rolamentoFields.forEach(function(el) { el.style.display = 'none'; });
                }
            }
            // Inicializar ao carregar
            document.addEventListener('DOMContentLoaded', function() {
                var select = document.querySelector('select[name^=\'measured\'][name$=\"[tipo]\"]');
                if (select) toggleVirabrequimFields(select.value);
                if (select) select.addEventListener('change', function() {
                    toggleVirabrequimFields(this.value);
                });
            });
            </script>";
        }
    } else {
        echo "<div class='info-msg'>Nenhum dado de referência encontrado para esta ordem de serviço.</div>";
        echo "<div class='info-msg'>Por favor, adicione os dados de referência primeiro.</div>";
    }

    echo "</div>";
    echo "</div>";

    mysqli_stmt_close($refStmt);
    mysqli_stmt_close($measStmt);
}