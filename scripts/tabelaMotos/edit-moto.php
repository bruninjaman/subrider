<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

if (isset($_POST['endereco'])) {
    //Upload picture
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    $file_path = "upload/moto/";

    //Edit
    $foto = uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path);

    die();
    $endereco = $_POST['endereco'];
    $ano = $_POST['ano'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $placa = $_POST['placa'];
    $km = $_POST['KM'];
    $proprietario = $_POST['proprietario'];
    $motoid= $_POST['motoID'];
    
    if($_POST['foto'] != '') {

    $mysqli_query = "UPDATE motocicletas "; //UPDATE
    $mysqli_query .= "SET foto = '{$foto}', endereco = '{$endereco}', ";
    $mysqli_query .= "ano = '{$ano}', modelo = '{$modelo}', marca = '{$marca}', placa = '{$placa}', km = '{$km}', proprietario = '{$proprietario}' ";
    } else {
        $mysqli_query = "UPDATE motocicletas "; //UPDATE
        $mysqli_query .= "SET endereco = '{$endereco}', ";
        $mysqli_query .= "ano = '{$ano}', modelo = '{$modelo}', marca = '{$marca}', placa = '{$placa}', km = '{$km}', proprietario = '{$proprietario}' ";
    }
    $mysqli_query .= "WHERE motoID = '" . $motoid . "'";
    mysqli_query($conn, $mysqli_query);
    header('Location: ../../tabelaMotos.php');
}
?>