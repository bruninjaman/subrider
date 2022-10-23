<?php
//CONNECTION
require_once("../connection/connection.php");
//FUNCTIONS
require_once("functions.php");

   if (isset($_POST["user"])) {
		login($_POST["user"],$_POST["pass"],$conn);
      }
	  else {
		header("location: ../login.php");
	  }
?>