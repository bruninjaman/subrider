<?php
session_start();

//PERM
require_once("../../../../assets/php/scripts/perm.php");
//CONNECTION
require_once("../../../../connection/connection.php");
//FUNCTIONS
require_once("../../../../assets/php/scripts/functions.php");


$mysqli_query = "DELETE FROM motocicletas ";
$mysqli_query .= "WHERE motoId = ". $_GET['motoID'];
mysqli_query($conn, $mysqli_query);
header('Location: ../../../../tabelaMotos.php');
?>