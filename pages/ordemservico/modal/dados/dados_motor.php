<?php
require_once 'dados_util.php';

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
        
        // Calcular cilindros de trás e frente
        $cilindros_tras = ceil($motorRef['nr_cilindros'] / 2);
        $cilindros_frente = $motorRef['nr_cilindros'] - $cilindros_tras;
        
        if ($motorRef['nr_cilindros'] > 1) {
            echo "<thead><tr><th></th><th></th>";
            // Adicionar identificação para cabeçote traseiro
            echo "<th colspan='" . $cilindros_tras . "' class='cabecote-tras-header'>CABEÇOTE TRASEIRO</th>";
            // Adicionar identificação para cabeçote dianteiro
            echo "<th colspan='" . $cilindros_frente . "' class='cabecote-frente-header'>CABEÇOTE DIANTEIRO</th>";
        }
        echo "</tr><tr><th>ITEM</th><th>REFERÊNCIA</th>";
        for ($i = 1; $i <= $cilindros_tras; $i++) {
            echo "<th class='cilindro-tras'>CILINDRO " . $i . "</th>";
        }
        for ($i = $cilindros_tras + 1; $i <= $motorRef['nr_cilindros']; $i++) {
            echo "<th class='cilindro-frente'>CILINDRO " . $i . "</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";

        // Medição de curso do pistão
        echo "<tr class='curso-pistao'>";
        echo "<td>Curso do pistão</td>";
        echo "<td>" . number_format($motorRef['curso_pistao'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['curso_pistao'][$i]) && $medicoes['curso_pistao'][$i] !== null ? number_format($medicoes['curso_pistao'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[curso_pistao][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de diâmetro do cilindro
        echo "<tr class='diametro-cilindro'>";
        echo "<td>Diâmetro do cilindro máximo</td>";
        echo "<td>" . number_format($motorRef['diametro_cilindro_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['diametro_cilindro_max'][$i]) && $medicoes['diametro_cilindro_max'][$i] !== null ? number_format($medicoes['diametro_cilindro_max'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[diametro_cilindro_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de conicidade
        echo "<tr class='conicidade'>";
        echo "<td>Conicidade máxima</td>";
        echo "<td>" . number_format($motorRef['conicidade_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['conicidade_max'][$i]) && $medicoes['conicidade_max'][$i] !== null ? number_format($medicoes['conicidade_max'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[conicidade_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de ovalização
        echo "<tr class='ovalizacao'>";
        echo "<td>Ovalização máxima</td>";
        echo "<td>" . number_format($motorRef['ovalizacao_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['ovalizacao_max'][$i]) && $medicoes['ovalizacao_max'][$i] !== null ? number_format($medicoes['ovalizacao_max'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[ovalizacao_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de diâmetro do pistão
        echo "<tr class='diametro-pistao'>";
        echo "<td>Diâmetro do pistão mínimo</td>";
        echo "<td>" . number_format($motorRef['diametro_pistao_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['diametro_pistao_min'][$i]) && $medicoes['diametro_pistao_min'][$i] !== null ? number_format($medicoes['diametro_pistao_min'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[diametro_pistao_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de folga cilindro/pistão
        echo "<tr class='folga-cil-pis'>";
        echo "<td>Folga cilindro/pistão máxima</td>";
        echo "<td>" . number_format($motorRef['folga_cil_pis_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['folga_cil_pis_max'][$i]) && $medicoes['folga_cil_pis_max'][$i] !== null ? number_format($medicoes['folga_cil_pis_max'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[folga_cil_pis_max][" . $i . "]' class='meas-input' value='$valor'></td>";
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
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['aber_anel_1_max'][$i]) && $medicoes['aber_anel_1_max'][$i] !== null ? number_format($medicoes['aber_anel_1_max'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[aber_anel_1_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de abertura do anel 2
        echo "<tr class='anel-2'>";
        echo "<td>Abertura do anel 2 máxima</td>";
        echo "<td>" . number_format($motorRef['aber_anel_2_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['aber_anel_2_max'][$i]) && $medicoes['aber_anel_2_max'][$i] !== null ? number_format($medicoes['aber_anel_2_max'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[aber_anel_2_max][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de pressão do anel 1
        echo "<tr class='anel-1'>";
        echo "<td>Pressão do anel 1 mínima</td>";
        echo "<td>" . number_format($motorRef['aber_anel_1_pres_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['aber_anel_1_pres_min'][$i]) && $medicoes['aber_anel_1_pres_min'][$i] !== null ? number_format($medicoes['aber_anel_1_pres_min'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[aber_anel_1_pres_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de pressão do anel 2
        echo "<tr class='anel-2'>";
        echo "<td>Pressão do anel 2 mínima</td>";
        echo "<td>" . number_format($motorRef['aber_anel_2_pres_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['aber_anel_2_pres_min'][$i]) && $medicoes['aber_anel_2_pres_min'][$i] !== null ? number_format($medicoes['aber_anel_2_pres_min'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[aber_anel_2_pres_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de largura do anel 1
        echo "<tr class='anel-1'>";
        echo "<td>Largura do anel 1 mínima</td>";
        echo "<td>" . number_format($motorRef['larg_anel_1_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['larg_anel_1_min'][$i]) && $medicoes['larg_anel_1_min'][$i] !== null ? number_format($medicoes['larg_anel_1_min'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[larg_anel_1_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de largura do anel 2
        echo "<tr class='anel-2'>";
        echo "<td>Largura do anel 2 mínima</td>";
        echo "<td>" . number_format($motorRef['larg_anel_2_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['larg_anel_2_min'][$i]) && $medicoes['larg_anel_2_min'][$i] !== null ? number_format($medicoes['larg_anel_2_min'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[larg_anel_2_min][" . $i . "]' class='meas-input' value='$valor'></td>";
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
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['dia_furo_pis_min'][$i]) && $medicoes['dia_furo_pis_min'][$i] !== null ? number_format($medicoes['dia_furo_pis_min'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[dia_furo_pis_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de diâmetro do pino do pistão
        echo "<tr class='pino'>";
        echo "<td>Diâmetro do pino do pistão mínimo</td>";
        echo "<td>" . number_format($motorRef['dia_pino_pis_min'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['dia_pino_pis_min'][$i]) && $medicoes['dia_pino_pis_min'][$i] !== null ? number_format($medicoes['dia_pino_pis_min'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[dia_pino_pis_min][" . $i . "]' class='meas-input' value='$valor'></td>";
        }
        echo "</tr>";

        // Medição de folga do pino do pistão
        echo "<tr class='pino'>";
        echo "<td>Folga do pino do pistão máxima</td>";
        echo "<td>" . number_format($motorRef['folga_pino_pis_max'], 2, ',', '.') . "</td>";
        for ($i = 1; $i <= $motorRef['nr_cilindros']; $i++) {
            $classe_cilindro = $i <= $cilindros_tras ? 'cilindro-tras' : 'cilindro-frente';
            $valor = isset($medicoes['folga_pino_pis_max'][$i]) && $medicoes['folga_pino_pis_max'][$i] !== null ? number_format($medicoes['folga_pino_pis_max'][$i], 2, ',', '.') : '';
            echo "<td class='" . $classe_cilindro . "'><input type='text' name='medida[folga_pino_pis_max][" . $i . "]' class='meas-input' value='$valor'></td>";
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