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

// Chamar a função de medições para cada componente
if (isset($_GET['ordem'])) {
    displayTableData($conn, "embreagem", "Embreagem");
    displayEmbreagemMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "bomba", "Bomba");
    displayBombaMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "motor", "Motor");
    displayMotorMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "virabrequim", "Virabrequim");
    displayVirabrequimMedicoes($conn, $_GET['ordem']);
    displayTableData($conn, "cabecote", "Cabeçote");
    displayCabecoteMedicoes($conn, $_GET['ordem']);
} else {
    echo "<div class='error-msg'>Erro: Parâmetro 'ordem' não foi especificado.</div>";
}

echo '<a class="button primary" id="closeModal3">Sair</a>';
?>

<link rel="stylesheet" href="assets/css/ordemservico/menus/dados.css">

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se os valores de referência estão presentes
    const valAdmMin = document.getElementById('val_adm_limite_min').value;
    const valAdmMax = document.getElementById('val_adm_limite_max').value;
    const valEscMin = document.getElementById('val_esc_limite_min').value;
    const valEscMax = document.getElementById('val_esc_limite_max').value;


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