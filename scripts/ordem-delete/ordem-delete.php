<?php
require_once(__DIR__ . "/../config.php");

$id = $_GET["id"];

$mysqli_query = " DELETE FROM item_ordem ";
$mysqli_query .= " WHERE item_ordemID = '{$id}'";

mysqli_query($conn, $mysqli_query);
mysqli_close($conn);
header('Location: ../../ordemservico.php?ordem='. $_GET['ordem']);

?>