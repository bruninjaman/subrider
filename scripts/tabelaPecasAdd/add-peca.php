<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_FILES["foto"])) {
    //Upload picture
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    $file_path = "../../upload/peca/";
    //upload
    $foto = uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path);
    //remove relative path
    $foto = trim($foto,"../../");
    
    $grupo = $_POST['grupo'];
    $item = $_POST['item'];
    $quantidade = $_POST['quantidade'];
    $parte = $_POST['parte'];
    $valor = $_POST['valor'];

    $mysqli_query = "INSERT INTO pecas (foto,grupo,item,quantidade,parte,valor) ";
    $mysqli_query .= "VALUES ('{$foto}','{$grupo}', ";
    $mysqli_query .= "'{$item}','{$quantidade}','{$parte}','{$valor}'";
    $mysqli_query .= ") ";
    
    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../tabelaPecas.php');
}
?>