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

    $mysqli_query = "INSERT INTO motocicletas (foto,endereco,ano,modelo,marca,placa,KM,proprietario) "; //UPDATE
    $mysqli_query .= "VALUES ('{$foto}','{$endereco}', ";
    $mysqli_query .= "'{$ano}','{$modelo}','{$marca}','{$placa}','{$km}','{$proprietario}'";
    $mysqli_query .= ") ";
    mysqli_query($conn, $mysqli_query);
    header('Location: ../../../../tabelaMotos.php');
}
?>