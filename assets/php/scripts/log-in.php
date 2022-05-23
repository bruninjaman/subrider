<?php
//CONNECTION
require("../../../connection/connection.php");
//FUNCTIONS
require("functions.php");

   if (isset($_POST["user"])) {
		login($_POST["user"],$_POST["pass"],$conn);
		header("location: ../../../index.php");
      }
	  else {
	  echo "failed to login";
	  }