<?php

// Verificação inicial do parâmetro ordem
if (!isset($_GET['ordem'])) {
    die("<div class='error-msg'>Erro: Parâmetro 'ordem' não foi especificado na URL.</div>");
}

// Usar a ordem como string
$ordem = $_GET['ordem'];

// Verificação da conexão cos com o banco de dados
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

if ($checkRow['count'] == 0) {
    die("<div class='error-msg'>Erro: Não foram encontrados dados de referência para a ordem de serviço #" . $ordem . ".</div>");
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
                    
                    // Verificar se é a tabela virabrequim e se tem o campo tipo
                    $isVirabrequim = ($tableName === 'virabrequim');
                    $tipo = null;
                    
                    if ($isVirabrequim) {
                        foreach ($fields as $field => $value) {
                            if (strtolower($field) === 'tipo') {
                                $tipo = $value;
                                break;
                            }
                        }
                    }
                    
                    foreach ($fields as $field => $value) {
                        // Se for virabrequim e tipo for Rolamento, alguns campos devem ser NULL
                        if ($isVirabrequim && $tipo === 'Rolamento' && 
                            in_array(strtolower($field), [
                                'folga_lateral_biela',
                                'folga_lateral_eixo_min',
                                'folga_lateral_eixo_max',
                                'empenamento'
                            ])) {
                            $updates[] = "`$field` = NULL";
                        } else {
                            $updates[] = "`$field` = ?";
                            $params[] = $value;
                            $types .= 's';
                        }
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

    if (mysqli_num_rows($refResult) > 0) {
        $refRow = mysqli_fetch_assoc($refResult);

        echo "<div class='card'>";
        echo "<h2 class='card-title'>" . htmlspecialchars($tableTitle) . "</h2>";
        echo "<div class='card-content'>";

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

        echo "</div>";
        echo "</div>";
    }

    mysqli_stmt_close($refStmt);
    mysqli_stmt_close($measStmt);
}

function displayCabecoteMedicoes($conn, $ordem) {
    try {
        // Função auxiliar para calcular o valor médio do intervalo
        function calcularValorMedio($min, $max) {
            return (floatval($min) + floatval($max)) / 2;
        }

        // Função auxiliar para formatar intervalo
        function formatarIntervalo($min, $max) {
            return number_format(floatval($min), 2, ',', '.') . " a " . number_format(floatval($max), 2, ',', '.');
        }

        // Função para calcular Pastilha Corrigida (PC)
        function calcularPC($folga, $referencia, $pastilha_antiga) {
            return (floatval($folga) - floatval($referencia)) + floatval($pastilha_antiga);
        }

        // Buscar dados de referência do cabeçote
        $query = "SELECT * FROM cabecote WHERE is_reference = 1 AND ordem = ?";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar consulta: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "s", $ordem);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Erro ao executar consulta: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $cabecote = mysqli_fetch_assoc($result);
        
        if (!$cabecote) {
            echo "<div class='error-msg'>Dados de referência do cabeçote não encontrados para a ordem #" . htmlspecialchars($ordem) . "</div>";
            return;
        }

        // Verificar apenas campos essenciais
        $campos_obrigatorios = ['motor_tipo', 'cilindros', 'val_adm', 'val_esc'];
        
        foreach ($campos_obrigatorios as $campo) {
            if (!isset($cabecote[$campo]) || $cabecote[$campo] === null) {
                echo "<div class='error-msg'>Campo obrigatório não encontrado: " . htmlspecialchars($campo) . "</div>";
                return;
            }
        }

        // Definir valores padrão para campos opcionais
        $campos_opcionais = [
            'val_adm_limite_min' => 0.00,
            'val_adm_limite_max' => 0.00,
            'val_esc_limite_min' => 0.00,
            'val_esc_limite_max' => 0.00,
            'cames_adm_diam_max' => 0.00,
            'cames_esc_diam_max' => 0.00,
            'cames_diam_min' => 0.00,
            'compressao_min' => 0.00,
            'compressao_max' => 0.00
        ];

        foreach ($campos_opcionais as $campo => $valor_padrao) {
            if (!isset($cabecote[$campo]) || $cabecote[$campo] === null) {
                $cabecote[$campo] = $valor_padrao;
            } else {
                // Converter string com vírgula para float
                $cabecote[$campo] = str_replace(',', '.', $cabecote[$campo]);
                $cabecote[$campo] = floatval($cabecote[$campo]);
            }
        }

        // Buscar dados medidos do cabeçote
        $query = "SELECT * FROM cabecote WHERE is_reference = 0 AND ordem = ?";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar consulta de medições: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "s", $ordem);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Erro ao executar consulta de medições: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $medicoes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $medicoes[] = $row;
        }

        // Calcular quantos cilindros são da frente e quantos são de trás
        $cilindros_tras = ceil($cabecote['cilindros'] / 2);
        $cilindros_frente = $cabecote['cilindros'] - $cilindros_tras;

        echo "<div class='card cabecote-medicoes'>";
        echo "<h2 class='card-title'>MENU MEDIÇÕES CABEÇOTE</h2>";
        echo "<div class='legenda'>Medição de válvulas para cada cilindro</div>";
        echo "<div> Tipo do motor: <div class='subtitulo'> " . htmlspecialchars($cabecote['motor_tipo']) . "</div></div>";
        
        // Cabeçalho da tabela
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='cabecote'>";
        echo "<input type='hidden' name='update' value='1'>";
        echo "<table>";
        
        // Primeira linha com identificadores de frente/trás
        echo "<thead><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        
        // Calcular quantos cilindros são da frente e quantos são de trás
        $cilindros_tras = ceil($cabecote['cilindros'] / 2);
        $cilindros_frente = $cabecote['cilindros'] - $cilindros_tras;
        
        // Cilindros de trás
        for ($i = 1; $i <= $cilindros_tras; $i++) {
            echo "<th class='cilindro-tras'>CILINDRO " . $i . "</th>";
        }
        
        // Cilindros da frente
        for ($i = $cilindros_tras + 1; $i <= $cabecote['cilindros']; $i++) {
            echo "<th class='cilindro-frente'>CILINDRO " . $i . "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";

        // Válvulas de admissão
        for ($i = 1; $i <= $cabecote['val_adm']; $i++) {
            $lado = ($i == 1) ? 'direita' : 'esquerda';
            
            if ($cabecote['val_adm_limite_min'] > 0 && $cabecote['val_adm_limite_max'] > 0) {
                echo "<tr class='valvula-admissao'>";
                echo "<td>Folga válvula admissão " . $lado . "</td>";
                echo "<td>" . formatarIntervalo($cabecote['val_adm_limite_min'], $cabecote['val_adm_limite_max']) . "</td>";
                
                for ($c = 1; $c <= $cabecote['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<input type='text' 
                        name='medida[adm_folga_" . $lado . "][" . $c . "]' 
                        class='meas-input folga-input' 
                        data-cilindro='" . $c . "' 
                        data-tipo='adm' 
                        data-lado='" . $lado . "' 
                        onchange='calcularPastilha(this)'>";
                    echo "</td>";
                }
                echo "</tr>";
            }

            // Só exibe as pastilhas se tiver tucho
            if (isset($cabecote['tucho']) && $cabecote['tucho'] == 1) {
                echo "<tr class='valvula-admissao'>";
                echo "<td>Pastilha válvula admissão " . $lado . "</td>";
                echo "<td>-</td>";
                
                for ($c = 1; $c <= $cabecote['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<div class='pastilha-container'>";
                    echo "<input type='text' 
                        name='medida[adm_pastilha_" . $lado . "][" . $c . "]' 
                        class='meas-input pastilha-input'>";
                    echo "<div class='pastilha-corrigida' 
                        id='pc_adm_" . $lado . "_" . $c . "'>-</div>";
                    echo "</div>";
                    echo "</td>";
                }
                echo "</tr>";
            }
        }

        // Válvulas de escape
        for ($i = 1; $i <= $cabecote['val_esc']; $i++) {
            $lado = ($i == 1) ? 'direita' : 'esquerda';
            
            if ($cabecote['val_esc_limite_min'] > 0 && $cabecote['val_esc_limite_max'] > 0) {
                echo "<tr class='valvula-escape'>";
                echo "<td>Folga válvula escape " . $lado . "</td>";
                echo "<td>" . formatarIntervalo($cabecote['val_esc_limite_min'], $cabecote['val_esc_limite_max']) . "</td>";
                
                for ($c = 1; $c <= $cabecote['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<input type='text' 
                        name='medida[esc_folga_" . $lado . "][" . $c . "]' 
                        class='meas-input folga-input' 
                        data-cilindro='" . $c . "' 
                        data-tipo='esc' 
                        data-lado='" . $lado . "' 
                        onchange='calcularPastilha(this)'>";
                    echo "</td>";
                }
                echo "</tr>";
            }

            // Só exibe as pastilhas se tiver tucho
            if (isset($cabecote['tucho']) && $cabecote['tucho'] == 1) {
                echo "<tr class='valvula-escape'>";
                echo "<td>Pastilha válvula escape " . $lado . "</td>";
                echo "<td>-</td>";
                
                for ($c = 1; $c <= $cabecote['cilindros']; $c++) {
                    $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                    echo "<td class='" . $classe_cilindro . "'>";
                    echo "<div class='pastilha-container'>";
                    echo "<input type='text' 
                        name='medida[esc_pastilha_" . $lado . "][" . $c . "]' 
                        class='meas-input pastilha-input'>";
                    echo "<div class='pastilha-corrigida' 
                        id='pc_esc_" . $lado . "_" . $c . "'>-</div>";
                    echo "</div>";
                    echo "</td>";
                }
                echo "</tr>";
            }
        }

        // Adicionar medição de compressão aqui, antes do separador
        if ($cabecote['compressao_min'] > 0 && $cabecote['compressao_max'] > 0) {
            echo "<tr class='valvula-admissao'>";
            echo "<td>Compressão</td>";
            echo "<td>" . formatarIntervalo($cabecote['compressao_min'], $cabecote['compressao_max']) . "</td>";
            
            for ($c = 1; $c <= $cabecote['cilindros']; $c++) {
                $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                echo "<td class='" . $classe_cilindro . "'>";
                echo "<input type='text' 
                    name='medida[compressao][" . $c . "]' 
                    class='meas-input'>";
                echo "</td>";
            }
            echo "</tr>";
        }

        // Separador entre medições de cilindros e medições gerais
        echo "<tr class='separador'>";
        echo "<td colspan='" . ($cabecote['cilindros'] + 2) . "'>MEDIÇÕES GERAIS DO CABEÇOTE</td>";
        echo "</tr>";

        // Itens fixos - só exibe se houver valores válidos
        if ($cabecote['cames_adm_diam_max'] > 0) {
            echo "<tr class='item-fixo'>";
            echo "<td>Diâmetro eixo cames admissão</td>";
            echo "<td>" . number_format($cabecote['cames_adm_diam_max'], 2, ',', '.') . "</td>";
            
            // Cilindros de trás
            for ($cil = 1; $cil <= $cilindros_tras; $cil++) {
                echo "<td class='cilindro-tras'>-</td>";
            }
            
            // Cilindros da frente
            for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
                echo "<td class='cilindro-frente'>-</td>";
            }
            echo "</tr>";
        }

        if ($cabecote['cames_esc_diam_max'] > 0) {
            echo "<tr class='item-fixo'>";
            echo "<td>Diâmetro eixo cames escape</td>";
            echo "<td>" . number_format($cabecote['cames_esc_diam_max'], 2, ',', '.') . "</td>";
            
            // Cilindros de trás
            for ($cil = 1; $cil <= $cilindros_tras; $cil++) {
                echo "<td class='cilindro-tras'>-</td>";
            }
            
            // Cilindros da frente
            for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
                echo "<td class='cilindro-frente'>-</td>";
            }
            echo "</tr>";
        }

        // Valores fixos que sempre devem aparecer
        echo "<tr class='item-fixo'>";
        echo "<td>Empenamento eixo cames adm/esc</td>";
        echo "<td>0,10</td>";
        
        // Cilindros de trás
        for ($cil = 1; $cil <= $cilindros_tras; $cil++) {
            echo "<td class='cilindro-tras'>";
            echo "<input type='text' 
                name='medida[eixo_cames_lim_empen][" . $cil . "]' 
                class='meas-input'>";
            echo "</td>";
        }
        
        // Cilindros da frente
        for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
            echo "<td class='cilindro-frente'>";
            echo "<input type='text' 
                name='medida[eixo_cames_lim_empen][" . $cil . "]' 
                class='meas-input'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Folga eixo de cames/mancal</td>";
        echo "<td>0,15</td>";
        
        // Cilindros de trás
        for ($cil = 1; $cil <= $cilindros_tras; $cil++) {
            echo "<td class='cilindro-tras'>";
            echo "<input type='text' 
                name='medida[folga_eixo_mancal][" . $cil . "]' 
                class='meas-input'>";
            echo "</td>";
        }
        
        // Cilindros da frente
        for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
            echo "<td class='cilindro-frente'>";
            echo "<input type='text' 
                name='medida[folga_eixo_mancal][" . $cil . "]' 
                class='meas-input'>";
            echo "</td>";
        }
        echo "</tr>";

        // Adicionar campos para altura dos cames
        echo "<tr class='item-fixo'>";
        echo "<td>Came admissão altura min</td>";
        echo "<td>" . number_format($cabecote['came_adm_altura_min'], 2, ',', '.') . "</td>";
        
        // Cilindros de trás
        for ($cil = 1; $cil <= $cilindros_tras; $cil++) {
            echo "<td class='cilindro-tras'>";
            echo "<input type='text' 
                name='medida[came_adm_altura_min][" . $cil . "]' 
                class='meas-input'>";
            echo "</td>";
        }
        
        // Cilindros da frente
        for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
            echo "<td class='cilindro-frente'>";
            echo "<input type='text' 
                name='medida[came_adm_altura_min][" . $cil . "]' 
                class='meas-input'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Came escape altura min</td>";
        echo "<td>" . number_format($cabecote['came_esc_altura_min'], 2, ',', '.') . "</td>";
        
        // Cilindros de trás
        for ($cil = 1; $cil <= $cilindros_tras; $cil++) {
            echo "<td class='cilindro-tras'>";
            echo "<input type='text' 
                name='medida[came_esc_altura_min][" . $cil . "]' 
                class='meas-input'>";
            echo "</td>";
        }
        
        // Cilindros da frente
        for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
            echo "<td class='cilindro-frente'>";
            echo "<input type='text' 
                name='medida[came_esc_altura_min][" . $cil . "]' 
                class='meas-input'>";
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

// Adicione este código para processar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === 'cabecote') {
    try {
        // Verificar se já existe medição
        $checkQuery = "SELECT COUNT(*) as count FROM cabecote WHERE is_reference = 0 AND ordem = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $ordem);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        $checkRow = mysqli_fetch_assoc($checkResult);

        if ($checkRow['count'] == 0) {
            // Inserir nova medição
            $insertQuery = "INSERT INTO cabecote (ordem, is_reference) VALUES (?, 0)";
            $insertStmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, "s", $ordem);
            mysqli_stmt_execute($insertStmt);
            $medicao_id = mysqli_insert_id($conn);
        }

        // Atualizar medições
        if (isset($_POST['medida'])) {
            foreach ($_POST['medida'] as $tipo => $cilindros) {
                foreach ($cilindros as $cilindro => $valor) {
                    // Aqui você precisará criar a lógica para atualizar cada medição
                    // baseado na estrutura do seu banco de dados
                }
            }
            echo "<div class='success-msg'>Medições salvas com sucesso!</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro ao salvar medições: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

displayTableData($conn, "embreagem", "Embreagem");
displayTableData($conn, "bomba", "Bomba");
displayTableData($conn, "motor", "Motor");
displayTableData($conn, "virabrequim", "Virabrequim");
displayTableData($conn, "cabecote", "Cabeçote");

// Chamar a função de medições do cabeçote
if (isset($_GET['ordem'])) {
    displayCabecoteMedicoes($conn, $_GET['ordem']);
} else {
    echo "<div class='error-msg'>Erro: Parâmetro 'ordem' não foi especificado.</div>";
}

echo '<a class="button primary" id="closeModal3">Sair</a>';
?>

<input type="hidden" id="val_adm_limite_min" value="<?php echo $cabecote['val_adm_limite_min']; ?>">
<input type="hidden" id="val_adm_limite_max" value="<?php echo $cabecote['val_adm_limite_max']; ?>">
<input type="hidden" id="val_esc_limite_min" value="<?php echo $cabecote['val_esc_limite_min']; ?>">
<input type="hidden" id="val_esc_limite_max" value="<?php echo $cabecote['val_esc_limite_max']; ?>">

<style>
.card {
    background: #1e2029;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    margin: 15px 0;
    padding: 15px;
    width: 100%;
    max-width: 100%;
    color: #e5e5e5;
    border: 1px solid #333;
}

.card-title {
    color: #fff;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
    margin-bottom: 15px;
    font-size: 1.2em;
}

.card-content {
    padding: 10px;
}

.table-container {
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

th {
    background: #2a2c35;
    color: #fff;
}

.data-label {
    font-weight: bold;
    color: #aaa;
}

.ref-value {
    color: #e5e5e5;
    text-align: right;
}

.meas-values {
    color: #4CAF50;
    text-align: right;
}

.meas-input {
    background: #2a2c35;
    border: 1px solid #4CAF50;
    border-radius: 4px;
    padding: 5px;
    color: #fff;
    text-align: right;
    width: 100%;
    margin: 2px 0;
}

.meas-input.first {
    margin-top: 0;
}

.table-form {
    margin-top: 15px;
}

.save-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.2s ease-in-out;
}

.save-btn:hover {
    background-color: #45a049;
}

#closeModal3 {
    display: block;
    margin: 20px auto;
    padding: 0 1.75em;
    background-color: #ed4933;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    width: fit-content;
    text-transform: uppercase;
    letter-spacing: 0.25em;
    font-size: 0.8em;
    height: 3.5em;
    line-height: 3.5em;
    transition: background-color 0.2s ease-in-out;
    box-shadow: none;
}

#closeModal3:hover {
    background-color: #ef5e4a;
}
.success-msg {
    color: #4CAF50;
    margin: 10px 0;
    font-weight: bold;
}

.error-msg {
    color: #ed4933;
    margin: 10px 0;
    font-weight: bold;
}

.cabecote-medicoes {
    margin-top: 20px;
}

.cabecote-medicoes .legenda {
    color: #888;
    font-size: 0.9em;
    margin-bottom: 10px;
}

.cabecote-medicoes .subtitulo {
    font-size: 1.1em;
    font-weight: bold;
    margin-bottom: 15px;
    color: #4CAF50;
}

.cabecote-medicoes .secao {
    margin-bottom: 30px;
}

.cabecote-medicoes .secao h3 {
    color: #fff;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 1px solid #333;
}

.cabecote-medicoes table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.cabecote-medicoes th {
    background: #2a2c35;
    color: #fff;
    padding: 10px;
    text-align: left;
    font-weight: bold;
}

.cabecote-medicoes td {
    padding: 8px 10px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.cabecote-medicoes .valvula-admissao {
    background-color: rgba(33, 150, 243, 0.1);
}

.cabecote-medicoes .valvula-escape {
    background-color: rgba(233, 30, 99, 0.1);
}

.cabecote-medicoes .item-fixo {
    background-color: rgba(255, 255, 255, 0.05);
}

.cabecote-medicoes td:first-child {
    font-weight: bold;
    color: #e5e5e5;
}

.cabecote-medicoes td:nth-child(2) {
    color: #4CAF50;
    text-align: right;
}

.cabecote-medicoes td:not(:first-child):not(:nth-child(2)) {
    text-align: right;
    color: #888;
}

.cabecote-medicoes td:not(:first-child):not(:nth-child(2)):not(:empty) {
    color: #fff;
}

.cabecote-medicoes .identificadores {
    background-color: rgba(255, 255, 255, 0.05);
}

.cabecote-medicoes .identificadores th {
    text-align: center;
    font-size: 0.9em;
    padding: 8px;
}

.cabecote-medicoes .cilindro-tras {
    background-color: rgba(33, 150, 243, 0.1);
    border-left: 2px solid #2196F3;
}

.cabecote-medicoes .cilindro-frente {
    background-color: rgba(233, 30, 99, 0.1);
    border-left: 2px solid #E91E63;
}

.cabecote-medicoes td.cilindro-tras {
    background-color: rgba(33, 150, 243, 0.05);
}

.cabecote-medicoes td.cilindro-frente {
    background-color: rgba(233, 30, 99, 0.05);
}

.cabecote-medicoes .titulo-cilindro {
    background-color: rgba(255, 255, 255, 0.1);
    font-weight: bold;
    text-align: center;
    padding: 10px;
}

.cabecote-medicoes .titulo-cilindro.cilindro-tras {
    background-color: rgba(33, 150, 243, 0.2);
    border-left: 2px solid #2196F3;
}

.cabecote-medicoes .titulo-cilindro.cilindro-frente {
    background-color: rgba(233, 30, 99, 0.2);
    border-left: 2px solid #E91E63;
}

.cabecote-medicoes .separador {
    background-color: rgba(255, 255, 255, 0.15);
    font-weight: bold;
    text-align: center;
    padding: 15px;
    margin-top: 20px;
    border-top: 2px solid rgba(255, 255, 255, 0.2);
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.cabecote-medicoes .separador td {
    color: #fff;
    font-size: 1.1em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Adicione estes estilos */
.meas-input[readonly] {
    background-color: #1a1b23;
    border-color: #333;
    color: #666;
    cursor: not-allowed;
}

.pastilha-input {
    background-color: #2a2c35;
    border-color: #4CAF50;
    color: #fff;
    cursor: text;
}

.folga-input {
    background-color: #2a2c35;
    border-color: #4CAF50;
    color: #fff;
    cursor: text;
}

.pastilha-valor {
    color: #4CAF50;
    font-weight: bold;
    min-height: 1em; /* Garante altura mínima mesmo vazio */
}

.valor-calculado {
    text-align: right;
    padding: 8px;
}

.pastilha-container {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.pastilha-corrigida {
    font-size: 0.9em;
    color: #4CAF50;
    text-align: right;
    padding: 2px 4px;
    background: rgba(76, 175, 80, 0.1);
    border-radius: 3px;
}

.pastilha-corrigida::before {
    content: 'PC: ';
    color: #888;
    font-size: 0.9em;
}

/* Estilo para o select do campo tucho */
select.meas-input {
    background: #2a2c35;
    border: 1px solid #4CAF50;
    border-radius: 4px;
    padding: 5px;
    color: #fff;
    text-align: right;
    width: 100%;
    margin: 2px 0;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 8.825L1.175 4 2.238 2.938 6 6.7l3.763-3.763L10.825 4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    padding-right: 30px;
}

select.meas-input:focus {
    outline: none;
    border-color: #45a049;
}

select.meas-input option {
    background: #2a2c35;
    color: #fff;
}

.virabrequim-field.hidden {
    display: none;
}
</style>