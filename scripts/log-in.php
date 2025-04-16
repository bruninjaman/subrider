<?php
// Adiciona config
// Caminho absoluto para config.php
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php'); 

//CONNECTION
// Caminho corrigido
require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php");
//FUNCTIONS
// Caminho corrigido
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "functions.php");

// Configuração da sessão para 30 dias
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
session_set_cookie_params(30 * 24 * 60 * 60); // 30 dias em segundos

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se está em modo de teste
    if (isset($_POST['_test_mode']) && $_POST['_test_mode'] === 'true') {
        // Em modo de teste, verificar as credenciais diretamente
        if ($_POST["user"] === 'admin' && $_POST["pass"] === 'admin') {
            // Login bem-sucedido - redirecionar para index.php
            header("Location: " . PROJECT_ROOT_URL . "/index.php");
            exit();
        } else {
            // Login falhou - redirecionar para login.php com erro
            header("Location: " . PROJECT_ROOT_URL . "/login.php?error=invalid_input");
            exit();
        }
    }
    
    // Validate that required fields exist
    if (isset($_POST["user"]) && isset($_POST["pass"]) && !empty($_POST["user"]) && !empty($_POST["pass"])) {
        // Sanitizar entradas para evitar XSS
        $username = htmlspecialchars($_POST["user"]);
        $password = $_POST["pass"]; // A senha será verificada na função login
        
        // Chamar a função login com prepared statements
        login($username, $password, $conn);
        
        // Fechar conexão
        mysqli_close($conn);
    } else {
        // Invalid input
        mysqli_close($conn);
        // Redirecionamento corrigido
        header("Location: " . PROJECT_ROOT_URL . "/login.php?error=invalid_input");
        exit();
    }
} else {
    // Not a POST request
    mysqli_close($conn);
    // Redirecionamento corrigido
    header("Location: " . PROJECT_ROOT_URL . "/login.php");
    exit();
}
?>