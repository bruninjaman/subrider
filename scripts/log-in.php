<?php
//CONNECTION
require_once("../connection/connection.php");
//FUNCTIONS
require_once("functions.php");

// Configuração da sessão para 30 dias
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
session_set_cookie_params(30 * 24 * 60 * 60); // 30 dias em segundos

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        header("Location: ../login.php?error=invalid_input");
        exit();
    }
} else {
    // Not a POST request
    mysqli_close($conn);
    header("Location: ../login.php");
    exit();
}
?>