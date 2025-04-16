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

if (isset($_POST['item'])) {
    // Sanitize and validate input
    $item = $_POST['item'];
    $tipo = $_POST['tipo'];
    $servicoID = $_POST['servicoID'];

    // Prepare the SQL statement using a prepared statement
    $stmt = $conn->prepare("UPDATE servicos SET item = ?, tipo = ? WHERE servicoId = ?");

    // Bind parameters to the statement
    $stmt->bind_param("ssi", $item, $tipo, $servicoID);

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
