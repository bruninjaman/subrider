<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");


$mysqli_query = "UPDATE ordem_servicos ";
$mysqli_query .= " SET motoID = '". $_POST["motoid"] ."', KM = '". $_POST["km"] ."', Data = '". $_POST["data"] ."', proprietario_ordem = '". $_POST["proprietario_ordem"] ."' ";
$mysqli_query .= " WHERE Codigo = '".$_GET['ordem']."' ";
//CREATE SERVICE



mysqli_query($conn,$mysqli_query);
mysqli_close($conn);
header('location: ../../tabelaOrdens.php?ordem='. $_GET['ordem']);
?>