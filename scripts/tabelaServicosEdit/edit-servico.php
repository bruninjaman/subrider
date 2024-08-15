<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

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

    // Redirect the user
    header('Location: ../../tabelaServicos.php');
    exit(); // Stop further execution
}
?>
