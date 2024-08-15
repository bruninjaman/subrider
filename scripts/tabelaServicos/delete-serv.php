<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

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
    
    header('Location: ../../tabelaServicos.php');
} else {
    // Redirect to an error page or handle the error in some other way
    header('Location: ../../error.php');
    exit(); // Stop further execution
}
?>
