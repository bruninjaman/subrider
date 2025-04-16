<?php
// Adiciona config
// Caminho absoluto para config.php
require_once(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.php'); 
session_start();

//PERM
// Caminhos corrigidos
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "perm.php");
//CONNECTION
require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php");
//FUNCTIONS
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "functions.php");

$id = $_GET["ordemID"];
$codigo = $_GET["Ordem"]; // Assume que o 'codigo' seja passado pela URL também

// !!! ALERTA DE SEGURANÇA: Este código é vulnerável a SQL Injection !!!
// !!! Recomenda-se usar prepared statements em vez de concatenação direta !!!

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
// Redirecionamento corrigido
header('Location: ' . PROJECT_ROOT_URL . '/tabelaOrdens.php');

?>
