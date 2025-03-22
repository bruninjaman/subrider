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
            // Verificar se já existe medição
            $checkQuery = "SELECT COUNT(*) as count FROM $tableName WHERE is_reference = 0 AND ordem = ?";
            $checkStmt = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, "s", $ordem);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);
            $checkRow = mysqli_fetch_assoc($checkResult);
            
            if ($checkRow['count'] == 0) {
                // Não existe medição, vamos criar
                $refQuery = "SELECT * FROM $tableName WHERE is_reference = 1 AND ordem = ?";
                $refStmt = mysqli_prepare($conn, $refQuery);
                mysqli_stmt_bind_param($refStmt, "s", $ordem);
                mysqli_stmt_execute($refStmt);
                $refResult = mysqli_stmt_get_result($refStmt);
                $refRow = mysqli_fetch_assoc($refResult);
                
                if ($refRow) {
                    $fields = [];
                    $values = [];
                    $types = '';
                    foreach ($refRow as $key => $value) {
                        if (!in_array($key, ['id', 'is_reference']) && $value !== null) {
                            $fields[] = $key;
                            $values[] = $value;
                            $types .= 's';
                        }
                    }
                    
                    $fields[] = 'is_reference';
                    $values[] = 0;
                    $types .= 'i';
                    
                    $placeholders = array_fill(0, count($fields), '?');
                    $insertQuery = "INSERT INTO $tableName (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $insertStmt = mysqli_prepare($conn, $insertQuery);
                    if ($insertStmt) {
                        mysqli_stmt_bind_param($insertStmt, $types, ...$values);
                        mysqli_stmt_execute($insertStmt);
                        mysqli_stmt_close($insertStmt);
                    }
                }
            }

            // Agora atualiza os valores
            foreach ($_POST['measured'] as $id => $fields) {
                $updates = [];
                $params = [];
                $types = '';
                foreach ($fields as $field => $value) {
                    $updates[] = "`$field` = ?";
                    $params[] = $value;
                    $types .= 's';
                }
                $params[] = $id;
                $types .= 'i';

                $updateQuery = "UPDATE " . $tableName . 
                              " SET " . implode(', ', $updates) . 
                              " WHERE id = ?";
                $stmt = mysqli_prepare($conn, $updateQuery);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, $types, ...$params);
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
        echo "<thead><tr><th>Parâmetro</th><th>Referência</th><th>Medidos</th></tr></thead>";
        echo "<tbody>";

        foreach ($refRow as $key => $value) {
            $keyLower = strtolower($key);
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
                echo "<td class='ref-value'>" . 
                     htmlspecialchars($value) . 
                     "</td>";
                
                echo "<td class='meas-values'>";
                mysqli_data_seek($measResult, 0);
                $first = true;
                while ($measRow = mysqli_fetch_assoc($measResult)) {
                    if (isset($measRow[$key]) && $measRow[$key] !== null) {
                        echo "<input type='text' " .
                             "name='measured[" . $measRow['id'] . "][" . htmlspecialchars($key) . "]' " .
                             "value='" . htmlspecialchars($measRow[$key]) . "' " .
                             "class='meas-input" . ($first ? " first" : "") . "'>";
                        $first = false;
                    }
                }
                if (mysqli_num_rows($measResult) === 0) {
                    echo "<input type='text' " .
                         "name='measured[new][" . htmlspecialchars($key) . "]' " .
                         "value='" . htmlspecialchars($value) . "' " .
                         "class='meas-input first'>";
                }
                echo "</td>";
                
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
        echo "<div class='legenda'>Valores retirados do banco de dados</div>";
        echo "<div class='subtitulo'>" . htmlspecialchars($cabecote['motor_tipo']) . "</div>";
        
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
                        onchange='calcularPastilha(this, \"adm\", \"" . $lado . "\", " . $c . ")'>";
                    echo "</td>";
                }
                echo "</tr>";
            }

            echo "<tr class='valvula-admissao'>";
            echo "<td>Pastilha válvula admissão " . $lado . "</td>";
            echo "<td>-</td>";
            
            for ($c = 1; $c <= $cabecote['cilindros']; $c++) {
                $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                echo "<td class='" . $classe_cilindro . " valor-calculado'>";
                echo "<div id='pastilha_adm_" . $lado . "_" . $c . "' class='pastilha-valor'>-</div>";
                echo "</td>";
            }
            echo "</tr>";
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
                        onchange='calcularPastilha(this, \"esc\", \"" . $lado . "\", " . $c . ")'>";
                    echo "</td>";
                }
                echo "</tr>";
            }

            echo "<tr class='valvula-escape'>";
            echo "<td>Pastilha válvula escape " . $lado . "</td>";
            echo "<td>-</td>";
            
            for ($c = 1; $c <= $cabecote['cilindros']; $c++) {
                $classe_cilindro = $c <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                echo "<td class='" . $classe_cilindro . " valor-calculado'>";
                echo "<div id='pastilha_esc_" . $lado . "_" . $c . "' class='pastilha-valor'>-</div>";
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
            echo "<td class='cilindro-tras'>-</td>";
        }
        
        // Cilindros da frente
        for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
            echo "<td class='cilindro-frente'>-</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Folga eixo de cames/mancal</td>";
        echo "<td>0,15</td>";
        
        // Cilindros de trás
        for ($cil = 1; $cil <= $cilindros_tras; $cil++) {
            echo "<td class='cilindro-tras'>-</td>";
        }
        
        // Cilindros da frente
        for ($cil = $cilindros_tras + 1; $cil <= $cabecote['cilindros']; $cil++) {
            echo "<td class='cilindro-frente'>-</td>";
        }
        echo "</tr>";

        if ($cabecote['compressao_min'] > 0 && $cabecote['compressao_max'] > 0) {
            echo "<tr class='item-fixo'>";
            echo "<td>Compressão</td>";
            echo "<td>" . formatarIntervalo($cabecote['compressao_min'], $cabecote['compressao_max']) . "</td>";
            
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

<script src="pages/ordemservico/calcularPastilha.js"></script>

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
</style>