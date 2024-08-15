<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

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

    // Redirect the user
    header('Location: ../../tabelaServicos.php');
    exit(); // Stop further execution
}
?>
