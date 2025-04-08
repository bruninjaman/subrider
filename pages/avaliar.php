<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/AvaliacaoManager.php';
require_once __DIR__ . '/../includes/session_manager.php';
require_once __DIR__ . '/../classes/Security/PermissionManager.php';

$sessionManager = new SessionManager();
$avaliacaoManager = new AvaliacaoManager();
$permManager = \Security\PermissionManager::getInstance();

// Verifica se o usuário está logado
if (!$sessionManager->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Verifica permissão
$permManager->loadUserPermissions($_SESSION['user_id']);
if (!$permManager->hasPermission('avaliacoes.create')) {
    header('Location: access-denied.php');
    exit;
}

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Verifica se foi passado o ID da ordem
if (!isset($_GET['ordem'])) {
    die('Ordem não especificada');
}

$ordemId = $_GET['ordem'];
$proprietarioId = $sessionManager->getClienteId();

// Se for POST, processa a avaliação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota = (int) $_POST['nota'];
    $comentario = trim($_POST['comentario']);
    
    if ($avaliacaoManager->criarAvaliacao($ordemId, $nota, $comentario, $proprietarioId)) {
        $mensagem = "Avaliação enviada com sucesso! Agradecemos seu feedback.";
        $tipo = "success";
    } else {
        $mensagem = "Erro ao enviar avaliação. Por favor, tente novamente.";
        $tipo = "danger";
    }
}

// Inclui o cabeçalho
require_once __DIR__ . '/../includes/header.php';
?>

<section id="banner">
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Avaliar Ordem de Serviço #<?php echo htmlspecialchars($ordemId); ?></h2>
                    
                    <?php if (isset($mensagem)): ?>
                        <div class="alert alert-<?php echo $tipo; ?>">
                            <?php echo $mensagem; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" class="avaliacao-form">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label>Sua Avaliação</label>
                                <div class="rating">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="nota" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                        <label for="star<?php echo $i; ?>">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <label for="comentario">Comentário (opcional)</label>
                                <textarea name="comentario" id="comentario" rows="4" class="form-control" 
                                          placeholder="Conte-nos mais sobre sua experiência..."></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="button primary">
                                    Enviar Avaliação
                                </button>
                                <a href="ordemservico.php?ordem=<?php echo htmlspecialchars($ordemId); ?>" class="button">
                                    Voltar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    font-size: 2em;
    margin: 1em 0;
}

.rating input {
    display: none;
}

.rating label {
    cursor: pointer;
    padding: 0 0.2em;
    color: #ddd;
}

.rating label:hover,
.rating label:hover ~ label,
.rating input:checked ~ label {
    color: #ffd700;
}

.rating label i {
    transition: color 0.2s ease;
}

.avaliacao-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 2em;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert {
    margin-bottom: 2em;
    padding: 1em;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php
require_once __DIR__ . '/../includes/footer.php';
?> 