<?php
// Incluir inicialização segura
require_once __DIR__ . '/../../../config/init.php';
require_once __DIR__ . '/../../../src/Database/Database.php';
require_once __DIR__ . '/../../../src/Permissions/PermissionManager.php';

use Subrider\Database\Database;
use Subrider\Permissions\PermissionManager;

$servico = null;
$error_message = null;
$servicoId = null;

// Definição de $baseUrl
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// Verificar permissão
// TODO: Definir a permissão correta
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    header("Location: {$baseUrl}/dashboard.php?error=permission");
    exit;
}

// Validar ID
if (!isset($_GET["servicoID"]) || !ctype_digit((string)$_GET["servicoID"])) {
    $error_message = "ID do serviço inválido ou não fornecido.";
} else {
    $servicoId = intval($_GET["servicoID"]);

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Usar Prepared Statement
        // TODO: Confirmar colunas (item, tipo, preco, descricao)
        $sql = "SELECT * FROM servicos WHERE servicoId = :servicoId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':servicoId', $servicoId, PDO::PARAM_INT);
        $stmt->execute();
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$servico) {
            $error_message = "Serviço com ID {$servicoId} não encontrado.";
        }

    } catch (PDOException $e) {
        error_log("Erro PDO em editservicos.php (fetch): " . $e->getMessage());
        $error_message = "Erro ao buscar os dados do serviço.";
        $servico = null;
    }
}

// Exibir mensagens de erro/sucesso da sessão
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert success" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 4px;">' . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['form_errors']) && is_array($_SESSION['form_errors'])) {
    echo '<div class="alert error" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">';
    foreach ($_SESSION['form_errors'] as $error) {
        echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '<br>';
    }
    echo '</div>';
    unset($_SESSION['form_errors']);
}

$validation_errors = isset($_SESSION['validation_errors']) ? $_SESSION['validation_errors'] : [];
unset($_SESSION['validation_errors']);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="<?php echo $baseUrl; ?>/assets/css/images/logo-branco-crop.png">

        <?php if ($error_message): ?>
            <div class="alert error" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php elseif ($servico): ?>
            <!-- Formulário aponta para script correto -->
            <form method="post" action="<?php echo $baseUrl; ?>/scripts/tabelaServicosEdit/edit-servico.php?servicoID=<?php echo htmlspecialchars($servicoId, ENT_QUOTES, 'UTF-8'); ?>" enctype="multipart/form-data">
                 <!-- CSRF Token -->
                 <?php /* <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>"> */ ?>

                <div class="row">
                    <div class="col-12">
                        <h2>Editar Serviço (#<?php echo htmlspecialchars($servicoId, ENT_QUOTES, 'UTF-8'); ?>)</h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <label for="item">Item:</label>
                        <input type="text" name="item" id="item" value="<?php echo htmlspecialchars($servico["item"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                               class="<?php echo isset($validation_errors['item']) ? 'error' : ''; ?>">
                        <?php if (isset($validation_errors['item'])): ?>
                           <span class="error-message" style="color:red;"><?php echo htmlspecialchars($validation_errors['item'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <label for="tipo">Tipo:</label>
                        <input type="text" name="tipo" id="tipo" value="<?php echo htmlspecialchars($servico["tipo"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                               class="<?php echo isset($validation_errors['tipo']) ? 'error' : ''; ?>">
                         <?php if (isset($validation_errors['tipo'])): ?>
                           <span class="error-message" style="color:red;"><?php echo htmlspecialchars($validation_errors['tipo'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-4">
                        <label for="preco">Preço (R$):</label>
                        <input type="text" name="preco" id="preco" value="<?php echo htmlspecialchars(number_format($servico['preco'] ?? 0, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>" required placeholder="0,00" pattern="[0-9]+([,\.][0-9]{1,2})?"
                               class="<?php echo isset($validation_errors['preco']) ? 'error' : ''; ?>">
                         <?php if (isset($validation_errors['preco'])): ?>
                           <span class="error-message" style="color:red;"><?php echo htmlspecialchars($validation_errors['preco'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                        <small>Use vírgula para centavos.</small>
                    </div>
                    <div class="col-8">
                        <label for="descricao">Descrição:</label>
                        <textarea name="descricao" id="descricao" rows="3" required
                                  class="<?php echo isset($validation_errors['descricao']) ? 'error' : ''; ?>"><?php echo htmlspecialchars($servico["descricao"] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                         <?php if (isset($validation_errors['descricao'])): ?>
                           <span class="error-message" style="color:red;"><?php echo htmlspecialchars($validation_errors['descricao'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <br>
                <button class="button primary" type="submit"><i class="fas fa-save"></i> Salvar Alterações</button>
                 <a href="<?php echo $baseUrl; ?>/tabelaServicos.php" class="button">
                     <i class="fas fa-times"></i> Cancelar
                 </a>
            </form>
        <?php endif; ?>
    </div>
</section>

<!-- Opcional: Adicionar máscara para preço -->
<script src="<?php echo $baseUrl; ?>/assets/js/global/jquery.min.js"></script>
<script src="<?php echo $baseUrl; ?>/assets/js/global/jquery.mask.min.js"></script>
<script>
$(document).ready(function(){
  $('#preco').mask('#.##0,00', {reverse: true});
});
</script>