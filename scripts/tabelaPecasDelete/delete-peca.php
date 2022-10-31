<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");


$mysqli_query = "DELETE FROM pecas ";
$mysqli_query .= "WHERE pecaId = ". $_GET['pecaID'];
mysqli_query($conn, $mysqli_query);
mysqli_close($conn);
header('Location: ../../tabelaPecas.php');
?>