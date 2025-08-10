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

function formatNumber($value) {
    if ($value === null || $value === '') return '-';
    return is_numeric($value) ? number_format($value, 2, ',', '.') : htmlspecialchars($value);
}

function displayComponentMeasurements($conn, $ordem, $table, $title) {
    // Removido o campo created_at que não existe
    $query = "SELECT * FROM $table WHERE is_reference = 0 AND ordem = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $ordem);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='component-section'>";
        echo "<h3>$title</h3>";
        echo "<div class='table-container'>";
        echo "<table class='measurements-table'>";
        echo "<thead><tr><th>Campo</th><th>Valor Medido</th></tr></thead>";
        echo "<tbody>";
        
        // Campos específicos por componente
        switch($table) {
            case 'bomba':
                // Para bomba, as medições estão no campo JSON 'medicoes'
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
                        foreach($medicoes as $campo => $valor) {
                            $label = isset($campos[$campo]) ? $campos[$campo] : ucfirst(str_replace('_', ' ', $campo));
                            echo "<tr><td>$label</td><td>" . formatNumber($valor) . "</td></tr>";
                        }
                    }
                }
                break;
                
            case 'embreagem':
                // Medições de fricção
                if (!empty($row['medicoes_friccao'])) {
                    $medicoes_friccao = json_decode($row['medicoes_friccao'], true);
                    if ($medicoes_friccao) {
                        echo "<tr><td colspan='2' class='section-header'>Disco de Fricção - Espessura</td></tr>";
                        foreach($medicoes_friccao as $index => $valor) {
                            echo "<tr><td>Disco " . ($index + 1) . "</td><td>" . formatNumber($valor) . "</td></tr>";
                        }
                    }
                }
                // Medições de separador
                if (!empty($row['medicoes_separador'])) {
                    $medicoes_separador = json_decode($row['medicoes_separador'], true);
                    if ($medicoes_separador) {
                        echo "<tr><td colspan='2' class='section-header'>Disco Separador - Empenamento</td></tr>";
                        foreach($medicoes_separador as $index => $valor) {
                            echo "<tr><td>Disco " . ($index + 1) . "</td><td>" . formatNumber($valor) . "</td></tr>";
                        }
                    }
                }
                break;
                
            case 'motor':
                if (!empty($row['medicoes'])) {
                    $medicoes = json_decode($row['medicoes'], true);
                    if ($medicoes) {
                        $campos_motor = [
                            'curso_pistao' => 'Curso do Pistão',
                            'diametro_cilindro_max' => 'Diâmetro Cilindro Máx',
                            'conicidade_max' => 'Conicidade Máx',
                            'ovalizacao_max' => 'Ovalização Máx',
                            'diametro_pistao_min' => 'Diâmetro Pistão Mín',
                            'folga_cil_pis_max' => 'Folga Cilindro/Pistão Máx',
                            'aber_anel_1_max' => 'Abertura Anel 1 Máx',
                            'aber_anel_2_max' => 'Abertura Anel 2 Máx',
                            'aber_anel_1_pres_min' => 'Pressão Anel 1 Mín',
                            'aber_anel_2_pres_min' => 'Pressão Anel 2 Mín',
                            'larg_anel_1_min' => 'Largura Anel 1 Mín',
                            'larg_anel_2_min' => 'Largura Anel 2 Mín',
                            'dia_furo_pis_min' => 'Diâmetro Furo Pistão Mín',
                            'dia_pino_pis_min' => 'Diâmetro Pino Pistão Mín',
                            'folga_pino_pis_max' => 'Folga Pino/Pistão Máx'
                        ];
                        
                        foreach($medicoes as $cilindro => $dados_cilindro) {
                            if (is_array($dados_cilindro)) {
                                echo "<tr><td colspan='2' class='section-header'>Cilindro $cilindro</td></tr>";
                                foreach($dados_cilindro as $campo => $valor) {
                                    $label = isset($campos_motor[$campo]) ? $campos_motor[$campo] : ucfirst(str_replace('_', ' ', $campo));
                                    echo "<tr><td>$label</td><td>" . formatNumber($valor) . "</td></tr>";
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
                        $campos_virabrequim = [
                            'folga_mancal' => 'Folga Mancal',
                            'folga_bronzina' => 'Folga Bronzina',
                            'folga_lateral_biela' => 'Folga Lateral Biela',
                            'folga_lateral_eixo_min' => 'Folga Lateral Eixo Mín',
                            'folga_lateral_eixo_max' => 'Folga Lateral Eixo Máx',
                            'empenamento' => 'Empenamento'
                        ];
                        
                        foreach($medicoes as $campo => $valor) {
                            $label = isset($campos_virabrequim[$campo]) ? $campos_virabrequim[$campo] : ucfirst(str_replace('_', ' ', $campo));
                            echo "<tr><td>$label</td><td>" . formatNumber($valor) . "</td></tr>";
                        }
                    }
                }
                break;
                
            case 'cabecote':
                if (!empty($row['medicoes'])) {
                    $medicoes = json_decode($row['medicoes'], true);
                    if ($medicoes) {
                        foreach($medicoes as $tipo_medicao => $dados) {
                            if (is_array($dados)) {
                                $label_tipo = '';
                                if (strpos($tipo_medicao, 'adm_folga') !== false) {
                                    $lado = str_replace('adm_folga_', '', $tipo_medicao);
                                    $label_tipo = "Folga Válvula Admissão " . ucfirst($lado);
                                } elseif (strpos($tipo_medicao, 'esc_folga') !== false) {
                                    $lado = str_replace('esc_folga_', '', $tipo_medicao);
                                    $label_tipo = "Folga Válvula Escape " . ucfirst($lado);
                                } elseif (strpos($tipo_medicao, 'adm_pastilha') !== false) {
                                    $lado = str_replace('adm_pastilha_', '', $tipo_medicao);
                                    $label_tipo = "Pastilha Válvula Admissão " . ucfirst($lado);
                                } elseif (strpos($tipo_medicao, 'esc_pastilha') !== false) {
                                    $lado = str_replace('esc_pastilha_', '', $tipo_medicao);
                                    $label_tipo = "Pastilha Válvula Escape " . ucfirst($lado);
                                } else {
                                    $label_tipo = ucfirst(str_replace('_', ' ', $tipo_medicao));
                                }
                                
                                echo "<tr><td colspan='2' class='section-header'>$label_tipo</td></tr>";
                                foreach($dados as $cilindro => $valor) {
                                    echo "<tr><td>Cilindro $cilindro</td><td>" . formatNumber($valor) . "</td></tr>";
                                }
                            }
                        }
                    }
                }
                break;
        }
        
        echo "</tbody></table>";
        echo "</div>";
        echo "</div>";
        return true;
    }
    return false;
}

echo "<div class='measurements-container'>";
echo "<h1>Medições da Ordem de Serviço: $ordem</h1>";

$hasData = false;

// Verificar e exibir medições de cada componente
$components = [
    'embreagem' => 'Embreagem',
    'bomba' => 'Bomba',
    'motor' => 'Motor', 
    'virabrequim' => 'Virabrequim',
    'cabecote' => 'Cabeçote'
];

foreach($components as $table => $title) {
    if (displayComponentMeasurements($conn, $ordem, $table, $title)) {
        $hasData = true;
    }
}

if (!$hasData) {
    echo "<div class='no-data-msg'>Nenhuma medição encontrada para esta ordem de serviço.</div>";
}

echo "</div>";
?>

<style>
.measurements-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

.component-section {
    margin-bottom: 30px;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.component-section h3 {
    background-color: #f8f9fa;
    margin: 0;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    color: #333;
}

.table-container {
    overflow-x: auto;
}

.measurements-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.measurements-table th,
.measurements-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.measurements-table th {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.measurements-table tr:hover {
    background-color: #f5f5f5;
}

.section-header {
    background-color: #e3f2fd !important;
    font-weight: bold;
    color: #1976d2 !important;
}

.no-data-msg {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #ddd;
}

@media (max-width: 768px) {
    .measurements-container {
        padding: 10px;
    }
    
    .measurements-table th,
    .measurements-table td {
        padding: 8px 10px;
        font-size: 0.9em;
    }
}
</style>