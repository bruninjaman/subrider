<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_FILES["foto"])) {
    //Upload picture
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    $file_path = "../../upload/peca/";
    //upload
    $foto = uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path);
    //remove relative path
    $foto = trim($foto,"../../");
    
    $grupo = $_POST['grupo'];
    $item = $_POST['item'];
    $parte = $_POST['parte'];

    // Prepare the SQL statement using a prepared statement
    $stmt = $conn->prepare("INSERT INTO pecas (foto, grupo, item, parte) VALUES (?, ?, ?, ?)");
    // Bind parameters to the statement
    $stmt->bind_param("ssss", $foto, $grupo, $item, $parte);
    // Execute the statement
    $stmt->execute();
    
    // Close the statement
    $stmt->close();
    // Close the connection
    mysqli_close($conn);
    header('Location: ../../tabelaPecas.php');
}
?>
