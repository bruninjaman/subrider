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
            if (!isset($_POST['pecaid']) || !is_numeric($_POST['pecaid'])) {
                die("ID da peça inválido");
            }
            
            $categoria = 2;
            //SELECT PECA
            $sql_query = "SELECT * FROM pecas WHERE pecas.pecaId = ?";
            $stmt = mysqli_prepare($conn, $sql_query);
            mysqli_stmt_bind_param($stmt, "i", $_POST['pecaid']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($peca = mysqli_fetch_assoc($result)) {
                $foto = $peca['foto'];
                $grupo = $peca['grupo'];
                $tipo = 0;
                $item = $peca['item'];
                $parte = $peca['parte'];
                $quantidade = isset($_POST['pquantidade']) ? $_POST['pquantidade'] : 0;
                $valor = isset($_POST['pvalor']) ? $_POST['pvalor'] : 0;
                $descricao = 0;
                $ordem = isset($_GET["ordem"]) ? $_GET["ordem"] : 0;            
                $scode = isset($_POST['scode']) ? $_POST['scode'] : '';
                
                $insert_query = "INSERT INTO item_ordem (Foto,Grupo,Tipo,Item,Parte,Quantidade,Valor,Descricao,Ordem,Categoria,Codigo) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "ssissddisis", $foto, $grupo, $tipo, $item, $parte, $quantidade, $valor, $descricao, $ordem, $categoria, $scode);
                mysqli_stmt_execute($stmt);
            }
            break;
            
        case 'service':
            if (!isset($_POST['servicoid']) || !is_numeric($_POST['servicoid'])) {
                die("ID do serviço inválido");
            }
            
            $categoria = 1;
            $sql_query = "SELECT * FROM servicos WHERE servicos.servicoId = ?";
            $stmt = mysqli_prepare($conn, $sql_query);
            mysqli_stmt_bind_param($stmt, "i", $_POST['servicoid']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($servico = mysqli_fetch_assoc($result)) {
                $foto = 0;
                $grupo = 0;
                $tipo = $servico['tipo'];
                $item = $servico['item'];
                $parte = 0;
                $quantidade = isset($_POST['squantidade']) ? $_POST['squantidade'] : 0;
                $valor = isset($_POST['svalor']) ? $_POST['svalor'] : 0;
                $descricao = 0;
                $ordem = isset($_GET["ordem"]) ? $_GET["ordem"] : 0;
                
                $insert_query = "INSERT INTO item_ordem (Foto,Grupo,Tipo,Item,Parte,Quantidade,Valor,Descricao,Ordem,Categoria) VALUES (?,?,?,?,?,?,?,?,?,?)";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "ssissddisi", $foto, $grupo, $tipo, $item, $parte, $quantidade, $valor, $descricao, $ordem, $categoria);
                mysqli_stmt_execute($stmt);
            }
            break;
            
        case 'adiantamento':
            $categoria = 3;
            $foto = 0;
            $grupo = 0;
            $tipo = 0;
            $item = 0;
            $parte = 0;
            $quantidade = 1;
            $valor = isset($_POST['avalor']) ? $_POST['avalor'] : 0;
            $descricao = isset($_POST['aitem']) ? $_POST['aitem'] : '';
            $ordem = isset($_GET["ordem"]) ? $_GET["ordem"] : 0;
            
            $insert_query = "INSERT INTO item_ordem (Foto,Grupo,Tipo,Item,Parte,Quantidade,Valor,Descricao,Ordem,Categoria) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "ssissddisi", $foto, $grupo, $tipo, $item, $parte, $quantidade, $valor, $descricao, $ordem, $categoria);
            mysqli_stmt_execute($stmt);
            break;
    }

    mysqli_close($conn);
    header('Location: ../../ordemservico.php?ordem='. $_GET['ordem']);
    exit();
}
?>

