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
    $query = "SELECT * FROM $table WHERE is_reference = 0 AND ordem = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $ordem);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $componentId = strtolower(str_replace(' ', '-', $title));
        echo "<div class='component-section'>";
        echo "<div class='component-header' onclick='toggleComponent(\"$componentId\")'>";
        echo "<h3>$title</h3>";
        echo "<span class='toggle-icon' id='icon-$componentId'>▼</span>";
        echo "</div>";
        echo "<div class='table-container' id='content-$componentId'>";
        echo "<table class='measurements-table'>";
        echo "<thead><tr><th>Campo</th><th>Valor</th></tr></thead>";
        echo "<tbody>";
        
        // Campos específicos por componente
        switch($table) {
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
                        foreach($medicoes as $campo => $valor) {
                            $label = isset($campos[$campo]) ? $campos[$campo] : ucfirst(str_replace('_', ' ', $campo));
                            echo "<tr><td>$label</td><td>" . formatNumber($valor) . "</td></tr>";
                        }
                    }
                }
                break;
                
            case 'embreagem':
                if (!empty($row['medicoes_friccao'])) {
                    $medicoes_friccao = json_decode($row['medicoes_friccao'], true);
                    if ($medicoes_friccao) {
                        $subSectionId = $componentId . '-friccao';
                        echo "<tr><td colspan='2' class='subsection-header' onclick='toggleSubsection(\"$subSectionId\")'>
                                <span>Disco de Fricção - Espessura</span>
                                <span class='toggle-icon-small' id='icon-$subSectionId'>▼</span>
                              </td></tr>";
                        echo "<tbody id='content-$subSectionId' class='subsection-content'>";
                        foreach($medicoes_friccao as $index => $valor) {
                            echo "<tr><td>Disco " . ($index + 1) . "</td><td>" . formatNumber($valor) . "</td></tr>";
                        }
                        echo "</tbody>";
                    }
                }
                if (!empty($row['medicoes_separador'])) {
                    $medicoes_separador = json_decode($row['medicoes_separador'], true);
                    if ($medicoes_separador) {
                        $subSectionId = $componentId . '-separador';
                        echo "<tr><td colspan='2' class='subsection-header' onclick='toggleSubsection(\"$subSectionId\")'>
                                <span>Disco Separador - Empenamento</span>
                                <span class='toggle-icon-small' id='icon-$subSectionId'>▼</span>
                              </td></tr>";
                        echo "<tbody id='content-$subSectionId' class='subsection-content'>";
                        foreach($medicoes_separador as $index => $valor) {
                            echo "<tr><td>Disco " . ($index + 1) . "</td><td>" . formatNumber($valor) . "</td></tr>";
                        }
                        echo "</tbody>";
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
                                $subSectionId = $componentId . '-cilindro-' . $cilindro;
                                echo "<tr><td colspan='2' class='subsection-header' onclick='toggleSubsection(\"$subSectionId\")'>
                                        <span>Cilindro $cilindro</span>
                                        <span class='toggle-icon-small' id='icon-$subSectionId'>▼</span>
                                      </td></tr>";
                                echo "<tbody id='content-$subSectionId' class='subsection-content'>";
                                foreach($dados_cilindro as $campo => $valor) {
                                    $label = isset($campos_motor[$campo]) ? $campos_motor[$campo] : ucfirst(str_replace('_', ' ', $campo));
                                    echo "<tr><td>$label</td><td>" . formatNumber($valor) . "</td></tr>";
                                }
                                echo "</tbody>";
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
                                
                                $subSectionId = $componentId . '-' . $tipo_medicao;
                                echo "<tr><td colspan='2' class='subsection-header' onclick='toggleSubsection(\"$subSectionId\")'>
                                        <span>$label_tipo</span>
                                        <span class='toggle-icon-small' id='icon-$subSectionId'>▼</span>
                                      </td></tr>";
                                echo "<tbody id='content-$subSectionId' class='subsection-content'>";
                                foreach($dados as $cilindro => $valor) {
                                    echo "<tr><td>Cilindro $cilindro</td><td>" . formatNumber($valor) . "</td></tr>";
                                }
                                echo "</tbody>";
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
echo "<div class='header-controls'>";
echo "<h1>Medições da Ordem de Serviço: $ordem</h1>";
echo "<div class='control-buttons'>";
echo "<button onclick='expandAll()' class='btn-control'>Expandir Tudo</button>";
echo "<button onclick='collapseAll()' class='btn-control'>Recolher Tudo</button>";
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

function toggleSubsection(sectionId) {
    const content = document.getElementById('content-' + sectionId);
    const icon = document.getElementById('icon-' + sectionId);
    
    if (content.style.display === 'none') {
        content.style.display = 'table-row-group';
        icon.textContent = '▼';
    } else {
        content.style.display = 'none';
        icon.textContent = '▶';
    }
}

function expandAll() {
    const allContents = document.querySelectorAll('[id^="content-"]');
    const allIcons = document.querySelectorAll('[id^="icon-"]');
    
    allContents.forEach(content => {
        if (content.classList.contains('subsection-content')) {
            content.style.display = 'table-row-group';
        } else {
            content.style.display = 'block';
        }
    });
    
    allIcons.forEach(icon => {
        icon.textContent = '▼';
    });
}

function collapseAll() {
    const allContents = document.querySelectorAll('[id^="content-"]');
    const allIcons = document.querySelectorAll('[id^="icon-"]');
    
    allContents.forEach(content => {
        content.style.display = 'none';
    });
    
    allIcons.forEach(icon => {
        icon.textContent = '▶';
    });
}

// Inicializar com todas as seções recolhidas
document.addEventListener('DOMContentLoaded', function() {
    collapseAll();
});
</script>

<style>
.measurements-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 15px;
    font-family: Arial, sans-serif;
    background-color: #24262e;
    color: #e0e0e0;
}

.header-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #3a3d47;
}

.header-controls h1 {
    margin: 0;
    font-size: 1.5em;
}

.control-buttons {
    display: flex;
    gap: 10px;
}

.btn-control {
    padding: 8px 15px;
    background-color: #e44c5c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
    transition: background-color 0.3s;
}

.btn-control:hover {
    background-color: #d63447;
}

.component-section {
    margin-bottom: 15px;
    border: 1px solid #3a3d47;
    border-radius: 6px;
    overflow: hidden;
    background-color: #181a20;
}

.component-header {
    background-color: #e44c5c;
    padding: 10px 15px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
    transition: background-color 0.3s;
}

.component-header:hover {
    background-color: #d63447;
}

.component-header h3 {
    margin: 0;
    color: #ffffff;
    font-weight: bold;
    font-size: 1.1em;
}

.toggle-icon {
    font-size: 1.2em;
    color: #ffffff;
    transition: transform 0.3s;
}

.table-container {
    overflow-x: auto;
    background-color: #181a20;
}

.measurements-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    background-color: #181a20;
    font-size: 0.9em;
}

.measurements-table th,
.measurements-table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #3a3d47;
    color: #e0e0e0;
}

.measurements-table th {
    background-color: #2a2d35;
    font-weight: bold;
    color: #ffffff;
    font-size: 0.85em;
}

.measurements-table tr:hover {
    background-color: #2a2d35;
}

.subsection-header {
    background-color: #3a3d47 !important;
    font-weight: bold;
    color: #ffffff !important;
    cursor: pointer;
    user-select: none;
    position: relative;
    transition: background-color 0.3s;
}

.subsection-header:hover {
    background-color: #4a4d57 !important;
}

.subsection-header span:first-child {
    display: inline-block;
    width: calc(100% - 20px);
}

.toggle-icon-small {
    font-size: 0.9em;
    color: #ffffff;
    float: right;
    transition: transform 0.3s;
}

.subsection-content {
    display: table-row-group;
}

.subsection-content tr td:first-child {
    padding-left: 25px;
    font-style: italic;
    color: #c0c0c0;
}

.no-data-msg {
    text-align: center;
    padding: 30px;
    color: #b0b0b0;
    font-style: italic;
    background-color: #181a20;
    border-radius: 6px;
    border: 1px solid #3a3d47;
}

@media (max-width: 768px) {
    .measurements-container {
        padding: 10px;
    }
    
    .header-controls {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .header-controls h1 {
        text-align: center;
        font-size: 1.3em;
    }
    
    .control-buttons {
        justify-content: center;
    }
    
    .measurements-table th,
    .measurements-table td {
        padding: 6px 8px;
        font-size: 0.8em;
    }
    
    .component-header {
        padding: 8px 12px;
    }
    
    .component-header h3 {
        font-size: 1em;
    }
}
</style>