<?php

if (!isset($_GET['ordem'])) {
    echo "<div class='error-msg'>Ordem de serviço não especificada.</div>";
    exit;
}

$ordem = $_GET['ordem'];

if (!$conn) {
    echo "<div class='error-msg'>Erro na conexão com o banco de dados.</div>";
    exit;
}

function formatNumber($value)
{
    if ($value === null || $value === '' || $value == 0)
        return null;
    return is_numeric($value) ? number_format($value, 2, ',', '.') : htmlspecialchars($value);
}

function formatPastilha($value)
{
    if ($value === null || $value === '' || $value == 0)
        return null;
    // Para pastilhas, dividir por 100 para converter de centésimos para centímetros
    return is_numeric($value) ? number_format($value, 2, ',', '.') : htmlspecialchars($value);
}

function getReferenceData($conn, $ordem, $table)
{
    $query = "SELECT * FROM $table WHERE is_reference = 1 AND ordem = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $ordem);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function isInRange($value, $reference, $validationType)
{
    if ($value === null || $value === '' || $reference === null || $reference === '' || $reference == 0)
        return true;

    if (!is_numeric($value) || !is_numeric($reference))
        return true;

    switch ($validationType) {
        case 'min':
            return $value >= $reference;
        case 'max':
            return $value <= $reference;
        case 'exact':
            // Para valores exatos, considera uma tolerância de 5%
            $tolerance = $reference * 0.05;
            return abs($value - $reference) <= $tolerance;
        default:
            return true;
    }
}

function getValidationType($campo, $table)
{
    // Define o tipo de validação baseado no campo e tabela
    $validationRules = [
        'bomba' => [
            'pressao_oleo_min' => 'min',
            'pressao_oleo_max' => 'max',
            'vazao_min' => 'min',
            'vazao_max' => 'max',
            'comb_pressao' => 'exact'
        ],
        'embreagem' => [
            'disco_friccao_espes' => 'min',
            'disco_separador_emp' => 'max'
        ],
        'motor' => [
            'diametro_cilindro_max' => 'max',
            'conicidade_max' => 'max',
            'ovalizacao_max' => 'max',
            'diametro_pistao_min' => 'min',
            'folga_cil_pis_max' => 'max',
            'aber_anel_1_max' => 'max',
            'aber_anel_2_max' => 'max',
            'aber_anel_1_pres_min' => 'min',
            'aber_anel_2_pres_min' => 'min',
            'larg_anel_1_min' => 'min',
            'larg_anel_2_min' => 'min',
            'dia_furo_pis_min' => 'min',
            'dia_pino_pis_min' => 'min',
            'folga_pino_pis_max' => 'max'
        ],
        'virabrequim' => [
            'folga_mancal' => 'exact',
            'folga_biela' => 'exact',
            'folga_bronzina' => 'exact',
            'folga_bronzinha' => 'exact',
            'folga_lateral_biela' => 'exact',
            'folga_lateral_eixo_min' => 'min',
            'folga_lateral_eixo_max' => 'max',
            'empenamento' => 'max',
            'diametro_moente' => 'exact',
            'diametro_munhao' => 'exact'
        ],
        'cabecote' => [
            'val_adm_limite_min' => 'min',
            'val_adm_limite_max' => 'max',
            'val_esc_limite_min' => 'min',
            'val_esc_limite_max' => 'max'
        ]
    ];

    return isset($validationRules[$table][$campo]) ? $validationRules[$table][$campo] : 'exact';
}

function formatReferenceValue($value, $table, $campo)
{
    if ($value === null || $value === '' || $value == 0 || !is_numeric($value))
        return is_string($value) ? htmlspecialchars($value) : '-';

    $validationType = getValidationType($campo, $table);
    $formattedValue = formatNumber($value);

    switch ($validationType) {
        case 'min':
            return $formattedValue . ' mm (mín)';
        case 'max':
            return $formattedValue . ' mm (máx)';
        case 'exact':
            $min = $value * 0.95;
            $max = $value * 1.05;
            return formatNumber($min) . ' - ' . formatNumber($max) . ' mm';
        default:
            return $formattedValue;
    }
}

function displayComponentMeasurements($conn, $ordem, $table, $title)
{
    $query = "SELECT * FROM $table WHERE is_reference = 0 AND ordem = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $ordem);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Buscar dados de referência
    $referenceData = getReferenceData($conn, $ordem, $table);

    if ($row = mysqli_fetch_assoc($result)) {
        $componentId = strtolower(str_replace(' ', '-', $title));
        $hasVisibleData = false;
        $tableContent = "";

        switch ($table) {
            case 'bomba':
                if (!empty($row['medicoes'])) {
                    $medicoes = json_decode($row['medicoes'], true);
                    if ($medicoes) {
                        $campos = [
                            'pressao_oleo_min' => 'Pressão Óleo Mín',
                            'pressao_oleo_max' => 'Pressão Óleo Máx',
                            'vazao_min' => 'Vazão Mín',
                            'vazao_max' => 'Vazão Máx',
                            'comb_pressao' => 'Pressão Combustível'
                        ];

                        $tableContent .= "<tr class='section-header'><td><strong>Medição</strong></td><td><strong>Referência</strong></td><td><strong>Valor Medido</strong></td></tr>";

                        foreach ($medicoes as $campo => $valor) {
                            $formattedValue = formatNumber($valor);
                            if ($formattedValue !== null) {
                                $label = isset($campos[$campo]) ? $campos[$campo] : ucfirst(str_replace('_', ' ', $campo));
                                $referenceValue = isset($referenceData[$campo]) ? $referenceData[$campo] : null;
                                $refFormatted = formatReferenceValue($referenceValue, $table, $campo);

                                // Só aplicar cor se houver referência válida
                                $colorClass = '';
                                if ($referenceValue !== null && $referenceValue !== '' && $referenceValue != 0) {
                                    $validationType = getValidationType($campo, $table);
                                    $inRange = isInRange($valor, $referenceValue, $validationType);
                                    $colorClass = $inRange ? 'in-range' : 'out-range';
                                }

                                $tableContent .= "<tr><td>$label</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue</td></tr>";
                                $hasVisibleData = true;
                            }
                        }
                    }
                }
                break;

            case 'embreagem':
                $tableContent .= "<tr class='section-header'><td><strong>Medição</strong></td><td><strong>Referência</strong></td><td><strong>Valor Medido</strong></td></tr>";

                if (!empty($row['medicoes_friccao'])) {
                    $medicoes_friccao = json_decode($row['medicoes_friccao'], true);
                    if ($medicoes_friccao) {
                        $referenceMin = isset($referenceData['disco_friccao_espes_min']) ? $referenceData['disco_friccao_espes_min'] : null;
                        $refFormatted = $referenceMin ? formatNumber($referenceMin) . ' mm (mín)' : '-';

                        foreach ($medicoes_friccao as $index => $valor) {
                            $formattedValue = formatNumber($valor);
                            if ($formattedValue !== null) {
                                $colorClass = '';
                                if ($referenceMin !== null && $referenceMin !== '' && $referenceMin != 0) {
                                    $inRange = isInRange($valor, $referenceMin, 'min');
                                    $colorClass = $inRange ? 'in-range' : 'out-range';
                                }

                                $tableContent .= "<tr><td>Disco Fricção " . ($index + 1) . "</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue mm</td></tr>";
                                $hasVisibleData = true;
                            }
                        }
                    }
                }

                if (!empty($row['medicoes_separador'])) {
                    $medicoes_separador = json_decode($row['medicoes_separador'], true);
                    if ($medicoes_separador) {
                        $referenceMax = isset($referenceData['disco_separador_emp_max']) ? $referenceData['disco_separador_emp_max'] : null;
                        $refFormatted = formatReferenceValue($referenceMax, $table, 'disco_separador_emp');

                        foreach ($medicoes_separador as $index => $valor) {
                            $formattedValue = formatNumber($valor);
                            if ($formattedValue !== null) {
                                $colorClass = '';
                                if ($referenceMax !== null && $referenceMax !== '' && $referenceMax != 0) {
                                    $inRange = isInRange($valor, $referenceMax, 'max');
                                    $colorClass = $inRange ? 'in-range' : 'out-range';
                                }

                                $tableContent .= "<tr><td>Disco Separador " . ($index + 1) . "</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue mm</td></tr>";
                                $hasVisibleData = true;
                            }
                        }
                    }
                }
                break;

            case 'motor':
                if (!empty($row['medicoes'])) {
                    $medicoes = json_decode($row['medicoes'], true);
                    if ($medicoes) {
                        $campos_motor = [
                            'curso_pistao' => 'Curso Pistão',
                            'diametro_cilindro_max' => 'Diâm. Cilindro Máx',
                            'conicidade_max' => 'Conicidade Máx',
                            'ovalizacao_max' => 'Ovalização Máx',
                            'diametro_pistao_min' => 'Diâm. Pistão Mín',
                            'folga_cil_pis_max' => 'Folga Cil/Pis Máx',
                            'aber_anel_1_max' => 'Aber. Anel 1 Máx',
                            'aber_anel_2_max' => 'Aber. Anel 2 Máx',
                            'aber_anel_1_pres_min' => 'Press. Anel 1 Mín',
                            'aber_anel_2_pres_min' => 'Press. Anel 2 Mín',
                            'larg_anel_1_min' => 'Larg. Anel 1 Mín',
                            'larg_anel_2_min' => 'Larg. Anel 2 Mín',
                            'dia_furo_pis_min' => 'Diâm. Furo Pis Mín',
                            'dia_pino_pis_min' => 'Diâm. Pino Pis Mín',
                            'folga_pino_pis_max' => 'Folga Pino/Pis Máx'
                        ];

                        // Organizar por cilindro
                        $cilindros = array_keys($medicoes);
                        sort($cilindros);

                        foreach ($cilindros as $cilindro) {
                            if (is_array($medicoes[$cilindro])) {
                                $tableContent .= "<tr class='cylinder-header'><td colspan='3'><strong>CILINDRO $cilindro</strong></td></tr>";
                                $tableContent .= "<tr class='section-header'><td><strong>Medição</strong></td><td><strong>Referência</strong></td><td><strong>Valor Medido</strong></td></tr>";

                                foreach ($medicoes[$cilindro] as $campo => $valor) {
                                    $formattedValue = formatNumber($valor);
                                    if ($formattedValue !== null) {
                                        $label = isset($campos_motor[$campo]) ? $campos_motor[$campo] : ucfirst(str_replace('_', ' ', $campo));
                                        $referenceValue = isset($referenceData[$campo]) ? $referenceData[$campo] : null;
                                        $refFormatted = formatReferenceValue($referenceValue, $table, $campo);

                                        $colorClass = '';
                                        if ($referenceValue !== null && $referenceValue !== '' && $referenceValue != 0) {
                                            $validationType = getValidationType($campo, $table);
                                            $inRange = isInRange($valor, $referenceValue, $validationType);
                                            $colorClass = $inRange ? 'in-range' : 'out-range';
                                        }

                                        $tableContent .= "<tr><td>$label</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue</td></tr>";
                                        $hasVisibleData = true;
                                    }
                                }
                            }
                        }
                    }
                }
                break;

            case 'virabrequim':
                if (!empty($row['medicoes'])) {
                    $medicoes = json_decode($row['medicoes'], true);
                    if ($medicoes) {
                        $tipoVira = isset($referenceData['tipo']) ? strtolower($referenceData['tipo']) : '';
                        $campos_virabrequim = [
                            'folga_mancal' => ($tipoVira === 'rolamento') ? 'Folga Eixo Mancal' : 'Folga Mancal',
                            'folga_biela' => ($tipoVira === 'rolamento') ? 'Folga Biela' : 'Folga Bronzina',
                            'folga_bronzina' => 'Folga Bronzina',
                            'folga_bronzinha' => 'Folga Bronzina',
                            'folga_eixo_biela' => 'Folga Eixo Biela',
                            'folga_eixo_mancal' => 'Folga Eixo Mancal',
                            'folga_lateral_biela' => 'Folga Lat. Biela',
                            'folga_lateral_eixo_min' => 'Folga Lat. Eixo Mín',
                            'folga_lateral_eixo_max' => 'Folga Lat. Eixo Máx',
                            'empenamento' => 'Empenamento',
                            'diametro_moente' => 'Diâmetro Moente',
                            'diametro_munhao' => 'Diâmetro Munhão'
                        ];

                        $tableContent .= "<tr class='section-header'><td><strong>Medição</strong></td><td><strong>Referência</strong></td><td><strong>Valor Medido</strong></td></tr>";

                        foreach ($medicoes as $campo => $valor) {
                            if (is_array($valor))
                                continue;

                            // Ocultar campos conforme o tipo de virabrequim (Whitelist para Rolamento)
                            if ($tipoVira === 'rolamento' && !in_array($campo, ['folga_mancal', 'folga_biela', 'folga_bronzina', 'folga_eixo_biela', 'folga_lateral_eixo_min', 'folga_lateral_eixo_max', 'empenamento'])) {
                                continue;
                            }
                            // Ocultar campos que não devem aparecer no relatório
                            if (in_array($campo, ['qtd_cilindros', 'qtd_munhoes', 'diametro_munhao', 'diametro_munhoes'])) {
                                continue;
                            }

                            // Ocultar lateral biela para bronzina apenas se não houver valor
                            if ($tipoVira === 'bronzina' && $campo === 'folga_lateral_biela' && ($valor === null || $valor === '')) {
                                continue;
                            }

                            $formattedValue = formatNumber($valor);
                            if ($formattedValue !== null) {
                                $label = isset($campos_virabrequim[$campo]) ? $campos_virabrequim[$campo] : ucfirst(str_replace('_', ' ', $campo));
                                $referenceValue = isset($referenceData[$campo]) ? $referenceData[$campo] : null;
                                $refFormatted = formatReferenceValue($referenceValue, $table, $campo);

                                $colorClass = '';
                                if ($referenceValue !== null && $referenceValue !== '' && $referenceValue != 0) {
                                    $validationType = getValidationType($campo, $table);
                                    $inRange = isInRange($valor, $referenceValue, $validationType);
                                    $colorClass = $inRange ? 'in-range' : 'out-range';
                                }

                                $tableContent .= "<tr><td>$label</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue mm</td></tr>";
                                $hasVisibleData = true;
                            }
                        }

                        // Processar Moentes individuais
                        if ($tipoVira !== 'rolamento' && isset($medicoes['diametro_moente']) && is_array($medicoes['diametro_moente'])) {
                            $refMoente = isset($referenceData['diametro_moente']) ? $referenceData['diametro_moente'] : null;
                            $refFormatted = formatReferenceValue($refMoente, $table, 'diametro_moente');

                            foreach ($medicoes['diametro_moente'] as $i => $v) {
                                $formattedValue = formatNumber($v);
                                if ($formattedValue !== null) {
                                    $colorClass = '';
                                    if ($refMoente !== null && $refMoente !== '' && $refMoente != 0) {
                                        $inRange = isInRange($v, $refMoente, 'exact');
                                        $colorClass = $inRange ? 'in-range' : 'out-range';
                                    }
                                    $tableContent .= "<tr><td>Diâmetro Moente $i</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue mm</td></tr>";
                                    $hasVisibleData = true;
                                }
                            }
                        }

                        // Processar Munhões individuais
                        if ($tipoVira !== 'rolamento' && isset($medicoes['diametro_munhao']) && is_array($medicoes['diametro_munhao'])) {
                            $refMunhao = isset($referenceData['diametro_munhao']) ? $referenceData['diametro_munhao'] : null;
                            $refFormatted = formatReferenceValue($refMunhao, $table, 'diametro_munhao');

                            foreach ($medicoes['diametro_munhao'] as $i => $v) {
                                $formattedValue = formatNumber($v);
                                if ($formattedValue !== null) {
                                    $colorClass = '';
                                    if ($refMunhao !== null && $refMunhao !== '' && $refMunhao != 0) {
                                        $inRange = isInRange($v, $refMunhao, 'exact');
                                        $colorClass = $inRange ? 'in-range' : 'out-range';
                                    }
                                    $tableContent .= "<tr><td>Diâmetro Munhão $i</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue mm</td></tr>";
                                    $hasVisibleData = true;
                                }
                            }
                        }

                        // Processar Folga Mancal individual
                        if (isset($medicoes['folga_mancal']) && is_array($medicoes['folga_mancal'])) {
                            $refMancal = isset($referenceData['folga_mancal']) ? $referenceData['folga_mancal'] : null;
                            $refFormatted = formatReferenceValue($refMancal, $table, 'folga_mancal');

                            foreach ($medicoes['folga_mancal'] as $i => $v) {
                                $formattedValue = formatNumber($v);
                                if ($formattedValue !== null) {
                                    $colorClass = '';
                                    if ($refMancal !== null && $refMancal !== '' && $refMancal != 0) {
                                        $inRange = isInRange($v, $refMancal, 'exact');
                                        $colorClass = $inRange ? 'in-range' : 'out-range';
                                    }
                                    $tableContent .= "<tr><td>Folga Mancal $i</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue mm</td></tr>";
                                    $hasVisibleData = true;
                                }
                            }
                        }

                        // Processar Folga Biela individual (suporta folga_biela, folga_bronzina ou folga_bronzinha)
                        $bielaKey = isset($medicoes['folga_biela']) ? 'folga_biela' : (isset($medicoes['folga_bronzina']) ? 'folga_bronzina' : (isset($medicoes['folga_bronzinha']) ? 'folga_bronzinha' : null));
                        if ($bielaKey && is_array($medicoes[$bielaKey])) {
                            $refBiela = isset($referenceData['folga_biela']) ? $referenceData['folga_biela'] : (isset($referenceData['folga_bronzina']) ? $referenceData['folga_bronzina'] : null);
                            $refFormatted = formatReferenceValue($refBiela, $table, 'folga_biela');
                            $labelBase = ($tipoVira === 'rolamento') ? 'Folga Biela' : 'Folga Bronzina';

                            foreach ($medicoes[$bielaKey] as $i => $v) {
                                $formattedValue = formatNumber($v);
                                if ($formattedValue !== null) {
                                    $colorClass = '';
                                    if ($refBiela !== null && $refBiela !== '' && $refBiela != 0) {
                                        $inRange = isInRange($v, $refBiela, 'exact');
                                        $colorClass = $inRange ? 'in-range' : 'out-range';
                                    }
                                    $tableContent .= "<tr><td>$labelBase $i</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$formattedValue mm</td></tr>";
                                    $hasVisibleData = true;
                                }
                            }
                        }
                    }

                    // Se o registro de medições existe, garantimos que a seção apareça se tivermos algum conteúdo
                    if ($tableContent !== "") {
                        $hasVisibleData = true;
                    }
                }
                break;

            case 'cabecote':
                if (!empty($row['medicoes'])) {
                    $medicoes = json_decode($row['medicoes'], true);
                    if ($medicoes) {
                        // Organizar por cilindro e válvula
                        $cilindrosData = [];

                        // Primeiro, coletar todas as medições organizadas por cilindro e tipo de válvula
                        $grupos = [
                            'adm_folga' => 'Folga Válv. Adm',
                            'esc_folga' => 'Folga Válv. Esc',
                            'adm_pastilha' => 'Pastilha Válv. Adm',
                            'esc_pastilha' => 'Pastilha Válv. Esc'
                        ];

                        foreach ($grupos as $prefixo => $titulo_grupo) {
                            foreach ($medicoes as $tipo_medicao => $dados) {
                                if (strpos($tipo_medicao, $prefixo) !== false && is_array($dados)) {
                                    $lado = str_replace($prefixo . '_', '', $tipo_medicao);
                                    foreach ($dados as $cilindro => $valor) {
                                        if (!isset($cilindrosData[$cilindro])) {
                                            $cilindrosData[$cilindro] = [];
                                        }
                                        $cilindrosData[$cilindro][$prefixo][$lado] = $valor;
                                    }
                                }
                            }
                        }

                        // Exibir organizadamente por cilindro
                        ksort($cilindrosData);
                        foreach ($cilindrosData as $cilindro => $dados) {
                            $hasValidData = false;
                            $cylinderContent = "";

                            // Organizar por tipo de válvula (adm, esc) e depois por lado
                            $valvulas = ['adm', 'esc'];
                            $lados = [];

                            // Coletar todos os lados disponíveis
                            foreach ($dados as $tipo => $ladosData) {
                                foreach ($ladosData as $lado => $valor) {
                                    if (!in_array($lado, $lados)) {
                                        $lados[] = $lado;
                                    }
                                }
                            }
                            sort($lados);

                            foreach ($valvulas as $valvula) {
                                foreach ($lados as $lado) {
                                    // Exibir folga primeiro
                                    $folgaKey = $valvula . '_folga';
                                    $pastilhaKey = $valvula . '_pastilha';

                                    $folgaValue = isset($dados[$folgaKey][$lado]) ? $dados[$folgaKey][$lado] : null;
                                    $pastilhaValue = isset($dados[$pastilhaKey][$lado]) ? $dados[$pastilhaKey][$lado] : null;

                                    // Folga
                                    $formattedFolga = formatNumber($folgaValue);
                                    if ($formattedFolga !== null || $pastilhaValue !== null) { // Mostrar se tem folga OU pastilha
                                        $hasValidData = true;

                                        $referenceMin = null;
                                        $referenceMax = null;
                                        if ($valvula === 'adm') {
                                            $referenceMin = isset($referenceData['val_adm_limite_min']) ? $referenceData['val_adm_limite_min'] : null;
                                            $referenceMax = isset($referenceData['val_adm_limite_max']) ? $referenceData['val_adm_limite_max'] : null;
                                        } else {
                                            $referenceMin = isset($referenceData['val_esc_limite_min']) ? $referenceData['val_esc_limite_min'] : null;
                                            $referenceMax = isset($referenceData['val_esc_limite_max']) ? $referenceData['val_esc_limite_max'] : null;
                                        }

                                        $refFormatted = '-';
                                        if ($referenceMin || $referenceMax) {
                                            $refParts = [];
                                            if ($referenceMin)
                                                $refParts[] = formatNumber($referenceMin);
                                            if ($referenceMax)
                                                $refParts[] = formatNumber($referenceMax);
                                            $refFormatted = implode('-', $refParts) . ' mm';
                                        }

                                        // Folga
                                        $colorClass = '';
                                        if ($formattedFolga !== null && (($referenceMin !== null && $referenceMin !== '' && $referenceMin != 0) || ($referenceMax !== null && $referenceMax !== '' && $referenceMax != 0))) {
                                            $inRange = true;
                                            if ($referenceMin !== null && $referenceMin !== '' && $referenceMin != 0 && $folgaValue < $referenceMin)
                                                $inRange = false;
                                            if ($referenceMax !== null && $referenceMax !== '' && $referenceMax != 0 && $folgaValue > $referenceMax)
                                                $inRange = false;
                                            $colorClass = $inRange ? 'in-range' : 'out-range';
                                        }

                                        $folgaDisplay = $formattedFolga !== null ? $formattedFolga . ' mm' : '-';
                                        $measurementName = "Folga Válv. " . strtoupper($valvula) . " ($lado)";
                                        $cylinderContent .= "<tr><td>$measurementName</td><td class='reference-value'>$refFormatted</td><td class='$colorClass'>$folgaDisplay</td></tr>";

                                        // Pastilha (sempre sem cor)
                                        $pastilhaDisplay = formatPastilha($pastilhaValue);
                                        $pastilhaDisplay = $pastilhaDisplay !== null ? $pastilhaDisplay . ' mm' : '-';
                                        $pastilhaMeasurementName = "Pastilha Válv. " . strtoupper($valvula) . " ($lado)";
                                        $cylinderContent .= "<tr><td>$pastilhaMeasurementName</td><td class='reference-value'>-</td><td>$pastilhaDisplay</td></tr>";
                                    }
                                }
                            }

                            // Só adicionar o cilindro se houver dados válidos
                            if ($hasValidData) {
                                $tableContent .= "<tr class='cylinder-header'><td colspan='3'><strong>CILINDRO $cilindro</strong></td></tr>";
                                $tableContent .= "<tr class='section-header'><td><strong>Medição</strong></td><td><strong>Referência</strong></td><td><strong>Valor Medido</strong></td></tr>";
                                $tableContent .= $cylinderContent;
                                $hasVisibleData = true;
                            }
                        }
                    }
                }
                break;
        }

        if ($hasVisibleData) {
            echo "<div class='component-section'>";
            echo "<div class='component-header' onclick='toggleComponent(\"$componentId\")'>";
            echo "<h4>$title</h4>";
            echo "<span class='toggle-icon' id='icon-$componentId'>▼</span>";
            echo "</div>";
            echo "<div class='table-container' id='content-$componentId'>";
            echo "<table class='measurements-table'>";
            echo $tableContent;
            echo "</table>";
            echo "</div>";
            echo "</div>";
            return true;
        }
    }
    return false;
}

echo "<div class='report-container'>";
echo "<div class='report-header'>";
echo "<h2>Relatório de Medições - OS: $ordem</h2>";
echo "<div class='controls'>";
echo "<button onclick='expandAll()' class='btn'>Expandir</button>";
echo "<button onclick='collapseAll()' class='btn'>Recolher</button>";
echo "<button onclick='printTable()' class='btn'>Imprimir</button>";
echo "</div>";
echo "</div>";

$hasData = false;
$components = [
    'embreagem' => 'Embreagem',
    'bomba' => 'Bomba',
    'motor' => 'Motor',
    'virabrequim' => 'Virabrequim',
    'cabecote' => 'Cabeçote'
];

foreach ($components as $table => $title) {
    if (displayComponentMeasurements($conn, $ordem, $table, $title)) {
        $hasData = true;
    }
}

if (!$hasData) {
    echo "<div class='no-data'>Nenhuma medição encontrada para esta ordem de serviço.</div>";
}

echo "</div>";
?>

<script>
    function toggleComponent(componentId) {
        const content = document.getElementById('content-' + componentId);
        const icon = document.getElementById('icon-' + componentId);

        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.textContent = '▼';
        } else {
            content.style.display = 'none';
            icon.textContent = '▶';
        }
    }

    function expandAll() {
        document.querySelectorAll('[id^="content-"]').forEach(content => {
            content.style.display = 'block';
        });
        document.querySelectorAll('[id^="icon-"]').forEach(icon => {
            icon.textContent = '▼';
        });
    }

    function collapseAll() {
        document.querySelectorAll('[id^="content-"]').forEach(content => {
            content.style.display = 'none';
        });
        document.querySelectorAll('[id^="icon-"]').forEach(icon => {
            icon.textContent = '▶';
        });
    }

    function printTable() {
        // Expandir todas as seções antes de imprimir
        expandAll();

        // Criar uma nova janela para impressão
        var printWindow = window.open('', '_blank');
        var reportContainer = document.querySelector('.report-container');

        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Relatório de Medições - OS: <?php echo $ordem; ?></title>
            <style>
                @page {
                    size: A4 landscape;
                    margin: 10mm;
                }
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0;
                    padding: 0;
                    color: #000;
                    background: #fff;
                    font-size: 10px;
                }
                .report-container {
                    max-width: 100%;
                    margin: 0;
                    padding: 0;
                }
                .report-header h2 {
                    color: #000;
                    text-align: center;
                    margin-bottom: 15px;
                    font-size: 16px;
                }
                .controls { display: none; }
                .component-section {
                    margin-bottom: 8px;
                    border: 1px solid #ccc;
                    page-break-inside: avoid;
                    break-inside: avoid;
                }
                .component-header {
                    background-color: #f5f5f5;
                    padding: 5px 8px;
                    border-bottom: 1px solid #ccc;
                }
                .component-header h4 {
                    margin: 0;
                    color: #000;
                    font-size: 12px;
                }
                .toggle-icon { display: none; }
                .measurements-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 9px;
                    margin: 0;
                }
                .measurements-table td {
                    padding: 2px 4px;
                    border: 1px solid #ccc;
                    color: #000;
                    line-height: 1.2;
                }
                .section-header td {
                    background-color: #e0e0e0 !important;
                    font-weight: bold;
                    text-align: center;
                    font-size: 9px;
                    padding: 3px 4px;
                }
                .cylinder-header td {
                    background-color: #d0d0d0 !important;
                    font-weight: bold;
                    text-align: center;
                    font-size: 9px;
                    padding: 3px 4px;
                }
                .reference-value {
                    font-weight: bold;
                    font-style: italic;
                }
                .in-range {
                    background-color: #e8f5e8 !important;
                }
                .out-range {
                    background-color: #ffe8e8 !important;
                }
                .no-data {
                    text-align: center;
                    padding: 8px;
                    font-style: italic;
                    font-size: 9px;
                }
                /* Forçar tudo em uma página */
                * {
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }
                .component-section {
                    page-break-after: avoid !important;
                    break-after: avoid !important;
                }
                @media print {
                    body { 
                        margin: 0;
                        transform: scale(0.8);
                        transform-origin: top left;
                    }
                    .report-container {
                        transform: scale(0.85);
                        transform-origin: top left;
                        width: 117%;
                    }
                }
            </style>
        </head>
        <body>
    `);

        printWindow.document.write(reportContainer.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();

        // Aguardar o carregamento e imprimir
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', function () {
        collapseAll();
    });
</script>

<style>
    .report-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 15px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #24262e;
        color: #e0e0e0;
        font-size: 13px;
        line-height: 1.3;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3a3d47;
    }

    .report-header h2 {
        margin: 0;
        font-size: 18px;
        color: #ffffff;
    }

    .controls {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 6px 12px;
        background-color: #e44c5c;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 11px;
        transition: background-color 0.2s;
    }

    .btn:hover {
        background-color: #d63447;
    }

    .component-section {
        margin-bottom: 12px;
        border: 1px solid #3a3d47;
        border-radius: 4px;
        overflow: hidden;
        background-color: #181a20;
    }

    .component-header {
        background-color: #e44c5c;
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.3s;
    }

    .component-header:hover {
        background-color: #d63447;
    }

    .component-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #ffffff;
    }

    .toggle-icon {
        font-size: 12px;
        color: #ffffff;
    }

    .table-container {
        background-color: #181a20;
    }

    .measurements-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        background-color: #181a20;
    }

    .measurements-table td {
        padding: 4px 8px;
        border-bottom: 1px solid #3a3d47;
        vertical-align: top;
        color: #e0e0e0;
    }

    .measurements-table td:first-child {
        font-weight: 500;
        width: 40%;
        color: #f0f0f0;
    }

    .measurements-table td:nth-child(2) {
        width: 30%;
        text-align: center;
    }

    .measurements-table td:last-child {
        text-align: right;
        font-weight: 600;
        width: 30%;
    }

    /* Cabeçalhos de seção */
    .section-header td {
        background-color: #3a3d47 !important;
        color: #ffffff !important;
        font-weight: bold;
        text-align: center;
        padding: 8px !important;
    }

    /* Cabeçalhos de cilindro */
    .cylinder-header td {
        background-color: #e44c5c !important;
        color: #ffffff !important;
        font-weight: bold;
        text-align: center;
        padding: 10px !important;
        font-size: 13px;
    }

    /* Valores de referência */
    .reference-value {
        color: #87CEEB !important;
        font-weight: bold;
        font-style: italic;
    }

    /* Cores para medições dentro e fora do range */
    .measurements-table td.in-range {
        color: #4CAF50 !important;
        background-color: rgba(76, 175, 80, 0.1);
    }

    .measurements-table td.out-range {
        color: #f44336 !important;
        background-color: rgba(244, 67, 54, 0.1);
    }

    .measurements-table tr:nth-child(even) {
        background-color: #1f2129;
    }

    .measurements-table tr:hover {
        background-color: #2a2d35;
    }

    .no-data {
        text-align: center;
        padding: 20px;
        color: #b0b0b0;
        font-style: italic;
        background-color: #181a20;
        border: 1px solid #3a3d47;
        border-radius: 4px;
    }

    .error-msg {
        background-color: #d32f2f;
        color: white;
        padding: 10px;
        border-radius: 4px;
        margin: 10px 0;
        text-align: center;
    }

    @media print {
        .controls {
            display: none;
        }

        .component-header {
            cursor: default;
        }

        .table-container {
            display: block !important;
        }

        .report-container {
            max-width: none;
            padding: 0;
            background: white;
            color: black;
        }

        .component-section {
            break-inside: avoid;
            margin-bottom: 8px;
            background: white;
            border-color: #ccc;
        }

        .component-header {
            background: #f5f5f5 !important;
            color: black !important;
        }

        .component-header h4 {
            color: black !important;
        }

        .measurements-table {
            background: white;
        }

        .measurements-table td {
            color: black;
            border-color: #ccc;
        }

        .measurements-table td.in-range {
            color: #2E7D32 !important;
            background-color: rgba(76, 175, 80, 0.2);
        }

        .measurements-table td.out-range {
            color: #C62828 !important;
            background-color: rgba(244, 67, 54, 0.2);
        }

        .measurements-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .report-header h2 {
            color: black;
        }
    }

    @media (max-width: 768px) {
        .report-header {
            flex-direction: column;
            gap: 10px;
        }

        .measurements-table td {
            padding: 3px 6px;
            font-size: 11px;
        }

        .measurements-table td:first-child {
            width: 65%;
        }

        .measurements-table td:last-child {
            width: 35%;
        }

        .report-container {
            padding: 10px;
        }
    }
</style>