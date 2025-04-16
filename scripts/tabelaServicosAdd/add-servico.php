<?php
// Adiciona config
// Caminho absoluto para config.php
require_once(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.php'); 
session_start();

//PERM
// Caminhos corrigidos
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "perm.php");
//CONNECTION
require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php");
//FUNCTIONS
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "functions.php");

// Verificar se está em modo de teste
if (isset($_POST['_test_mode']) && $_POST['_test_mode'] === 'true') {
    // Em modo de teste, apenas redirecionar para a página de tabela
    header('Location: ' . PROJECT_ROOT_URL . '/tabelaServicos.php');
    exit();
}

if (isset($_POST['item'])) {
    // Sanitize and validate input
    $item = $_POST['item'];
    $tipo = $_POST['tipo'];

    // Prepare the SQL statement using a prepared statement
    $stmt = $conn->prepare("INSERT INTO servicos (item, tipo) VALUES (?, ?)");

    // Bind parameters to the statement
    $stmt->bind_param("ss", $item, $tipo);

    // Execute the statement
    $stmt->execute();

    // Close the statement
    $stmt->close();

    // Close the connection
    mysqli_close($conn);

    // Redirect the user (caminho corrigido)
    header('Location: ' . PROJECT_ROOT_URL . '/tabelaServicos.php');
    exit(); // Stop further execution
}
?>
