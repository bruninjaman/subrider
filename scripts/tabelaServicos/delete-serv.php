<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");


$mysqli_query = "DELETE FROM servicos ";
$mysqli_query .= "WHERE servicoId = ". $_GET['servID'];
mysqli_query($conn, $mysqli_query);
mysqli_close($conn);
header('Location: ../../tabelaServicos.php');
?>