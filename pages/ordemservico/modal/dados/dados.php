<?php
// Verificação inicial do parâmetro ordem
if (!isset($_GET['ordem'])) {
    echo "<div class='error-msg error-msg--center'> <i class='fas fa-exclamation-triangle'></i> Erro: Parâmetro 'ordem' não foi especificado na URL.</div>";
    exit;
}

$ordem = $_GET['ordem'];

if (!isset($conn) || !$conn) {
    echo "<div class='error-msg error-msg--center'> <i class='fas fa-exclamation-triangle'></i> Erro: Conexão com o banco de dados não estabelecida.</div>";
    exit;
}

$checkQuery = "SELECT COUNT(*) as count FROM cabecote WHERE is_reference = 1 AND ordem = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($checkStmt, "s", $ordem);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$checkRow = mysqli_fetch_assoc($checkResult);

require_once 'dados_util.php';
require_once 'dados_cabecote.php';
require_once 'dados_embreagem.php';
require_once 'dados_bomba.php';
require_once 'dados_motor.php';
require_once 'dados_virabrequim.php';

$componentes = [
    'embreagem' => 'Embreagem',
    'bomba' => 'Bomba',
    'motor' => 'Motor',
    'virabrequim' => 'Virabrequim',
    'cabecote' => 'Cabeçote'
];

if (isset($_GET['ordem'])) {
    $ordem = $_GET['ordem'];
    $componentesComReferencia = [];
    foreach ($componentes as $componente => $titulo) {
        $query = "SELECT COUNT(*) as count FROM $componente WHERE is_reference = 1 AND ordem = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $ordem);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        if ($row['count'] > 0) {
            $componentesComReferencia[$componente] = $titulo;
        }
    }
    if (count($componentesComReferencia) > 0) {
        echo '<div class="componentes-tabs-container">';
        echo '<div class="componentes-tabs">';
        $first = true;
        foreach ($componentesComReferencia as $componente => $titulo) {
            echo '<button type="button" class="tab-btn' . ($first ? ' active' : '') . '" data-tab="tab-' . $componente . '">' . $titulo . '</button>';
            $first = false;
        }
        echo '</div>';
        // Conteúdo das abas
        foreach ($componentesComReferencia as $componente => $titulo) {
            $isActive = $componente === array_key_first($componentesComReferencia);
            echo '<div class="tab-content' . ($isActive ? ' active' : '') . '" id="tab-' . $componente . '">';
            // --- Botões Referência/Medições ---
            echo '<div style="margin-bottom: 16px; display: flex; gap: 8px;">';
            echo '<button type="button" class="toggle-btn ref-btn active" data-target="ref-' . $componente . '">Referências</button>';
            echo '<button type="button" class="toggle-btn med-btn" data-target="med-' . $componente . '">Medições</button>';
            echo '</div>';
            // --- Conteúdo Referência ---
            echo '<div class="toggle-content ref-content" id="ref-' . $componente . '" style="display:block;">';
            displayTableData($conn, $componente, $titulo);
            echo '</div>';
            // --- Conteúdo Medições ---
            echo '<div class="toggle-content med-content" id="med-' . $componente . '" style="display:none;">';
            switch ($componente) {
                case 'embreagem':
                    displayEmbreagemMedicoes($conn, $_GET['ordem']);
                    break;
                case 'bomba':
                    displayBombaMedicoes($conn, $_GET['ordem']);
                    break;
                case 'motor':
                    displayMotorMedicoes($conn, $_GET['ordem']);
                    break;
                case 'virabrequim':
                    displayVirabrequimMedicoes($conn, $_GET['ordem']);
                    break;
                case 'cabecote':
                    displayCabecoteMedicoes($conn, $_GET['ordem']);
                    break;
            }
            echo '</div>';
            echo '</div>'; // tab-content
        }
        echo '</div>';
    } else {
        echo "<div class='error-msg error-msg--center'> <i class='fas fa-info-circle'></i> Nenhum dado de referência encontrado para esta ordem de serviço. O menu não será exibido.</div>";
    }
} else {
    echo "<div class='error-msg error-msg--center'> <i class='fas fa-exclamation-triangle'></i> Erro: Parâmetro 'ordem' não foi especificado.</div>";
}

// Botão de sair destacado
?>
<div class="footer-modal">
    <a class="button primary button--sair" id="closeModal3"><i class="fas fa-sign-out-alt"></i> Sair</a>
</div>

<link rel="stylesheet" href="pages/ordemservico/modal/dados/dados.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
$query = "SELECT val_adm_limite_min, val_adm_limite_max, val_esc_limite_min, val_esc_limite_max 
          FROM cabecote 
          WHERE is_reference = 1 AND ordem = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $ordem);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cabecote_ref = mysqli_fetch_assoc($result);
?>

<input type="hidden" id="val_adm_limite_min" value="<?php echo $cabecote_ref['val_adm_limite_min']; ?>">
<input type="hidden" id="val_adm_limite_max" value="<?php echo $cabecote_ref['val_adm_limite_max']; ?>">
<input type="hidden" id="val_esc_limite_min" value="<?php echo $cabecote_ref['val_esc_limite_min']; ?>">
<input type="hidden" id="val_esc_limite_max" value="<?php echo $cabecote_ref['val_esc_limite_max']; ?>">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="pages/ordemservico/modal/calcularPastilha.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Toggle Referência/Medições
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const parent = this.closest('.tab-content');
            parent.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            parent.querySelectorAll('.toggle-content').forEach(tc => tc.style.display = 'none');
            const target = parent.querySelector('#' + this.getAttribute('data-target'));
            if (target) target.style.display = 'block';
        });
    });
    
    // Pastilha cálculo (mantém)
    const folgaInputs = document.querySelectorAll('.folga-input');
    folgaInputs.forEach(input => {
        input.addEventListener('change', function() {
            calcularPastilha(this);
        });
        input.addEventListener('input', function() {
            calcularPastilha(this);
        });
    });
    folgaInputs.forEach(input => {
        if (input.value) {
            calcularPastilha(input);
        }
    });

    // Função universal para validar todos os campos de medição
    // Função universal para validar todos os campos de medição
    function validateAllMeasurementFields() {
    // Validar TODOS os campos com data-reference (validação genérica universal)
    document.querySelectorAll('input.meas-input[data-reference]').forEach(input => {
    if (typeof validateInput === 'function') {
    validateInput(input);
    }
    });
    
    // Validar campos específicos de cabeçote (folgas de válvulas)
    validateCabecoteSpecificFields();
    
    // Validar campos específicos de embreagem
    validateEmbreagemSpecificFields();
    
    // Validar outros campos específicos que não têm data-reference
    validateOtherSpecificFields();
    }
    
    // Validação específica para campos de cabeçote (folgas de válvulas)
    function validateCabecoteSpecificFields() {
        const valAdmMin = parseFloat(document.getElementById('val_adm_limite_min')?.value || 0);
        const valAdmMax = parseFloat(document.getElementById('val_adm_limite_max')?.value || 0);
        const valEscMin = parseFloat(document.getElementById('val_esc_limite_min')?.value || 0);
        const valEscMax = parseFloat(document.getElementById('val_esc_limite_max')?.value || 0);
        
        // Validar campos de admissão
        document.querySelectorAll('input[name*="adm_folga"]').forEach(input => {
            if (input.value.trim() === '') {
                input.classList.remove('out-of-range-input');
                return;
            }
            
            const inputValue = parseFloat(input.value.replace(',', '.'));
            if (isNaN(inputValue)) {
                input.classList.remove('out-of-range-input');
                return;
            }
            
            const convertedValue = inputValue > 1 ? inputValue / 100 : inputValue;
            
            if (convertedValue < valAdmMin || convertedValue > valAdmMax) {
                input.classList.add('out-of-range-input');
            } else {
                input.classList.remove('out-of-range-input');
            }
        });
        
        // Validar campos de escape
        document.querySelectorAll('input[name*="esc_folga"]').forEach(input => {
            if (input.value.trim() === '') {
                input.classList.remove('out-of-range-input');
                return;
            }
            
            const inputValue = parseFloat(input.value.replace(',', '.'));
            if (isNaN(inputValue)) {
                input.classList.remove('out-of-range-input');
                return;
            }
            
            const convertedValue = inputValue > 1 ? inputValue / 100 : inputValue;
            
            if (convertedValue < valEscMin || convertedValue > valEscMax) {
                input.classList.add('out-of-range-input');
            } else {
                input.classList.remove('out-of-range-input');
            }
        });
    }
    
    // Validação específica para campos de embreagem
    function validateEmbreagemSpecificFields() {
        const refCells = document.querySelectorAll('.embreagem-medicoes .ref-value');
        let discoFriccaoMin = 0;
        let discoSeparadorMax = 0;
        
        refCells.forEach(cell => {
            const row = cell.closest('tr');
            const itemCell = row?.querySelector('td:first-child');
            if (itemCell) {
                const text = itemCell.textContent.toLowerCase();
                if (text.includes('espessura mínima')) {
                    discoFriccaoMin = parseFloat(cell.textContent.replace(',', '.')) || 0;
                } else if (text.includes('empenamento máximo')) {
                    discoSeparadorMax = parseFloat(cell.textContent.replace(',', '.')) || 0;
                }
            }
        });
        
        document.querySelectorAll('input[name*="disco_friccao_espes"]').forEach(input => {
            if (input.value.trim() === '') {
                input.classList.remove('out-of-range-input');
                return;
            }
            const value = parseFloat(input.value.replace(',', '.'));
            if (!isNaN(value) && discoFriccaoMin > 0 && value < discoFriccaoMin) {
                input.classList.add('out-of-range-input');
            } else {
                input.classList.remove('out-of-range-input');
            }
        });
        
        document.querySelectorAll('input[name*="disco_separador_emp"]').forEach(input => {
            if (input.value.trim() === '') {
                input.classList.remove('out-of-range-input');
                return;
            }
            const value = parseFloat(input.value.replace(',', '.'));
            if (!isNaN(value) && discoSeparadorMax > 0 && value > discoSeparadorMax) {
                input.classList.add('out-of-range-input');
            } else {
                input.classList.remove('out-of-range-input');
            }
        });
    }
    
    // Validação para outros campos específicos
    function validateOtherSpecificFields() {
        // Aqui você pode adicionar validações específicas para outros componentes
        // que não são cobertas pela validação genérica
    }
    
    // Event listeners para validação em tempo real
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('meas-input')) {
            setTimeout(validateAllMeasurementFields, 100);
        }
    });
    
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('meas-input')) {
            validateAllMeasurementFields();
        }
    });
    
    // Validar campos ao trocar de aba
    document.querySelectorAll('.tab-btn, .toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            setTimeout(validateAllMeasurementFields, 200);
        });
    });
    
    // Validação inicial
    setTimeout(validateAllMeasurementFields, 500);
});
</script>