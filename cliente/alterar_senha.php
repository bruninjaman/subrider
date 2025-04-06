<?php
require_once '../includes/session_manager.php';
require_once '../includes/database.php';
require_once '../includes/validation.php';

$session = new SessionManager();

// Verifica se o cliente está logado
if (!$session->isClienteLoggedIn()) {
    header('Location: login.php');
    exit;
}

$clienteId = $session->getClienteId();
$db = new Database();
$errors = [];
$success = false;

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';
    
    // Validações
    if (empty($senhaAtual)) {
        $errors[] = 'A senha atual é obrigatória.';
    }
    
    if (empty($novaSenha)) {
        $errors[] = 'A nova senha é obrigatória.';
    } elseif (strlen($novaSenha) < 8) {
        $errors[] = 'A nova senha deve ter pelo menos 8 caracteres.';
    }
    
    if ($novaSenha !== $confirmarSenha) {
        $errors[] = 'As senhas não conferem.';
    }
    
    if (empty($errors)) {
        // Verifica se a senha atual está correta
        $sql = "SELECT senha FROM proprietarios WHERE id = ?";
        $cliente = $db->query($sql, [$clienteId])[0];
        
        if (!password_verify($senhaAtual, $cliente['senha'])) {
            $errors[] = 'Senha atual incorreta.';
        } else {
            // Atualiza a senha
            $sql = "UPDATE proprietarios SET 
                        senha = ?, 
                        updated_at = NOW() 
                    WHERE id = ?";
            
            try {
                $db->query($sql, [
                    password_hash($novaSenha, PASSWORD_DEFAULT),
                    $clienteId
                ]);
                $success = true;
            } catch (Exception $e) {
                $errors[] = 'Erro ao atualizar a senha. Tente novamente.';
            }
        }
    }
}

$pageTitle = 'Alterar Senha - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key"></i> Alterar Senha
                        </h5>
                        <a href="perfil.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Senha alterada com sucesso!
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> 
                            Por favor, corrija os seguintes erros:
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="senha_atual" 
                                       name="senha_atual" required>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="togglePassword('senha_atual')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="nova_senha" 
                                       name="nova_senha" required 
                                       pattern=".{8,}" 
                                       title="A senha deve ter pelo menos 8 caracteres">
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="togglePassword('nova_senha')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                A senha deve ter pelo menos 8 caracteres.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirmar_senha" class="form-label">Confirmar Nova Senha *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmar_senha" 
                                       name="confirmar_senha" required>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="togglePassword('confirmar_senha')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Alterar Senha
                            </button>
                            <a href="perfil.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-shield-alt"></i> Dicas de Segurança
                    </h6>
                    <ul class="mb-0">
                        <li>Use uma senha forte com pelo menos 8 caracteres</li>
                        <li>Combine letras maiúsculas, minúsculas, números e símbolos</li>
                        <li>Evite usar informações pessoais na senha</li>
                        <li>Não use a mesma senha em diferentes serviços</li>
                        <li>Troque sua senha periodicamente</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação do formulário
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Verifica se as senhas conferem
document.getElementById('confirmar_senha').addEventListener('input', function() {
    const novaSenha = document.getElementById('nova_senha').value;
    const confirmarSenha = this.value;
    
    if (novaSenha !== confirmarSenha) {
        this.setCustomValidity('As senhas não conferem');
    } else {
        this.setCustomValidity('');
    }
});

// Toggle de visualização da senha
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?> 