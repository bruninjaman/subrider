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

        $grupo = $_POST["pgrupo"];
        $item = $_POST["pitem"];
        $parte = $_POST["pparte"];
        $quantidade = $_POST["pquantidade"];
        $valor = $_POST["pvalor"];

        $mysqli_query = " UPDATE pecas ";
        $mysqli_query .= " SET grupo = '{$grupo}', ";
        $mysqli_query .= " item = '{$item}', ";
        $mysqli_query .= " parte = '{$parte}', ";
        $mysqli_query .= " quantidade = '{$quantidade}', ";
        $mysqli_query .= " valor = '{$valor}' ";
        $mysqli_query .= " WHERE pecaId = '{$id}' ";
    }
    if(isset($_POST['type_servicos'])) {

        $tipo = $_POST["sgrupo"];
        $item = $_POST["sitem"];
        $valor = $_POST["svalor"];
        //Código
        $scode = $_POST['scode'];

        $mysqli_query = " UPDATE servicos ";
        $mysqli_query .= " SET tipo = '{$tipo}', ";
        $mysqli_query .= " item = '{$item}', ";
        $mysqli_query .= " Codigo = '{$scode}', ";
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
    mysqli_close($conn);
    header('Location: ../../services.php?ordem='. $_GET['ordem']);
}

?>