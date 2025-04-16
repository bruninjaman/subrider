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

// Check if pecaID is set and is a valid integer
if (isset($_GET['pecaID']) && is_numeric($_GET['pecaID'])) {
    // Convert pecaID to an integer to prevent SQL injection
    $pecaID = intval($_GET['pecaID']);

    // Prepare the SQL statement using a prepared statement
    $stmt = $conn->prepare("DELETE FROM pecas WHERE pecaId = ?");
    // Bind parameters to the statement
    $stmt->bind_param("i", $pecaID);
    // Execute the statement
    $stmt->execute();

    // Close the statement
    $stmt->close();
    
    // Close the connection
    mysqli_close($conn);
    
    // Redirecionamento corrigido
    header('Location: ' . PROJECT_ROOT_URL . '/tabelaPecas.php');
} else {
    // Redirect to an error page or handle the error in some other way
    // Redirecionamento corrigido
    header('Location: ' . PROJECT_ROOT_URL . '/error.php');
    exit(); // Stop further execution
}
?>
