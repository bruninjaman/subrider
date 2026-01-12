<?php
require_once 'dados_util.php';

function displayVirabrequimMedicoes($conn, $ordem)
{
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

            // Processar quantidade de cilindros
            if (isset($_POST['qtd_cilindros']) && $_POST['qtd_cilindros'] !== '') {
                $medicoes['qtd_cilindros'] = intval($_POST['qtd_cilindros']);
            }

            // Processar diâmetros de moente (array por cilindro)
            if (isset($_POST['diametro_moente']) && is_array($_POST['diametro_moente'])) {
                $diametrosMoente = [];
                foreach ($_POST['diametro_moente'] as $num => $valor) {
                    $diametrosMoente[$num] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                }
                $medicoes['diametro_moente'] = $diametrosMoente;
            }

            // Processar folga mancal (array por cilindro)
            if (isset($_POST['folga_mancal']) && is_array($_POST['folga_mancal'])) {
                $folgaMancal = [];
                foreach ($_POST['folga_mancal'] as $num => $valor) {
                    $folgaMancal[$num] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                }
                $medicoes['folga_mancal'] = $folgaMancal;
            }

            // Processar folga biela (array por cilindro ou valor único)
            if (isset($_POST['folga_biela']) && is_array($_POST['folga_biela'])) {
                $folgaBiela = [];
                foreach ($_POST['folga_biela'] as $num => $valor) {
                    $folgaBiela[$num] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                }
                $medicoes['folga_biela'] = $folgaBiela;
            }

            // Processar quantidade de munhões
            if (isset($_POST['qtd_munhoes']) && $_POST['qtd_munhoes'] !== '') {
                $medicoes['qtd_munhoes'] = intval($_POST['qtd_munhoes']);
            }

            // Processar diâmetros de munhão (array por munhão)
            if (isset($_POST['diametro_munhao']) && is_array($_POST['diametro_munhao'])) {
                $diametrosMunhao = [];
                foreach ($_POST['diametro_munhao'] as $num => $valor) {
                    $diametrosMunhao[$num] = $valor !== '' ? floatval(str_replace(',', '.', $valor)) : null;
                }
                $medicoes['diametro_munhao'] = $diametrosMunhao;
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
                'folga_biela' => 0.0,
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
            // Pular campos especiais e campos que são processados na seção tabular abaixo
            if (in_array($campo, ['id', 'ordem', 'is_reference', 'tipo', 'medicoes', 'qtd_cilindros', 'qtd_munhoes', 'diametro_moente', 'diametro_munhao', 'folga_mancal', 'folga_biela']))
                continue;

            $campoLower = strtolower($campo);
            $virabrequimClass = '';

            // Campos específicos de Bronzina
            if (
                in_array($campoLower, [
                    'diametro_munhoes',
                    'diametro_moente',
                    'diametro_munhao'
                ])
            ) {
                $virabrequimClass = 'virabrequim-bronzina-field';
            }
            // Campos presentes em ambos ou específicos de Rolamento
            else if (in_array($campoLower, ['folga_biela', 'folga_mancal', 'folga_lateral_eixo_min', 'folga_lateral_eixo_max', 'empenamento'])) {
                $virabrequimClass = 'virabrequim-both-field';
            } else if (in_array($campoLower, ['folga_lateral_biela'])) {
                $virabrequimClass = 'virabrequim-bronzina-field'; // Esconder lateral biela para rolamento conforme lista do usuário
            }

            $validationType = 'exact';
            if (strpos($campoLower, '_min') !== false) {
                $validationType = 'min';
            } elseif (strpos($campoLower, '_max') !== false || strpos($campoLower, 'empenamento') !== false) {
                $validationType = 'max';
            }

            $refDisplay = '-';
            if (is_numeric($referencia) && $referencia != 0) {
                $fRef = (floor($referencia) == $referencia) ? number_format($referencia, 0, ',', '.') : number_format($referencia, 2, ',', '.');
                if ($validationType === 'exact') {
                    $min = $referencia * 0.95;
                    $max = $referencia * 1.05;
                    $fMin = (floor($min) == $min) ? number_format($min, 0, ',', '.') : number_format($min, 2, ',', '.');
                    $fMax = (floor($max) == $max) ? number_format($max, 0, ',', '.') : number_format($max, 2, ',', '.');
                    $refDisplay = "$fMin - $fMax";
                } elseif ($validationType === 'min') {
                    $refDisplay = $fRef . ' (mín)';
                } else {
                    $refDisplay = $fRef . ' (máx)';
                }
            } else {
                $refDisplay = htmlspecialchars($referencia);
            }

            echo "<tr" . ($virabrequimClass ? " class='$virabrequimClass'" : '') . ">";
            echo "<td>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $campo))) . "</td>";
            echo "<td>$refDisplay</td>";
            $valor = isset($medicoes[$campo]) && $medicoes[$campo] !== null ? (is_numeric($medicoes[$campo]) ? ((floor($medicoes[$campo]) == $medicoes[$campo]) ? number_format($medicoes[$campo], 0, ',', '.') : number_format($medicoes[$campo], 2, ',', '.')) : htmlspecialchars($medicoes[$campo])) : '';
            echo "<td><input type='text' name='medida[" . htmlspecialchars($campo) . "]' class='meas-input' data-reference='" . $referencia . "' data-validation-type='" . $validationType . "' value='$valor'></td>";
            echo "</tr>";
        }

        // --- NOVA SEÇÃO TABULAR PARA DIÂMETROS ---
        // Priorizar quantidade definida na referência (menu), fallback para medições existentes
        $qtdCilindros = isset($virabrequimRef['qtd_cilindros']) && $virabrequimRef['qtd_cilindros'] > 0
            ? intval($virabrequimRef['qtd_cilindros'])
            : (isset($medicoes['qtd_cilindros']) ? intval($medicoes['qtd_cilindros']) : 0);

        $qtdMunhoes = isset($virabrequimRef['qtd_munhoes']) && $virabrequimRef['qtd_munhoes'] > 0
            ? intval($virabrequimRef['qtd_munhoes'])
            : (isset($medicoes['qtd_munhoes']) ? intval($medicoes['qtd_munhoes']) : 0);

        $maxColunas = max($qtdCilindros, $qtdMunhoes);

        if ($maxColunas > 0) {
            echo "<tr class='section-header virabrequim-bronzina-field'><td colspan='" . ($maxColunas + 2) . "'><strong>MEDIÇÕES DE DIÂMETRO</strong></td></tr>";

            // Cabeçalho dos cilindros/munhões
            echo "<tr class='virabrequim-bronzina-field'><th>ITEM</th><th>REFERÊNCIA</th>";
            for ($i = 1; $i <= $maxColunas; $i++) {
                echo "<th>" . $i . "</th>";
            }
            echo "</tr>";

            // Linha do Diâmetro Moente
            if ($qtdCilindros > 0) {
                $diametrosMoente = isset($medicoes['diametro_moente']) ? $medicoes['diametro_moente'] : [];
                $refMoente = isset($virabrequimRef['diametro_moente']) ? $virabrequimRef['diametro_moente'] : null;
                $refMoenteDisplay = '-';
                if ($refMoente !== null && is_numeric($refMoente) && $refMoente != 0) {
                    $min = $refMoente * 0.95;
                    $max = $refMoente * 1.05;
                    $fMin = (floor($min) == $min) ? number_format($min, 0, ',', '.') : number_format($min, 2, ',', '.');
                    $fMax = (floor($max) == $max) ? number_format($max, 0, ',', '.') : number_format($max, 2, ',', '.');
                    $refMoenteDisplay = "$fMin - $fMax";
                }

                echo "<tr class='virabrequim-bronzina-field'>";
                echo "<td>Diâmetro Moente (mm)</td>";
                echo "<td>$refMoenteDisplay</td>";
                for ($i = 1; $i <= $maxColunas; $i++) {
                    if ($i <= $qtdCilindros) {
                        $valorMoente = isset($diametrosMoente[$i]) && $diametrosMoente[$i] !== null
                            ? ((floor($diametrosMoente[$i]) == $diametrosMoente[$i]) ? number_format($diametrosMoente[$i], 0, ',', '.') : number_format($diametrosMoente[$i], 2, ',', '.'))
                            : '';
                        echo "<td><input type='text' name='diametro_moente[$i]' class='meas-input' value='$valorMoente'></td>";
                    } else {
                        echo "<td>-</td>";
                    }
                }
                echo "</tr>";
            }

            // Linha do Diâmetro Munhão
            if ($qtdMunhoes > 0) {
                $diametrosMunhao = isset($medicoes['diametro_munhao']) ? $medicoes['diametro_munhao'] : [];
                $refMunhao = isset($virabrequimRef['diametro_munhao']) ? $virabrequimRef['diametro_munhao'] : null;
                $refMunhaoDisplay = '-';
                if ($refMunhao !== null && is_numeric($refMunhao) && $refMunhao != 0) {
                    $min = $refMunhao * 0.95;
                    $max = $refMunhao * 1.05;
                    $fMin = (floor($min) == $min) ? number_format($min, 0, ',', '.') : number_format($min, 2, ',', '.');
                    $fMax = (floor($max) == $max) ? number_format($max, 0, ',', '.') : number_format($max, 2, ',', '.');
                    $refMunhaoDisplay = "$fMin - $fMax";
                }

                echo "<tr class='virabrequim-bronzina-field'>";
                echo "<td>Diâmetro Munhão (mm)</td>";
                echo "<td>$refMunhaoDisplay</td>";
                for ($i = 1; $i <= $maxColunas; $i++) {
                    if ($i <= $qtdMunhoes) {
                        $valorMunhao = isset($diametrosMunhao[$i]) && $diametrosMunhao[$i] !== null
                            ? ((floor($diametrosMunhao[$i]) == $diametrosMunhao[$i]) ? number_format($diametrosMunhao[$i], 0, ',', '.') : number_format($diametrosMunhao[$i], 2, ',', '.'))
                            : '';
                        echo "<td><input type='text' name='diametro_munhao[$i]' class='meas-input' value='$valorMunhao'></td>";
                    } else {
                        echo "<td>-</td>";
                    }
                }
                echo "</tr>";
            }

            // Linha da Folga Mancal (por munhão)
            if ($qtdMunhoes > 0) {
                $folgaMancal = isset($medicoes['folga_mancal']) ? $medicoes['folga_mancal'] : [];
                $refFolgaMancal = isset($virabrequimRef['folga_mancal']) ? $virabrequimRef['folga_mancal'] : (isset($virabrequimRef['folga_eixo_mancal']) ? $virabrequimRef['folga_eixo_mancal'] : null);
                $refFolgaMancalDisplay = '-';
                if ($refFolgaMancal !== null && is_numeric($refFolgaMancal) && $refFolgaMancal != 0) {
                    $min = $refFolgaMancal * 0.95;
                    $max = $refFolgaMancal * 1.05;
                    $fMin = (floor($min) == $min) ? number_format($min, 0, ',', '.') : number_format($min, 2, ',', '.');
                    $fMax = (floor($max) == $max) ? number_format($max, 0, ',', '.') : number_format($max, 2, ',', '.');
                    $refFolgaMancalDisplay = "$fMin - $fMax";
                }

                echo "<tr class='virabrequim-both-field'>";
                echo "<td>Folga Mancal (mm)</td>";
                echo "<td>$refFolgaMancalDisplay</td>";
                for ($i = 1; $i <= $maxColunas; $i++) {
                    if ($i <= $qtdMunhoes) {
                        $valorFolgaMancal = isset($folgaMancal[$i]) && $folgaMancal[$i] !== null
                            ? ((floor($folgaMancal[$i]) == $folgaMancal[$i]) ? number_format($folgaMancal[$i], 0, ',', '.') : number_format($folgaMancal[$i], 2, ',', '.'))
                            : '';
                        echo "<td><input type='text' name='folga_mancal[$i]' class='meas-input' value='$valorFolgaMancal'></td>";
                    } else {
                        echo "<td>-</td>";
                    }
                }
                echo "</tr>";
            }

            // Linha da Folga Biela (por cilindro)
            if ($qtdCilindros > 0) {
                $folgaBiela = isset($medicoes['folga_biela']) ? $medicoes['folga_biela'] : [];
                $refFolgaBiela = isset($virabrequimRef['folga_biela']) ? $virabrequimRef['folga_biela'] : null;
                $refFolgaBielaDisplay = '-';
                if ($refFolgaBiela !== null && is_numeric($refFolgaBiela) && $refFolgaBiela != 0) {
                    $min = $refFolgaBiela * 0.95;
                    $max = $refFolgaBiela * 1.05;
                    $fMin = (floor($min) == $min) ? number_format($min, 0, ',', '.') : number_format($min, 2, ',', '.');
                    $fMax = (floor($max) == $max) ? number_format($max, 0, ',', '.') : number_format($max, 2, ',', '.');
                    $refFolgaBielaDisplay = "$fMin - $fMax";
                }

                $labelBiela = ($tipoAtual === 'bronzina') ? 'Folga Bronzina (mm)' : 'Folga Biela (mm)';

                echo "<tr class='virabrequim-both-field'>";
                echo "<td>$labelBiela</td>";
                echo "<td>$refFolgaBielaDisplay</td>";
                for ($i = 1; $i <= $maxColunas; $i++) {
                    if ($i <= $qtdCilindros) {
                        $valorFolgaBiela = isset($folgaBiela[$i]) && $folgaBiela[$i] !== null
                            ? ((floor($folgaBiela[$i]) == $folgaBiela[$i]) ? number_format($folgaBiela[$i], 0, ',', '.') : number_format($folgaBiela[$i], 2, ',', '.'))
                            : '';
                        echo "<td><input type='text' name='folga_biela[$i]' class='meas-input' value='$valorFolgaBiela'></td>";
                    } else {
                        echo "<td>-</td>";
                    }
                }
                echo "</tr>";
            }
        }

        // Campos hidden para manter as quantidades ao salvar
        echo "<input type='hidden' name='qtd_cilindros' value='$qtdCilindros'>";
        echo "<input type='hidden' name='qtd_munhoes' value='$qtdMunhoes'>";

        echo "</tbody></table>";
        echo "<button type='submit' class='save-btn'>Salvar Medições</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        // Script para alternar campos conforme o tipo
        echo "<script>
        function toggleVirabrequimFields(tipo) {
            if (!tipo) return;
            var t = tipo.toLowerCase();
            var bronzinaFields = document.querySelectorAll('.virabrequim-bronzina-field');
            var rolamentoFields = document.querySelectorAll('.virabrequim-rolamento-field');
            var bothFields = document.querySelectorAll('.virabrequim-both-field');

            if (t === 'bronzina') {
                bronzinaFields.forEach(function(el) { el.style.display = ''; });
                rolamentoFields.forEach(function(el) { el.style.display = 'none'; });
                bothFields.forEach(function(el) { el.style.display = ''; });
            } else if (t === 'rolamento') {
                bronzinaFields.forEach(function(el) { el.style.display = 'none'; });
                rolamentoFields.forEach(function(el) { el.style.display = ''; });
                bothFields.forEach(function(el) { el.style.display = ''; });
            } else {
                bronzinaFields.forEach(function(el) { el.style.display = 'none'; });
                rolamentoFields.forEach(function(el) { el.style.display = 'none'; });
                bothFields.forEach(function(el) { el.style.display = ''; });
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