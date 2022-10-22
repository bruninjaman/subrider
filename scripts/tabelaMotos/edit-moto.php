<?php
session_start();

//PERM
require("../../../../assets/php/scripts/perm.php");
//CONNECTION
require("../../../../connection/connection.php");
//FUNCTIONS
require("../../../../assets/php/scripts/functions.php");

if (isset($_POST['foto'])) {
    $foto = $_POST['foto'];
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
    var_dump($mysqli_query);
    mysqli_query($conn, $mysqli_query);
    header('Location: ../../../../tabelaMotos.php');
}
?>