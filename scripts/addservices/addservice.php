<?php
session_start();

//PERM
require("../../../../assets/php/scripts/perm.php");
//CONNECTION
require("../../../../connection/connection.php");
//FUNCTIONS
require("../../../../assets/php/scripts/functions.php");

if (isset($_POST['tipo_item'])) {
    switch($_POST['tipo_item']) {
        case 'pecas':
            $mysqli_query = "INSERT INTO pecas (foto,grupo,item,parte,quantidade,motoID,ordem,valor) ";
            $mysqli_query .= " VALUES ";
            $mysqli_query .= " ('". $_POST['pfoto'] . "',";
            $mysqli_query .= " '". $_POST['pgrupo'] . "',";
            $mysqli_query .= " '". $_POST['pitem'] . "',";
            $mysqli_query .= " '". $_POST['pparte'] . "',";
            $mysqli_query .= " '". $_POST['pquantidade'] . "',";
            $mysqli_query .= " '". $_GET['motoID'] . "',";
            $mysqli_query .= " '". $_GET['ordem'] . "',";
            $mysqli_query .= " '". $_POST['pvalor'] . "')";
            break;
        case 'service':
            $mysqli_query = "INSERT INTO servicos (tipo,item,motoID,ordem,valor) ";
            $mysqli_query .= " VALUES ";
            $mysqli_query .= " ( '". $_POST['sgrupo'] . "',";
            $mysqli_query .= " '". $_POST['sitem'] . "',";
            $mysqli_query .= " '". $_GET['motoID'] . "',";
            $mysqli_query .= " '". $_GET['ordem'] . "',";
            $mysqli_query .= " '". $_POST['svalor'] . "')";
            break;
        case 'adiantamento':
            $mysqli_query = "INSERT INTO adiantamento (descricao,motoID,ordem,valor) ";
            $mysqli_query .= " VALUES ";
            $mysqli_query .= " ( '". $_POST['aitem'] . "',";
            $mysqli_query .= " '". $_GET['motoID'] . "',";
            $mysqli_query .= " '". $_GET['ordem'] . "',";
            $mysqli_query .= " '". $_POST['avalor'] . "')";
            break;
    }
    var_dump($mysqli_query);
    mysqli_query($conn, $mysqli_query);
    header('Location: ../../../../services.php?ordem='. $_GET['ordem']);
}

?>