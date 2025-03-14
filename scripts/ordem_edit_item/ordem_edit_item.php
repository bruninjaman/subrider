<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_POST['tipo_item'])) {
    switch ($_POST['tipo_item']) {
        case 'pecas':
            $categoria = 2;
            //SELECT PECA
            $sql_query = "SELECT * FROM pecas ";
            $sql_query .= "WHERE pecas.pecaId = " . $_POST['pecaid'];
            $result = mysqli_query($conn, $sql_query);

            //GIVE RESULTS
            while ($peca = mysqli_fetch_assoc($result)) {
                $foto = $peca['foto'];
                $grupo = $peca['grupo'];
                $tipo = 0;
                $item = $peca['item'];
                $parte = $peca['parte'];
                $quantidade = $_POST['pquantidade'];
                $valor = $_POST['pvalor'];
                $descricao = 0;
                $ordem = $_GET["ordem"];
                //Código
                $scode = $_POST['scode'];
            }
            break;
        case 'service':
            $categoria = 1;
            //SELECT Serviço
            $sql_query = "SELECT * FROM servicos ";
            $sql_query .= "WHERE servicos.servicoId = " . $_POST['servicoid'];
            $result = mysqli_query($conn, $sql_query);

            //GIVE RESULTS
            while ($servico = mysqli_fetch_assoc($result)) {
                $foto = 0;
                $grupo = 0;
                $tipo = $servico['tipo'];
                $item = $servico['item'];
                $parte = 0;
                $quantidade = $_POST['squantidade'];
                $valor = $_POST['svalor'];
                $descricao = 0;
                $ordem = $_GET["ordem"];
            }
            break;
        case 'adiantamento':
            $categoria = 3;
            //GIVE RESULTS
            $foto = 0;
            $grupo = 0;
            $tipo = 0;
            $item = 0;
            $parte = 0;
            $quantidade = 1;
            $valor = $_POST['avalor'];
            $descricao = $_POST['aitem'];;
            $ordem = $_GET["ordem"];
            break;
    }
    $mysqli_query = "UPDATE item_ordem ";
    $mysqli_query .= " SET ";
    $mysqli_query .= " Foto = '{$foto}',Grupo = '{$grupo}',Tipo = '{$tipo}',Item = '{$item}',Parte = '{$parte}',Quantidade = '{$quantidade}',Valor = '{$valor}',Descricao = '{$descricao}',Ordem = '{$ordem}',Categoria = '{$categoria}',Codigo = '{$scode}' ";
    $mysqli_query .= " WHERE item_ordemID = ".$_GET['item_ordemID'];
    //var_dump($mysqli_query);
    //die();
    //update queries
    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../ordemservico.php?ordem=' . $_GET['ordem']);
}
