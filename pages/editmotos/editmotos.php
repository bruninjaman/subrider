<?php
// Incluir inicialização segura (sessão, config, db)
require_once __DIR__ . '/../../../config/init.php';
require_once __DIR__ . '/../../../src/Database/Database.php';
require_once __DIR__ . '/../../../src/Permissions/PermissionManager.php'; // Para verificar permissão de ver

use Subrider\Database\Database;
use Subrider\Permissions\PermissionManager;

$moto = null; // Inicializa a variável
$error_message = null; // Para mensagens de erro
$motoId = null;

// Definição de $baseUrl (idealmente vindo de init.php)
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// Verificar permissão para ver/editar a página
// TODO: Definir a permissão correta
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    // Redireciona para o dashboard ou página de erro
    header("Location: {$baseUrl}/dashboard.php?error=permission");
    exit;
}

// Validar se motoID foi passado e é um número
if (!isset($_GET["motoID"]) || !ctype_digit((string)$_GET["motoID"])) {
    $error_message = "ID da motocicleta inválido ou não fornecido.";
} else {
    $motoId = intval($_GET["motoID"]);

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Usar Prepared Statement para segurança
        $sql = "SELECT * FROM motocicletas WHERE motoId = :motoId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':motoId', $motoId, PDO::PARAM_INT);
        $stmt->execute();

        $moto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$moto) {
            $error_message = "Motocicleta com ID {$motoId} não encontrada.";
        }

    } catch (PDOException $e) {
        error_log("Erro PDO em editmotos.php (fetch): " . $e->getMessage());
        $error_message = "Erro ao buscar os dados da motocicleta.";
        $moto = null; // Garante que não tentaremos usar dados inválidos
    }
}


// Exibir mensagens de erro ou sucesso da sessão
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert success" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 4px;">' . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['success_message']);
}

// Usar form_errors para consistência com outros scripts
if (isset($_SESSION['form_errors']) && is_array($_SESSION['form_errors'])) {
    echo '<div class="alert error" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">';
    foreach ($_SESSION['form_errors'] as $error) {
        echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '<br>';
    }
    echo '</div>';
    unset($_SESSION['form_errors']);
}

// Obter erros de validação específicos do campo (se existirem)
$validation_errors = isset($_SESSION['validation_errors']) ? $_SESSION['validation_errors'] : [];
unset($_SESSION['validation_errors']);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="<?php echo $baseUrl; ?>/assets/css/images/logo-branco-crop.png">

        <?php // Exibe mensagem de erro principal se houver (ex: ID inválido, moto não encontrada)
        if ($error_message): ?>
            <div class="alert error" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php // Só continua se a moto foi carregada com sucesso
        elseif ($moto): ?>
            <div class="row">
                <div class="col-8">
                    <h2>Editar informações do Veículo (#<?php echo htmlspecialchars($moto['motoId'], ENT_QUOTES, 'UTF-8'); ?>)</h2>
                </div>
                <div class="col-4 text-right">
                    <!-- Link para histórico usa $baseUrl -->
                    <a href="<?php echo $baseUrl; ?>/editmotos.php?motoID=<?php echo htmlspecialchars($motoId, ENT_QUOTES, 'UTF-8'); ?>&historico=1" class="button small">
                        <i class="fas fa-history"></i> Ver Histórico
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['historico'])):
                 // Inclui histórico (verificar se historico.php existe e é seguro)
                 // TODO: Revisar historico.php
                 $filePathHistorico = __DIR__ . "/historico.php";
                 if (file_exists($filePathHistorico)) {
                      require_once($filePathHistorico);
                 } else {
                      echo '<p style="color:red;">Erro: Arquivo de histórico não encontrado.</p>';
                 }
             else: // Mostra o formulário de edição ?>
                <!-- Formulário aponta para script correto usando $baseUrl -->
                <form method="post" action="<?php echo $baseUrl; ?>/scripts/tabelaMotos/edit-moto.php?motoID=<?php echo htmlspecialchars($motoId, ENT_QUOTES, 'UTF-8'); ?>" enctype="multipart/form-data">
                    <!-- Incluir Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="row">
                        <div class="col-12">
                            <div class="upload-image">
                                <div class="card thmb">
                                    <?php // Usa $baseUrl e placeholder
                                    $fotoUrl = !empty($moto["foto"]) ? $baseUrl . '/' . ltrim(htmlspecialchars($moto["foto"], ENT_QUOTES, 'UTF-8'), '/') : $baseUrl . '/assets/css/images/placeholder-moto.png';
                                    ?>
                                    <img src="<?php echo $fotoUrl; ?>" alt="preview" id="foto-preview" />
                                    <input type="file" name="foto" id="foto-input" accept="image/*" /><i class="fas fa-arrow-circle-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label>Endereço:</label>
                            <!-- Usa dados da $moto e sanitiza output -->
                            <input type="text" name="endereco" value="<?php echo htmlspecialchars($moto["endereco"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                                   class="<?php echo isset($validation_errors['endereco']) ? 'error' : ''; ?>">
                            <?php if (isset($validation_errors['endereco'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($validation_errors['endereco'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3">
                            <label>Ano:</label>
                            <input type="text" name="ano" value="<?php echo htmlspecialchars($moto["ano"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" minlength="4" maxlength="4" required
                                   class="<?php echo isset($validation_errors['ano']) ? 'error' : ''; ?>">
                            <?php if (isset($validation_errors['ano'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($validation_errors['ano'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-5">
                            <label>Modelo:</label>
                            <input type="text" name="modelo" value="<?php echo htmlspecialchars($moto["modelo"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                                   class="<?php echo isset($validation_errors['modelo']) ? 'error' : ''; ?>">
                            <?php if (isset($validation_errors['modelo'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($validation_errors['modelo'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-4">
                            <label>Marca:</label>
                            <input type="text" name="marca" value="<?php echo htmlspecialchars($moto["marca"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                                   class="<?php echo isset($validation_errors['marca']) ? 'error' : ''; ?>">
                            <?php if (isset($validation_errors['marca'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($validation_errors['marca'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label>Proprietario:</label> <!-- TODO: Usar autocomplete aqui também? -->
                            <input type="text" name="proprietario" value="<?php echo htmlspecialchars($moto["proprietario"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                                   class="<?php echo isset($validation_errors['proprietario']) ? 'error' : ''; ?>">
                             <?php if (isset($validation_errors['proprietario'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($validation_errors['proprietario'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-4">
                            <label>Placa:</label>
                            <input type="text" name="placa" value="<?php echo htmlspecialchars($moto["placa"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                                   class="<?php echo isset($validation_errors['placa']) ? 'error' : ''; ?>"
                                   pattern="[A-Z]{3}-?[0-9][A-Z0-9][0-9]{2}" <!-- Ajustar pattern se necessário -->
                                   title="Formato Mercosul: ABC1D23 ou antigo: ABC-1234">
                            <?php if (isset($validation_errors['placa'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($validation_errors['placa'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-2">
                            <label>KM:</label>
                            <!-- Corrigido: name="km" -->
                            <input type="number" name="km" value="<?php echo htmlspecialchars($moto["km"] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" required min="0"
                                   class="<?php echo isset($validation_errors['km']) ? 'error' : ''; ?>">
                            <?php if (isset($validation_errors['km'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($validation_errors['km'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <button class="button primary" type="submit"><i class="fas fa-save"></i> Salvar Alterações</button>
                     <a href="<?php echo $baseUrl; ?>/tabelaMotos.php" class="button">
                         <i class="fas fa-times"></i> Cancelar
                    </a>
                </form>
            <?php endif; // Fim do else (mostra formulário) ?>
        <?php endif; // Fim do elseif ($moto) ?>
    </div>
</section>

<style>
.text-right {
    text-align: right;
}

.button.small {
    padding: 0.5em 1em;
    font-size: 0.9em;
}

.button.small i {
    margin-right: 5px;
}
</style>

<script>
// Preview da imagem antes do upload
document.getElementById('foto-input').onchange = function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('foto-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
};
</script>