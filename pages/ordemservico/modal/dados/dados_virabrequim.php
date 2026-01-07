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
                foreach ($_POST['medida'] as $param => $valor) {
                    $medicoes[$param] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
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
                'folga_eixo_biela' => 0.0,
                'folga_lateral_biela' => 0.0,
                'folga_lateral_eixo_min' => 0.0,
                'folga_lateral_eixo_max' => 0.0,
                'empenamento' => 0.0
            ];
        }

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
        echo "<div class='legenda'>Medição dos parâmetros do virabrequim conforme referência</div>";
        echo "<div> Tipo: <div class='subtitulo'> " . htmlspecialchars($virabrequimRef['tipo']) . "</div></div>";
        
        echo "<div class='table-container'>";
        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='virabrequim'>";
        echo "<input type='hidden' name='ordem' value='" . htmlspecialchars($ordem) . "'>";
        echo "<table>";
        
        // Cabeçalho da tabela
        echo "<thead><tr><th>PARÂMETRO</th><th>REFERÊNCIA</th><th>MEDIDA</th></tr></thead>";
        echo "<tbody>";

        // Exibir todos os campos de referência, exceto id, ordem, is_reference e tipo, com classes conforme o tipo
        $tipoAtual = isset($virabrequimRef['tipo']) ? $virabrequimRef['tipo'] : '';
        foreach ($virabrequimRef as $campo => $referencia) {
            if (in_array($campo, ['id', 'ordem', 'is_reference', 'tipo', 'medicoes'])) continue;
            $campoLower = strtolower($campo);
            $virabrequimClass = '';
            if (in_array($campoLower, ['folga_eixo_biela'])) {
                $virabrequimClass = 'virabrequim-bronzina-field';
            } else if (in_array($campoLower, ['folga_lateral_biela', 'folga_lateral_eixo_min', 'folga_lateral_eixo_max', 'empenamento'])) {
                $virabrequimClass = 'virabrequim-rolamento-field';
            }
            
            // Determinar tipo de validação baseado no nome do campo
            $validationType = 'exact';
            if (strpos($campoLower, '_min') !== false) {
                $validationType = 'min';
            } elseif (strpos($campoLower, '_max') !== false || strpos($campoLower, 'empenamento') !== false) {
                $validationType = 'max';
            }
            
            echo "<tr" . ($virabrequimClass ? " class='$virabrequimClass'" : '') . ">";
            echo "<td>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $campo))) . "</td>";
            echo "<td>" . (is_numeric($referencia) ? number_format($referencia, 2, ',', '.') : htmlspecialchars($referencia)) . "</td>";
            $valor = isset($medicoes[$campo]) && $medicoes[$campo] !== null ? (is_numeric($medicoes[$campo]) ? number_format($medicoes[$campo], 2, ',', '.') : htmlspecialchars($medicoes[$campo])) : '';
            echo "<td><input type='text' name='medida[" . htmlspecialchars($campo) . "]' class='meas-input' data-reference='" . $referencia . "' data-validation-type='" . $validationType . "' value='$valor'></td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "<button type='submit' class='save-btn'>Salvar Medições</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        // Script para alternar campos conforme o tipo
        echo "<script>
        function toggleVirabrequimFields(tipo) {
            var bronzinaFields = document.querySelectorAll('.virabrequim-bronzina-field');
            var rolamentoFields = document.querySelectorAll('.virabrequim-rolamento-field');
            if (tipo === 'Bronzina') {
                bronzinaFields.forEach(function(el) { el.style.display = ''; });
                rolamentoFields.forEach(function(el) { el.style.display = 'none'; });
            } else if (tipo === 'Rolamento') {
                bronzinaFields.forEach(function(el) { el.style.display = 'none'; });
                rolamentoFields.forEach(function(el) { el.style.display = ''; });
            } else {
                bronzinaFields.forEach(function(el) { el.style.display = 'none'; });
                rolamentoFields.forEach(function(el) { el.style.display = 'none'; });
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            toggleVirabrequimFields('" . addslashes($tipoAtual) . "');
        });
        </script>";
        
    } catch (Exception $e) {
        echo "<div class='error-msg'>Erro ao exibir medições do virabrequim: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}