<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

// Check if motoID is set in the GET request
if (isset($_GET['motoID'])) {
    $motoID = $_GET['motoID'];

    // Prepare the SQL statement with a placeholder
    $mysqli_query = "DELETE FROM motocicletas WHERE motoId = ?";

    // Initialize a prepared statement
    $stmt = mysqli_prepare($conn, $mysqli_query);

    // Check if the statement preparation was successful
    if ($stmt) {
        // Bind the parameter to the prepared statement
        mysqli_stmt_bind_param($stmt, "i", $motoID);

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Close the prepared statement
            mysqli_stmt_close($stmt);

            // Close the database connection
            mysqli_close($conn);

            // Redirect the user
            header('Location: ../../tabelaMotos.php');
            exit; // Exit after redirection to prevent further execution
        } else {
            // Handle the execution error, for example:
            die("Error: " . mysqli_error($conn));
        }
    } else {
        // Handle the statement preparation error, for example:
        die("Error: " . mysqli_error($conn));
    }
}
?>
