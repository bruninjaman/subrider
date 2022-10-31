<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

if (isset($_POST['item'])) {
    $item = $_POST['item'];
    $ordem = $_POST['ordem'];
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $servicoID= $_POST['servicoID'];

    $mysqli_query = " UPDATE servicos "; //UPDATE
    $mysqli_query .= " SET item = '{$item}', ";
    $mysqli_query .= " ordem = '{$ordem}', tipo = '{$tipo}', valor = '{$valor}' ";
    $mysqli_query .= " WHERE servicoId = '" . $servicoID . "'";

    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../tabelaServicos.php');
}
?>