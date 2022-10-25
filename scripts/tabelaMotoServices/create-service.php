<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

//CREATE NEW CODE
$mysqli_query = "SELECT Codigo FROM ordem_servicos ";
$ordem_result = mysqli_query($conn, $mysqli_query);

//CHECK ORDEMS
$existem_ordems = mysqli_num_rows($ordem_result) > 0 ? true : false;

//CREATE A NEW CODE
$novo_codigo = 100;
if ($existem_ordems) {
    while($ordem = mysqli_fetch_assoc($ordem_result)) {
        $ordem_splited = explode("/",$ordem['Codigo']);
        if($ordem_splited[0] > $novo_codigo)
            $novo_codigo = $ordem_splited[0];
    }
} else {
    $novo_codigo = '100' . '/' . date("Y");
}
$novo_codigo = $novo_codigo+1 . '/' . date("Y");


$mysqli_query = "INSERT INTO ordem_servicos ";
$mysqli_query .= "(Codigo, motoID) ";
$mysqli_query .= "VALUE ('" . $novo_codigo . "','". $_GET["motoID"] ."') ";
//CREATE SERVICE

mysqli_query($conn,$mysqli_query);
header('location: ../../services.php?ordem='. $novo_codigo);
?>