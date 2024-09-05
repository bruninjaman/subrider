<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_POST['tipo_item'])) {
    switch($_POST['tipo_item']) {
        case 'pecas':
            $categoria = 2;
            //SELECT PECA
            $sql_query = "SELECT * FROM pecas ";
            $sql_query .= "WHERE pecas.pecaId = ". $_POST['pecaid'];
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
            
            $mysqli_query = "INSERT INTO item_ordem (Foto,Grupo,Tipo,Item,Parte,Quantidade,Valor,Descricao,Ordem,Categoria,Codigo) ";
            $mysqli_query .= " VALUES ";
            $mysqli_query .= " ('{$foto}','{$grupo}','{$tipo}','{$item}','{$parte}','{$quantidade}','{$valor}','{$descricao}','{$ordem}','{$categoria}','{$scode}')";
            break;
        case 'service':
            $categoria = 1;
            //SELECT Serviço
            $sql_query = "SELECT * FROM servicos ";
            $sql_query .= "WHERE servicos.servicoId = ". $_POST['servicoid'];
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
            
            $mysqli_query = "INSERT INTO item_ordem (Foto,Grupo,Tipo,Item,Parte,Quantidade,Valor,Descricao,Ordem,Categoria) ";
            $mysqli_query .= " VALUES ";
            $mysqli_query .= " ('{$foto}','{$grupo}','{$tipo}','{$item}','{$parte}','{$quantidade}','{$valor}','{$descricao}','{$ordem}','{$categoria}')";
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
            
            $mysqli_query = "INSERT INTO item_ordem (Foto,Grupo,Tipo,Item,Parte,Quantidade,Valor,Descricao,Ordem,Categoria) ";
            $mysqli_query .= " VALUES ";
            $mysqli_query .= " ('{$foto}','{$grupo}','{$tipo}','{$item}','{$parte}','{$quantidade}','{$valor}','{$descricao}','{$ordem}','{$categoria}')";
            break;
    }

    var_dump($mysqli_query);
    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../ordemservico.php?ordem='. $_GET['ordem']);
}
?>

