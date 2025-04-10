<?php
// Incluir inicialização segura (sessão, config, db)
require_once __DIR__ . '/../../../config/init.php'; // Caminho ajustado
require_once __DIR__ . '/../../../src/Database/Database.php';

use Subrider\Database\Database;

$peca = null; // Inicializa a variável
$error_message = null; // Para mensagens de erro

// Validar se pecaID foi passado e é um número
if (!isset($_GET["pecaID"]) || !ctype_digit((string)$_GET["pecaID"])) {
    // Lidar com erro: ID inválido ou ausente
    // Pode redirecionar, mostrar mensagem, etc.
    // Por enquanto, definimos uma mensagem de erro.
    $error_message = "ID da peça inválido ou não fornecido.";
} else {
    $pecaId = $_GET["pecaID"];

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Usar Prepared Statement para segurança
        $sql = "SELECT * FROM pecas WHERE pecaId = :pecaId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pecaId', $pecaId, PDO::PARAM_INT);
        $stmt->execute();

        $peca = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$peca) {
            // Peça não encontrada
            $error_message = "Peça com ID {$pecaId} não encontrada.";
        }

    } catch (PDOException $e) {
        // Erro de banco de dados
        // Reason: Log de erro atualizado para refletir o novo nome do arquivo.
        error_log("Erro PDO em edit_pecas.php (fetch): " . $e->getMessage());
        $error_message = "Erro ao buscar os dados da peça.";
        $peca = null; // Garante que não tentaremos usar dados inválidos
    }
}

// Define $baseUrl (idealmente vindo de init.php)
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="<?php echo $baseUrl; ?>/assets/css/images/logo-branco-crop.png">

        <?php // Exibe mensagem de erro se houver
        if ($error_message): ?>
            <div class="error-message" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php // Só exibe o formulário se a peça foi carregada com sucesso
        elseif ($peca): ?>
            <form method="post" action="<?php echo $baseUrl; ?>/scripts/tabelaPecasEdit/edit-peca.php?pecaID=<?php echo htmlspecialchars($peca["pecaId"], ENT_QUOTES, 'UTF-8'); ?>" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <h2>Editar Peça (#<?php echo htmlspecialchars($peca["pecaId"], ENT_QUOTES, 'UTF-8'); ?>)</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="upload-image">
                            <div class="card thmb">
                                <?php // Usa $baseUrl para a imagem e sanitiza
                                $fotoUrl = !empty($peca["foto"]) ? $baseUrl . '/' . ltrim(htmlspecialchars($peca["foto"], ENT_QUOTES, 'UTF-8'), '/') : $baseUrl . '/assets/css/images/placeholder-peca.png'; // Adiciona placeholder
                                ?>
                                <img src="<?php echo $fotoUrl; ?>" alt="preview" />
                                <input type="file" name="foto" /><i class="fas fa-arrow-circle-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <label>Grupo:</label>
                        <input type="text" name="grupo" value="<?php echo htmlspecialchars($peca["grupo"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-4">
                        <label>Item:</label>
                        <input type="text" name="item" value="<?php echo htmlspecialchars($peca["item"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-4">
                    <label>Parte:</label>
                        <input type="text" name="parte" value="<?php echo htmlspecialchars($peca["parte"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>
                <br>
                <input class="button primary" type="submit" value="Salvar Alterações">
            </form>
        <?php endif; ?>
    </div>
</section>
