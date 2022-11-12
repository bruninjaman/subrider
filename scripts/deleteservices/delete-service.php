<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

$id = $_GET["id"];

$mysqli_query = " DELETE FROM item_ordem ";
$mysqli_query .= " WHERE item_ordemID = '{$id}'";

mysqli_query($conn, $mysqli_query);
mysqli_close($conn);
header('Location: ../../services.php?ordem='. $_GET['ordem']);

?>