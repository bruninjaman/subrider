<?php
// Verificação inicial do parâmetro ordem
if (!isset($_GET['ordem'])) {
    die("<div class='error-msg'>Erro: Parâmetro 'ordem' não foi especificado na URL.</div>");
}

// Usar a ordem como string
$ordem = $_GET['ordem'];

// Verificação da conexão com o banco de dados
if (!isset($conn) || !$conn) {
    die("<div class='error-msg'>Erro: Conexão com o banco de dados não estabelecida.</div>");
}

// Verificar se existem dados de referência para a ordem especificada
$checkQuery = "SELECT COUNT(*) as count FROM cabecote WHERE is_reference = 1 AND ordem = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($checkStmt, "s", $ordem);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$checkRow = mysqli_fetch_assoc($checkResult);

// Incluir os arquivos de funções
require_once 'dados_util.php';
require_once 'dados_cabecote.php';
require_once 'dados_embreagem.php';
require_once 'dados_bomba.php';
require_once 'dados_motor.php';
require_once 'dados_virabrequim.php';

// Array com os componentes e seus títulos
$componentes = [
    'embreagem' => 'Embreagem',
    'bomba' => 'Bomba',
    'motor' => 'Motor',
    'virabrequim' => 'Virabrequim',
    'cabecote' => 'Cabeçote'
];

// Criar os dropdowns para cada componente
if (isset($_GET['ordem'])) {
    echo '<div class="componentes-container">';
    foreach ($componentes as $componente => $titulo) {
        echo '<div class="componente-dropdown">';
        echo '<button type="button" class="dropdown-btn">' . $titulo . ' <i class="fas fa-chevron-down"></i></button>';
        echo '<div class="dropdown-content">';
        displayTableData($conn, $componente, $titulo);
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
        echo '</div>';
    }
    echo '</div>';
} else {
    echo "<div class='error-msg'>Erro: Parâmetro 'ordem' não foi especificado.</div>";
}

echo '<a class="button primary" id="closeModal3">Sair</a>';
?>

<link rel="stylesheet" href="assets/css/ordemservico/menus/dados.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
// Buscar dados de referência do cabeçote para os valores de limite
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
<script src="pages\ordemservico\modal\calcularPastilha.js"></script>

<style>
.componentes-container {
    margin-bottom: 20px;
}

.componente-dropdown {
    margin-bottom: 10px;
}

.dropdown-btn {
    background-color: #2a2c35;
    color: #e5e5e5;
    padding: 12px 20px;
    width: 100%;
    border: none;
    text-align: left;
    cursor: pointer;
    font-size: 1.1em;
    font-weight: bold;
    border-radius: 8px;
    transition: background-color 0.3s;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dropdown-btn:hover {
    background-color: #3a3c45;
}

.dropdown-content {
    display: none;
    background-color: #1e2029;
    padding: 15px;
    border-radius: 8px;
    margin-top: 5px;
    border: 1px solid #333;
}

.dropdown-content.active {
    display: block;
}

.dropdown-btn i {
    transition: transform 0.3s;
}

.dropdown-btn.active i {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se os valores de referência estão presentes
    const valAdmMin = document.getElementById('val_adm_limite_min').value;
    const valAdmMax = document.getElementById('val_adm_limite_max').value;
    const valEscMin = document.getElementById('val_esc_limite_min').value;
    const valEscMax = document.getElementById('val_esc_limite_max').value;

    // Adicionar funcionalidade aos dropdowns
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir o comportamento padrão do botão
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            content.classList.toggle('active');
        });
    });

    // Vincular o evento de cálculo a todos os inputs de folga
    const folgaInputs = document.querySelectorAll('.folga-input');
    folgaInputs.forEach(input => {
        input.addEventListener('change', function() {
            calcularPastilha(this);
        });
        input.addEventListener('input', function() {
            calcularPastilha(this);
        });
    });

    // Inicializar cálculos para inputs que já têm valores
    folgaInputs.forEach(input => {
        if (input.value) {
            calcularPastilha(input);
        }
    });
});
</script>