<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_POST['item'])) {
    
    $item = $_POST['item'];
    $tipo = $_POST['tipo'];

    $mysqli_query = "INSERT INTO servicos (item,tipo) ";
    $mysqli_query .= "VALUES ('{$item}', ";
    $mysqli_query .= "'{$tipo}'";
    $mysqli_query .= ") ";

    var_dump($mysqli_query);
    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../tabelaServicos.php');
}
?>