<?php
require_once '../includes/client_manager.php';
require_once '../includes/session_manager.php';

$session = new SessionManager();

// Se já estiver logado, redireciona para o dashboard
if ($session->getClienteId()) {
    header('Location: dashboard.php');
    exit;
}

$token = $_GET['token'] ?? '';
$erro = '';
$sucesso = '';

// Processa o formulário de redefinição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';
    $confirmacao = $_POST['confirmacao'] ?? '';
    
    if (strlen($senha) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres';
    } elseif ($senha !== $confirmacao) {
        $erro = 'As senhas não conferem';
    } else {
        try {
            $clientManager = new ClientManager();
            if ($clientManager->redefinirSenha($token, $senha)) {
                $sucesso = 'Senha redefinida com sucesso! Você já pode fazer login.';
            } else {
                $erro = 'Token inválido ou expirado';
            }
        } catch (Exception $e) {
            $erro = $e->getMessage();
        }
    }
}

$pageTitle = 'Redefinir Senha - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Redefinir Senha</h2>
                    
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($sucesso); ?>
                            <p class="mt-3 mb-0">
                                <a href="login.php" class="btn btn-primary">Ir para Login</a>
                            </p>
                        </div>
                    <?php else: ?>
                        <form method="post" action="">
                            <div class="form-group mb-3">
                                <label for="senha">Nova Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" 
                                       minlength="8" required>
                                <small class="form-text text-muted">
                                    A senha deve ter pelo menos 8 caracteres
                                </small>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="confirmacao">Confirme a Nova Senha</label>
                                <input type="password" class="form-control" id="confirmacao" 
                                       name="confirmacao" minlength="8" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Redefinir Senha</button>
                                <a href="login.php" class="btn btn-secondary">Voltar para Login</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 