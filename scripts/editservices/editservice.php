<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

if (isset($_POST['valor'])) {

    if(isset($_POST['type_pecas'])) {
        $mysqli_query = " UPDATE "
    }




    //var_dump($mysqli_query);
    //mysqli_query($conn, $mysqli_query);
    //header('Location: ../../services.php?ordem='. $_GET['ordem']);
}

?>