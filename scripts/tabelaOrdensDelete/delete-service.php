<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

$id = $_GET["ordemID"];
$codigo = $_GET["Codigo"]; // Assume que o 'codigo' seja passado pela URL também

// Begin Transaction
mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);

try {
    // Delete from ordem_servicos
    $query_ordem_servicos = "DELETE FROM ordem_servicos WHERE Codigo = '{$codigo}'";
    mysqli_query($conn, $query_ordem_servicos);

    // Delete from item_ordem
    $query_item_ordem = "DELETE FROM item_ordem WHERE Codigo = '{$codigo}'";
    mysqli_query($conn, $query_item_ordem);

    // Delete from pecas
    $query_pecas = "DELETE FROM pecas WHERE ordem = '{$codigo}'";
    mysqli_query($conn, $query_pecas);

    // Delete from servicos
    $query_servicos = "DELETE FROM servicos WHERE ordem = '{$codigo}'";
    mysqli_query($conn, $query_servicos);

    // Commit the transaction
    mysqli_commit($conn);
    
    // Redirect on success
    header('Location: ../../ordemservico.php?ordem=' . $_GET['ordem']);

} catch (Exception $e) {
    // Rollback if there's any error
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($conn);
header('Location: ../../tabelaOrdens.php');

?>
