<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/scripts/perm.php';
?>
require_once 'config.php';
require_once 'scripts/system/audit.php';
require_once 'scripts/system/password_policy.php';


// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$passwordPolicy = new PasswordPolicy($conn);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_STRING);
    $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
    
    // Verifica se as senhas novas são iguais
    if ($newPassword !== $confirmPassword) {
        $errors[] = "As senhas não coincidem";
    } else {
        // Valida a senha atual
        $stmt = mysqli_prepare($conn, "SELECT password FROM login WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['user']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            if (!password_verify($currentPassword, $user['password'])) {
                $errors[] = "Senha atual incorreta";
            } else {
                // Valida a nova senha
                $validation = $passwordPolicy->validatePassword($newPassword);
                if (!$validation['valid']) {
                    $errors = array_merge($errors, $validation['errors']);
                }
                
                // Verifica se a senha já foi usada
                if ($passwordPolicy->isPasswordReused($_SESSION['user'], $newPassword)) {
                    $errors[] = "Esta senha já foi utilizada recentemente";
                }
                
                // Se não houver erros, atualiza a senha
                if (empty($errors)) {
                    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    $stmt = mysqli_prepare($conn, 
                        "UPDATE login SET password = ? WHERE username = ?"
                    );
                    mysqli_stmt_bind_param($stmt, "ss", $passwordHash, $_SESSION['user']);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        // Registra a nova senha no histórico
                        $passwordPolicy->recordPasswordHistory($_SESSION['user'], $newPassword);
                        
                        // Remove flag de alteração forçada
                        unset($_SESSION['force_password_change']);
                        
                        $success = true;
                    } else {
                        $errors[] = "Erro ao atualizar a senha";
                    }
                }
            }
        }
    }
}

// Se for alteração forçada e teve sucesso, redireciona para a home
if ($success && isset($_SESSION['force_password_change'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - SubRider</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Alterar Senha</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Senha alterada com sucesso!
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['force_password_change'])): ?>
            <div class="alert alert-warning">
                Você precisa alterar sua senha para continuar.
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Senha Atual:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Nova Senha:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Nova Senha:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Alterar Senha</button>
                <?php if (!isset($_SESSION['force_password_change'])): ?>
                    <a href="index.php" class="btn btn-secondary">Voltar</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="password-requirements">
            <h3>Requisitos da Senha:</h3>
            <ul>
                <li>Mínimo de <?php echo PasswordPolicy::MIN_LENGTH; ?> caracteres</li>
                <?php if (PasswordPolicy::REQUIRE_UPPERCASE): ?>
                    <li>Pelo menos uma letra maiúscula</li>
                <?php endif; ?>
                <?php if (PasswordPolicy::REQUIRE_LOWERCASE): ?>
                    <li>Pelo menos uma letra minúscula</li>
                <?php endif; ?>
                <?php if (PasswordPolicy::REQUIRE_NUMBERS): ?>
                    <li>Pelo menos um número</li>
                <?php endif; ?>
                <?php if (PasswordPolicy::REQUIRE_SPECIAL): ?>
                    <li>Pelo menos um caractere especial</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html> 