<?php
require_once 'dados_util.php';

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