<?php
session_start();
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../../classes/StatusOrdem.php");

// Primeiro, verifica se houve mudança de status
$ordem_id = $_GET['ordem'];
$status_manager = new StatusOrdem($conn, $ordem_id);
$status_atual = $status_manager->getStatus();

if ($status_atual != $_POST['status']) {
    $status_manager->atualizarStatus($_POST['status'], $_POST['observacao_status']);
}

// Atualiza os demais dados da ordem
$mysqli_query = "UPDATE ordem_servicos ";
$mysqli_query .= " SET motoID = '". $_POST["motoid"] ."', KM = '". $_POST["km"] ."', Data = '". $_POST["data"] ."', proprietario_ordem = '". $_POST["proprietario_ordem"] ."' ";
$mysqli_query .= " WHERE Codigo = '".$ordem_id."' ";

mysqli_query($conn, $mysqli_query);
mysqli_close($conn);
header('location: ../../tabelaOrdens.php?ordem='. $ordem_id);
?>