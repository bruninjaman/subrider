<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_POST['desc'])) {  
    $sql_query = "INSERT INTO specs (motoID,spec_desc,valor)";
    $sql_query .= " VALUES ";
    $motoID = $_GET['motoID'];
    $spec_desc = $_POST['desc'];
    $valor = $_POST['value'];
    $sql_query .= " ('{$motoID}','{$spec_desc}','{$valor}') ";


    
    mysqli_query($conn, $sql_query);
    mysqli_close($conn);
    header('Location: ../../services.php?ordem='. $_GET['ordem']);
}
?>

