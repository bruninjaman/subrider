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

// Cabeçalho da página
echo '<div class="dados-header">';
echo '<div class="dados-header__content">';
echo '<h1 class="dados-header__title"><i class="fas fa-cogs"></i> Dados Técnicos</h1>';
echo '<div class="dados-header__subtitle">Ordem de Serviço: <span class="ordem-number">' . htmlspecialchars($ordem) . '</span></div>';
echo '</div>';
echo '</div>';

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
        echo '<div class="dados-main-container">';
        echo '<div class="componentes-navigation">';
        echo '<div class="nav-header">';
        echo '<h2 class="nav-title"><i class="fas fa-list"></i> Componentes Disponíveis</h2>';
        echo '<div class="nav-subtitle">Selecione um componente para visualizar os dados</div>';
        echo '</div>';
        echo '<div class="componentes-tabs">';
        $first = true;
        foreach ($componentesComReferencia as $componente => $titulo) {
            $icon = getComponentIcon($componente);
            echo '<button type="button" class="tab-btn' . ($first ? ' active' : '') . '" data-tab="tab-' . $componente . '">';
            echo '<i class="' . $icon . '"></i>';
            echo '<span>' . $titulo . '</span>';
            echo '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="componentes-content">';
        // Conteúdo das abas
        foreach ($componentesComReferencia as $componente => $titulo) {
            $isActive = $componente === array_key_first($componentesComReferencia);
            echo '<div class="tab-content' . ($isActive ? ' active' : '') . '" id="tab-' . $componente . '">';
            
            // Cabeçalho do componente
            echo '<div class="component-header">';
            echo '<h3 class="component-title">';
            echo '<i class="' . getComponentIcon($componente) . '"></i>';
            echo $titulo;
            echo '</h3>';
            echo '<div class="component-description">' . getComponentDescription($componente) . '</div>';
            echo '</div>';
            
            // --- Botões Referência/Medições ---
            echo '<div class="data-type-selector">';
            echo '<div class="selector-buttons">';
            echo '<button type="button" class="toggle-btn ref-btn" data-target="ref-' . $componente . '">';
            echo '<i class="fas fa-book"></i><span>Referências</span>';
            echo '</button>';
            echo '<button type="button" class="toggle-btn med-btn active" data-target="med-' . $componente . '">';
            echo '<i class="fas fa-ruler"></i><span>Medições</span>';
            echo '</button>';
            echo '</div>';
            echo '</div>';
            // --- Conteúdo Referência ---
            echo '<div class="toggle-content ref-content" id="ref-' . $componente . '" style="display:none;">';
            displayTableData($conn, $componente, $titulo);
            echo '</div>';
            // --- Conteúdo Medições ---
            echo '<div class="toggle-content med-content" id="med-' . $componente . '" style="display:block;">';
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
        echo '</div>'; // componentes-content
        echo '</div>'; // dados-main-container
    } else {
        echo "<div class='error-msg error-msg--center'> <i class='fas fa-info-circle'></i> Nenhum dado de referência encontrado para esta ordem de serviço. O menu não será exibido.</div>";
    }
} else {
    echo "<div class='error-msg error-msg--center'> <i class='fas fa-exclamation-triangle'></i> Erro: Parâmetro 'ordem' não foi especificado.</div>";
}

// Funções auxiliares para ícones e descrições
function getComponentIcon($componente) {
    $icons = [
        'embreagem' => 'fas fa-circle-notch',
        'bomba' => 'fas fa-tint',
        'motor' => 'fas fa-cog',
        'virabrequim' => 'fas fa-sync-alt',
        'cabecote' => 'fas fa-cube'
    ];
    return isset($icons[$componente]) ? $icons[$componente] : 'fas fa-wrench';
}

function getComponentDescription($componente) {
    $descriptions = [
        'embreagem' => 'Sistema de transmissão de potência do motor',
        'bomba' => 'Sistema de circulação de fluidos',
        'motor' => 'Unidade principal de combustão',
        'virabrequim' => 'Eixo de conversão do movimento',
        'cabecote' => 'Cabeça do motor com válvulas'
    ];
    return isset($descriptions[$componente]) ? $descriptions[$componente] : 'Componente do sistema';
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
    // Melhorar navegação das abas com animações
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover classes ativas
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(tc => {
                tc.classList.remove('active');
                tc.style.opacity = '0';
            });
            
            // Adicionar classe ativa ao botão clicado
            this.classList.add('active');
            
            // Mostrar conteúdo com animação
            const tabId = this.getAttribute('data-tab');
            const targetContent = document.getElementById(tabId);
            
            setTimeout(() => {
                targetContent.classList.add('active');
                targetContent.style.opacity = '1';
            }, 150);
            
            // Scroll suave para o topo do conteúdo em dispositivos móveis
            if (window.innerWidth <= 768) {
                targetContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Inicializar opacidade dos conteúdos
    tabContents.forEach(tc => {
        if (tc.classList.contains('active')) {
            tc.style.opacity = '1';
        } else {
            tc.style.opacity = '0';
        }
    });
    
    // Toggle Referência/Medições com animações melhoradas
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const parent = this.closest('.tab-content');
            const targetId = this.getAttribute('data-target');
            const target = parent.querySelector('#' + targetId);
            
            // Se já está ativo, não fazer nada
            if (this.classList.contains('active')) return;
            
            // Remover classes ativas dos botões
            parent.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Animar transição do conteúdo
            parent.querySelectorAll('.toggle-content').forEach(tc => {
                tc.style.opacity = '0';
                setTimeout(() => {
                    tc.style.display = 'none';
                }, 200);
            });
            
            // Mostrar novo conteúdo com animação
            setTimeout(() => {
                if (target) {
                    target.style.display = 'block';
                    target.style.opacity = '0';
                    setTimeout(() => {
                        target.style.opacity = '1';
                    }, 50);
                }
            }, 200);
        });
    });
    
    // Inicializar opacidade dos conteúdos toggle
    document.querySelectorAll('.toggle-content').forEach(tc => {
        if (tc.style.display !== 'none') {
            tc.style.opacity = '1';
        }
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

<!-- AJAX e Sistema de Histórico de Campos -->
<script src="dados_ajax.js"></script>