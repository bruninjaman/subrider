<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_POST['avalor']) || isset($_POST['svalor']) || isset($_POST['pvalor'])) {
    $id = $_GET["id"];
    if(isset($_POST['type_pecas'])) {

        $grupo = $_POST["grupo"];
        $item = $_POST["item"];
        $parte = $_POST["parte"];
        $quantidade = $_POST["quantidade"];
        $valor = $_POST["pvalor"];

        $mysqli_query = " UPDATE pecas ";
        $mysqli_query .= " SET grupo = '{$grupo}', ";
        $mysqli_query .= " item = '{$item}', ";
        $mysqli_query .= " item = '{$parte}', ";
        $mysqli_query .= " item = '{$quantidade}', ";
        $mysqli_query .= " item = '{$valor}' ";
        $mysqli_query .= " WHERE pecaId = '{$id}' ";
    }
    if(isset($_POST['type_servicos'])) {

        $tipo = $_POST["tipo"];
        $item = $_POST["item"];
        $valor = $_POST["svalor"];

        $mysqli_query = " UPDATE servicos ";
        $mysqli_query .= " SET tipo = '{$tipo}', ";
        $mysqli_query .= " item = '{$item}', ";
        $mysqli_query .= " valor = '{$valor}' ";
        $mysqli_query .= " WHERE servicoId = '{$id}' ";
    }
    if(isset($_POST['type_adiantamento'])) {

        $valor = $_POST["avalor"];
        $descricao = $_POST["aitem"];

        $mysqli_query = " UPDATE adiantamento ";
        $mysqli_query .= " SET valor = '{$valor}', ";
        $mysqli_query .= " descricao = '{$descricao}' ";
        $mysqli_query .= " WHERE IDadiantamento = '{$id}' ";
    }




    mysqli_query($conn, $mysqli_query);
    header('Location: ../../services.php?ordem='. $_GET['ordem']);
}

?>