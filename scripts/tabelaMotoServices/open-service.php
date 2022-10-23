<?php
session_start();

//PERM
require_once("../../../../assets/php/scripts/perm.php");
//CONNECTION
require_once("../../../../connection/connection.php");
//FUNCTIONS
require_once("../../../../assets/php/scripts/functions.php");

header('location: ../../../../services.php?ordem='. $_GET['ordem']);
?>