<?php
require_once 'dados_util.php';

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