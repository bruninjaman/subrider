<?php
session_start();

//PERM
require("../../../../assets/php/scripts/perm.php");
//CONNECTION
require("../../../../connection/connection.php");
//FUNCTIONS
require("../../../../assets/php/scripts/functions.php");

header('location: ../../../../services.php?ordem='. $_GET['ordem']);
?>