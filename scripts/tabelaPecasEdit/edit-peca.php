<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

if (isset($_POST['grupo'])) {
    //Upload picture
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    $file_path = "../../upload/moto/";

    //upload
    $foto = uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path);
    //remove relative path
    $foto = trim($foto,"../../");

    $grupo = $_POST['grupo'];
    $item = $_POST['item'];
    $ordem = $_POST['ordem'];
    $parte = $_POST['parte'];
    $quantidade = $_POST['quantidade'];
    $valor = $_POST['valor'];
    $pecaId = $_GET['pecaID'];

    if($foto != '') {

    $mysqli_query = "UPDATE pecas "; //UPDATE
    $mysqli_query .= "SET foto = '{$foto}', grupo = '{$grupo}', ";
    $mysqli_query .= "item = '{$item}', ordem = '{$ordem}', parte = '{$parte}', quantidade = '{$quantidade}', valor = '{$valor}' ";
    } else {
        $mysqli_query = "UPDATE pecas "; //UPDATE
        $mysqli_query .= "SET grupo = '{$grupo}', ";
        $mysqli_query .= "item = '{$item}', ordem = '{$ordem}', parte = '{$parte}', quantidade = '{$quantidade}', valor = '{$valor}' ";
    }
    $mysqli_query .= "WHERE pecaId = '" . $pecaId . "'";
    
    mysqli_query($conn, $mysqli_query);
    mysqli_close($conn);
    header('Location: ../../tabelaPecas.php');
}
?>