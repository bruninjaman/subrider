<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

$id = $_GET["ordemID"];
$codigo = $_GET["Ordem"]; // Assume que o 'codigo' seja passado pela URL também

// Begin Transaction
mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);

try {
    // Delete from ordem_servicos
    $query_ordem_servicos = "DELETE FROM ordem_servicos WHERE Codigo = '{$codigo}'";
    mysqli_query($conn, $query_ordem_servicos);

    // Delete from item_ordem
    $query_item_ordem = "DELETE FROM item_ordem WHERE Ordem = '{$codigo}'";
    mysqli_query($conn, $query_item_ordem);

    // Commit the transaction
    mysqli_commit($conn);

} catch (Exception $e) {
    // Rollback if there's any error
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($conn);
header('Location: ../../tabelaOrdens.php');

?>
