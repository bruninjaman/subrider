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

// Check if servID is set and is a valid integer
if (isset($_GET['servID']) && is_numeric($_GET['servID'])) {
    // Convert servID to an integer to prevent SQL injection
    $servID = intval($_GET['servID']);

    // Prepare the SQL statement using a prepared statement
    $stmt = $conn->prepare("DELETE FROM servicos WHERE servicoId = ?");
    // Bind parameters to the statement
    $stmt->bind_param("i", $servID);
    // Execute the statement
    $stmt->execute();

    // Close the statement
    $stmt->close();
    
    // Close the connection
    mysqli_close($conn);
    
    // Redirecionamento corrigido
    header('Location: ' . PROJECT_ROOT_URL . '/tabelaServicos.php');
} else {
    // Redirect to an error page or handle the error in some other way
    // Redirecionamento corrigido (assumindo error.php na raiz)
    header('Location: ' . PROJECT_ROOT_URL . '/error.php');
    exit(); // Stop further execution
}
?>
