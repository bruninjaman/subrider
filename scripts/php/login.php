<?php
   if (isset($_POST["user"])) {
      	if (($_POST["user"] == "xandov") and $_POST["pass"] == "1000net500") {
			session_start();
			$_SESSION["user"] = "xandov";
			$_SESSION["type"] = "adm";
			header("location: ../../index.php");
      	}
      }
	  echo "failed to login";
