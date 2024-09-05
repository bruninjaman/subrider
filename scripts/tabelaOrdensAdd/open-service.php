<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../../scripts/functions.php");

mysqli_close($conn);
header('location: ../../services.php?ordem='. $_GET['ordem']);
?>