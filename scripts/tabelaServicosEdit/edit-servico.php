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
    $tipo = $_POST['tipo'];
    $servicoID= $_POST['servicoID'];

    $mysqli_query = " UPDATE servicos "; //UPDATE
    $mysqli_query .= " SET item = '{$item}', ";
    $mysqli_query .= " tipo = '{$tipo}' ";
    $mysqli_query .= " WHERE servicoId = '" . $servicoID . "'";

    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../tabelaServicos.php');
}
?>