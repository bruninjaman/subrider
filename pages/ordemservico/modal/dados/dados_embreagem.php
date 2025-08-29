<?php
require_once 'dados_util.php';

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
                data-reference='" . $embreagemRef['disco_friccao_espes_min'] . "' 
                data-validation-type='min' 
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
                data-reference='" . $embreagemRef['disco_separador_emp_max'] . "' 
                data-validation-type='max' 
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