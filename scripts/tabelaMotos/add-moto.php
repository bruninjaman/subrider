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
    $file_path = "../../upload/moto/";
    //upload
    $foto = uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path);
    //remove relative path
    $foto = trim($foto,"../../");
    
    $endereco = $_POST['endereco'];
    $ano = $_POST['ano'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $placa = $_POST['placa'];
    $km = $_POST['KM'];
    $proprietario = $_POST['proprietario'];

    $mysqli_query = "INSERT INTO motocicletas (foto,endereco,ano,modelo,marca,placa,KM,proprietario) ";
    $mysqli_query .= "VALUES ('{$foto}','{$endereco}', ";
    $mysqli_query .= "'{$ano}','{$modelo}','{$marca}','{$placa}','{$km}','{$proprietario}'";
    $mysqli_query .= ") ";
    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../tabelaMotos.php');
}
?>