<?php
//CONNECTION
require("../../../connection/connection.php");
//FUNCTIONS
require("functions.php");

   if (isset($_POST["user"])) {
		login($_POST["user"],$_POST["pass"],$conn);
      }
	  else {
		header("location: ../../../login.php");
	  }