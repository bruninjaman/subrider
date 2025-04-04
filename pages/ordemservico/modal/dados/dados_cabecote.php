<?php
require_once 'dados_util.php';

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
                'cames_esc_diam_max' => 0.00,
                'cames_diam_min' => 0.00,
                'compressao_min' => 0.00,
                'compressao_max' => 0.00,
                'tucho' => 0,
                'came_adm_altura_min' => 0.00,
                'came_esc_altura_min' => 0.00
            ];
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
        $cilindros_tras = ceil((isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0) / 2);
        $cilindros_frente = (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0) - $cilindros_tras;

        // Exibir interface
        echo "<div class='card cabecote-medicoes'>";
        echo "<h2 class='card-title'>MENU MEDIÇÕES CABEÇOTE</h2>";
        echo "<div class='legenda'>Medição de válvulas para cada cilindro</div>";
        echo "<div> Tipo do motor: <div class='subtitulo'> " . htmlspecialchars(isset($cabecoteRef['motor_tipo']) ? $cabecoteRef['motor_tipo'] : '') . "</div></div>";
        
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='cabecote'>";
        echo "<input type='hidden' name='ordem' value='" . htmlspecialchars($ordem) . "'>";
        echo "<table>";
        
        if ((isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0) > 1) {
        // Cabeçalho da tabela
        echo "<thead><tr><th></th><th></th>";
            echo "<th colspan='" . $cilindros_tras . "' class='cabecote-tras-header'>CABEÇOTE TRASEIRO</th>";
            // Adicionar identificação para cabeçote dianteiro
            echo "<th colspan='" . $cilindros_frente . "' class='cabecote-frente-header'>CABEÇOTE DIANTEIRO</th>";
        }
        echo "</tr><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        for ($i = 1; $i <= $cilindros_tras; $i++) {
            echo "<th class='cilindro-tras'>CILINDRO " . $i . "</th>";
        }
        for ($i = $cilindros_tras + 1; $i <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $i++) {
            echo "<th class='cilindro-frente'>CILINDRO " . $i . "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";

        // Válvulas de admissão
        for ($i = 1; $i <= (isset($cabecoteRef['val_adm']) ? $cabecoteRef['val_adm'] : 0); $i++) {
            $lado = ($i == 1) ? 'direita' : 'esquerda';
            if (isset($cabecoteRef['val_adm_limite_min']) && isset($cabecoteRef['val_adm_limite_max']) && 
                $cabecoteRef['val_adm_limite_min'] > 0 && $cabecoteRef['val_adm_limite_max'] > 0) {
                echo "<tr class='valvula-admissao'>";
                echo "<td>Folga válvula admissão " . $lado . "</td>";
                echo "<td>" . (isset($cabecoteRef['val_adm_limite_min']) && isset($cabecoteRef['val_adm_limite_max']) ? 
                    formatarIntervalo($cabecoteRef['val_adm_limite_min'], $cabecoteRef['val_adm_limite_max']) : '0,00 - 0,00') . "</td>";
                for ($c = 1; $c <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $c++) {
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
                for ($c = 1; $c <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $c++) {
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
        for ($i = 1; $i <= (isset($cabecoteRef['val_esc']) ? $cabecoteRef['val_esc'] : 0); $i++) {
            $lado = ($i == 1) ? 'direita' : 'esquerda';
            if (isset($cabecoteRef['val_esc_limite_min']) && isset($cabecoteRef['val_esc_limite_max']) && 
                $cabecoteRef['val_esc_limite_min'] > 0 && $cabecoteRef['val_esc_limite_max'] > 0) {
                echo "<tr class='valvula-escape'>";
                echo "<td>Folga válvula escape " . $lado . "</td>";
                echo "<td>" . (isset($cabecoteRef['val_esc_limite_min']) && isset($cabecoteRef['val_esc_limite_max']) ? 
                    formatarIntervalo($cabecoteRef['val_esc_limite_min'], $cabecoteRef['val_esc_limite_max']) : '0,00 - 0,00') . "</td>";
                for ($c = 1; $c <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $c++) {
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
                for ($c = 1; $c <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $c++) {
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
        if (isset($cabecoteRef['compressao_min']) && isset($cabecoteRef['compressao_max']) && 
            $cabecoteRef['compressao_min'] > 0 && $cabecoteRef['compressao_max'] > 0) {
            echo "<tr class='valvula-admissao'>";
            echo "<td>Compressão</td>";
            echo "<td>" . (isset($cabecoteRef['compressao_min']) && isset($cabecoteRef['compressao_max']) ? 
                formatarIntervalo($cabecoteRef['compressao_min'], $cabecoteRef['compressao_max']) : '0,00 - 0,00') . "</td>";
            for ($c = 1; $c <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $c++) {
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
        echo "<td colspan='" . ((isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0) + 2) . "'>MEDIÇÕES GERAIS DO CABEÇOTE</td>";
        echo "</tr>";

        // Itens fixos
        if (isset($cabecoteRef['cames_adm_diam_max']) && $cabecoteRef['cames_adm_diam_max'] > 0) {
            echo "<tr class='item-fixo'>";
            echo "<td>Diâmetro eixo cames admissão</td>";
            echo "<td>" . number_format($cabecoteRef['cames_adm_diam_max'], 2, ',', '.') . "</td>";
            for ($cil = 1; $cil <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $cil++) {
                $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                echo "<td class='" . $classe_cilindro . "'>-</td>";
            }
            echo "</tr>";
        }

        if (isset($cabecoteRef['cames_esc_diam_max']) && $cabecoteRef['cames_esc_diam_max'] > 0) {
            echo "<tr class='item-fixo'>";
            echo "<td>Diâmetro eixo cames escape</td>";
            echo "<td>" . number_format($cabecoteRef['cames_esc_diam_max'], 2, ',', '.') . "</td>";
            for ($cil = 1; $cil <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $cil++) {
                $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
                echo "<td class='" . $classe_cilindro . "'>-</td>";
            }
            echo "</tr>";
        }

        echo "<tr class='item-fixo'>";
        echo "<td>Empenamento eixo cames adm/esc</td>";
        echo "<td>0,10</td>";
        for ($cil = 1; $cil <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $cil++) {
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
        for ($cil = 1; $cil <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $cil++) {
            $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['folga_eixo_mancal'][$cil]) && $medicoes['folga_eixo_mancal'][$cil] !== null ? number_format($medicoes['folga_eixo_mancal'][$cil], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'>";
            echo "<input type='text' name='medida[folga_eixo_mancal][" . $cil . "]' class='meas-input' value='$valor'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Came admissão altura min</td>";
        echo "<td>" . (isset($cabecoteRef['came_adm_altura_min']) ? number_format($cabecoteRef['came_adm_altura_min'], 2, ',', '.') : '0,00') . "</td>";
        for ($cil = 1; $cil <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $cil++) {
            $classe_cilindro = $cil <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['came_adm_altura_min'][$cil]) && $medicoes['came_adm_altura_min'][$cil] !== null ? number_format($medicoes['came_adm_altura_min'][$cil], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'>";
            echo "<input type='text' name='medida[came_adm_altura_min][" . $cil . "]' class='meas-input' value='$valor'>";
            echo "</td>";
        }
        echo "</tr>";

        echo "<tr class='item-fixo'>";
        echo "<td>Came escape altura min</td>";
        echo "<td>" . (isset($cabecoteRef['came_esc_altura_min']) ? number_format($cabecoteRef['came_esc_altura_min'], 2, ',', '.') : '0,00') . "</td>";
        for ($cil = 1; $cil <= (isset($cabecoteRef['cilindros']) ? $cabecoteRef['cilindros'] : 0); $cil++) {
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