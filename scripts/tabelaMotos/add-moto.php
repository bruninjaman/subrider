<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_FILES["foto"])) {
    //Upload picture securely
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    $file_path = "../../upload/moto/";

    // Validate and upload photo
    $foto = uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path);
    if (!$foto) {
        die("Error: Failed to upload photo.");
    }

    // Remove relative path
    $foto = trim($foto, "../../");

    // Validate other input data (example validations)
    $endereco = $_POST['endereco'];
    $ano = $_POST['ano'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $placa = $_POST['placa'];
    $km = $_POST['KM'];
    $proprietario = $_POST['proprietario'];

    // Perform additional input validation here (e.g., validate year, kilometer, etc.)

    // Prepare the SQL statement with placeholders
    $mysqli_query = "INSERT INTO motocicletas (foto, endereco, ano, modelo, marca, placa, KM, proprietario) ";
    $mysqli_query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Initialize a prepared statement
    $stmt = mysqli_prepare($conn, $mysqli_query);

    // Check if the statement preparation was successful
    if ($stmt) {
        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "ssssssss", $foto, $endereco, $ano, $modelo, $marca, $placa, $km, $proprietario);

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
