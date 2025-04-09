<?php
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../scripts/perm.php';
require_once __DIR__ . '/../../src/Controllers/PasswordController.php';

use App\Controllers\PasswordController;

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit();
}

$controller = new PasswordController();
$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaAtual = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_STRING);
    $novaSenha = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirmarSenha = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
    
    $erros = $controller->alterarSenha($senhaAtual, $novaSenha, $confirmarSenha);
    $sucesso = empty($erros);
}

// Se for alteração forçada e teve sucesso, redireciona para a home
if ($sucesso && isset($_SESSION['force_password_change'])) {
    header("Location: /index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - SubRider</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/form.css">
</head>
<body>
    <div id="page-wrapper">
        <?php require("./header.php"); ?>

        <section id="content" class="main">
            <div class="container">
                <h2>Alterar Senha</h2>
                
                <?php if ($sucesso): ?>
                    <div class="alert alert-success">
                        Senha alterada com sucesso!
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro); ?></li>
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
                    <div class="row gtr-uniform">
                        <div class="col-12">
                            <label for="current_password">Senha Atual:</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="col-12">
                            <label for="new_password">Nova Senha:</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="col-12">
                            <label for="confirm_password">Confirmar Nova Senha:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="col-12">
                            <ul class="actions">
                                <li><input type="submit" value="Alterar Senha" class="primary" /></li>
                                <?php if (!isset($_SESSION['force_password_change'])): ?>
                                    <li><input type="button" value="Voltar" onclick="window.location.href='/index.php'" /></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </form>
                
                <div class="password-requirements">
                    <h3>Requisitos da Senha:</h3>
                    <ul>
                        <li>Mínimo de <?php echo \App\Security\PasswordPolicy::MIN_LENGTH; ?> caracteres</li>
                        <?php if (\App\Security\PasswordPolicy::REQUIRE_UPPERCASE): ?>
                            <li>Pelo menos uma letra maiúscula</li>
                        <?php endif; ?>
                        <?php if (\App\Security\PasswordPolicy::REQUIRE_LOWERCASE): ?>
                            <li>Pelo menos uma letra minúscula</li>
                        <?php endif; ?>
                        <?php if (\App\Security\PasswordPolicy::REQUIRE_NUMBERS): ?>
                            <li>Pelo menos um número</li>
                        <?php endif; ?>
                        <?php if (\App\Security\PasswordPolicy::REQUIRE_SPECIAL): ?>
                            <li>Pelo menos um caractere especial</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>

        <?php require("./footer.php"); ?>
    </div>

    <script src="/assets/js/global/jquery.min.js"></script>
    <script src="/assets/js/global/jquery.scrolly.min.js"></script>
    <script src="/assets/js/global/jquery.dropotron.min.js"></script>
    <script src="/assets/js/global/jquery.scrollex.min.js"></script>
    <script src="/assets/js/global/browser.min.js"></script>
    <script src="/assets/js/global/breakpoints.min.js"></script>
    <script src="/assets/js/global/util.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html> 