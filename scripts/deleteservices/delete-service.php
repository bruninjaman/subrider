<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

$id = $_GET["id"];
$type = $_GET["type"];

switch($type) {
    case 1:
        $mysqli_query = " DELETE FROM pecas ";
        $mysqli_query .= " WHERE pecaId = '{$id}'";
        break;
    case 2:
        $mysqli_query = " DELETE FROM servicos ";
        $mysqli_query .= " WHERE servicoId = '{$id}'";
        break;
    case 3:
        $mysqli_query = " DELETE FROM adiantamento ";
        $mysqli_query .= " WHERE IDadiantamento = '{$id}'";
        break;
}

mysqli_query($conn, $mysqli_query);
header('Location: ../../services.php?ordem='. $_GET['ordem']);

?>