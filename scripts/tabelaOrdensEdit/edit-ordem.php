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


// !!! ALERTA DE SEGURANÇA: Este código é vulnerável a SQL Injection !!!
// !!! Recomenda-se usar prepared statements em vez de concatenação direta !!!
$mysqli_query = "UPDATE ordem_servicos ";
$mysqli_query .= " SET motoID = '". $_POST["motoid"] ."', KM = '". $_POST["km"] ."', Data = '". $_POST["data"] ."', proprietario_ordem = '". $_POST["proprietario_ordem"] ."' ";
$mysqli_query .= " WHERE Codigo = '".$_GET['ordem']."' ";
//CREATE SERVICE



mysqli_query($conn,$mysqli_query);
mysqli_close($conn);
// Redirecionamento corrigido
header('Location: ' . PROJECT_ROOT_URL . '/tabelaOrdens.php?ordem='. $_GET['ordem']);
?>