<?php
require_once __DIR__ . '/../config.php';

// Busca todos os usuários
$query = "SELECT username, password FROM login";
$result = mysqli_query($conn, $query);

while ($user = mysqli_fetch_assoc($result)) {
    // Gera o hash da senha atual
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
    
    // Atualiza a senha no banco
    $stmt = mysqli_prepare($conn, "UPDATE login SET password = ? WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $user['username']);
    mysqli_stmt_execute($stmt);
    
    echo "Senha atualizada para o usuário: " . $user['username'] . "\n";
}

echo "\nTodas as senhas foram atualizadas com sucesso!\n";

// Após atualizar todas as senhas, vamos atualizar o código de verificação
$loginFile = __DIR__ . '/log-in.php';
$loginContent = file_get_contents($loginFile);

// Substitui o código de verificação temporário pelo código com hash
$search = "// Verificação temporária para senhas em texto puro
            if (\$password === \$user['password']) {";
$replace = "// Verifica a senha usando hash seguro
            if (password_verify(\$password, \$user['password'])) {
                // Verifica se a senha precisa ser alterada
                if (\$passwordPolicy->passwordNeedsChange(\$username)) {
                    // Inicia a sessão com flag para forçar alteração de senha
                    session_start();
                    \$_SESSION['force_password_change'] = true;
                    \$_SESSION['user'] = \$username;
                    \$_SESSION['type'] = \$user['userType'];
                    
                    header(\"Location: /subrider/change_password.php\");
                    exit();
                }";

$newContent = str_replace($search, $replace, $loginContent);
file_put_contents($loginFile, $newContent);

echo "Código de verificação atualizado com sucesso!\n";
?> 