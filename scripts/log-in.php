<?php
//CONNECTION
require_once("../connection/connection.php");
//FUNCTIONS
require_once("functions.php");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate that required fields exist
    if (isset($_POST["user"]) && isset($_POST["pass"]) && !empty($_POST["user"]) && !empty($_POST["pass"])) {
        // Use prepared statements in your login function
        login(htmlspecialchars($_POST["user"]), $_POST["pass"], $conn);
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