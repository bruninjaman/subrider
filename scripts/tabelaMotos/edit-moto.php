<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

if (isset($_POST['endereco'])) {
    $motoid = $_POST['motoID'];
    $endereco = $_POST['endereco'];
    $ano = $_POST['ano'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $placa = $_POST['placa'];
    $km = $_POST['KM'];
    $proprietario = $_POST['proprietario'];

    // Check if a new photo is uploaded
    if (!empty($_FILES["foto"]["name"])) {
        //Upload picture
        $fotoName = $_FILES["foto"]["name"];
        $fotoSize = $_FILES["foto"]["size"];
        $fotoTmpname = $_FILES["foto"]["tmp_name"];
        $file_path = "../../upload/moto/";

        //upload
        $foto = uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path);
        //remove relative path
        $foto = trim($foto,"../../");

        // Update the photo along with other fields
        $mysqli_query = "UPDATE motocicletas ";
        $mysqli_query .= "SET foto = '{$foto}', endereco = '{$endereco}', ";
        $mysqli_query .= "ano = '{$ano}', modelo = '{$modelo}', marca = '{$marca}', placa = '{$placa}', km = '{$km}', proprietario = '{$proprietario}' ";
        $mysqli_query .= "WHERE motoID = '" . $motoid . "'";
    } else {
        // Update only other fields without changing the photo
        $mysqli_query = "UPDATE motocicletas ";
        $mysqli_query .= "SET endereco = '{$endereco}', ";
        $mysqli_query .= "ano = '{$ano}', modelo = '{$modelo}', marca = '{$marca}', placa = '{$placa}', km = '{$km}', proprietario = '{$proprietario}' ";
        $mysqli_query .= "WHERE motoID = '" . $motoid . "'";
    }

    // Execute the query
    mysqli_query($conn, $mysqli_query);

    // Close the connection
    mysqli_close($conn);

    // Redirect to the desired page after the update
    header('Location: ../../tabelaMotos.php');
    exit(); // Ensure that script execution stops after redirection
}
?>
