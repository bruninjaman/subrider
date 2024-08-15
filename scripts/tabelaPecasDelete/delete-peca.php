<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

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
    
    header('Location: ../../tabelaPecas.php');
} else {
    // Redirect to an error page or handle the error in some other way
    header('Location: ../../error.php');
    exit(); // Stop further execution
}
?>
