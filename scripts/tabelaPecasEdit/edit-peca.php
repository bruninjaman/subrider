<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

if (isset($_POST['grupo'])) {
    // Upload picture
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    $file_path = "../../upload/moto/";

    // Upload
    $foto = uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path);
    // Remove relative path
    $foto = trim($foto, "../../");

    $grupo = $_POST['grupo'];
    $item = $_POST['item'];
    $ordem = $_POST['ordem'];
    $parte = $_POST['parte'];
    $pecaId = $_GET['pecaID'];

    // Prepare the SQL statement using a prepared statement
    if (!empty($fotoName)) {
        $mysqli_query = "UPDATE pecas SET foto = ?, grupo = ?, item = ?, ordem = ?, parte = ? WHERE pecaId = ?";
        $stmt = $conn->prepare($mysqli_query);
        $stmt->bind_param("sssssi", $foto, $grupo, $item, $ordem, $parte, $pecaId);
    } else {
        $mysqli_query = "UPDATE pecas SET grupo = ?, item = ?, ordem = ?, parte = ? WHERE pecaId = ?";
        $stmt = $conn->prepare($mysqli_query);
        $stmt->bind_param("ssssi", $grupo, $item, $ordem, $parte, $pecaId);
    }
    
    // Execute the statement
    $stmt->execute();
    
    // Close the statement
    $stmt->close();
    
    // Close the connection
    mysqli_close($conn);
    
    header('Location: ../../tabelaPecas.php');
}
?>
