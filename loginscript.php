<?php
   if (isset($_POST["user"])) {
      	if (($_POST["user"] == "xandov") and $_POST["pass"] == "1000net500") {
			session_start();
			$_SESSION["user"] = "xandov";
			header('Location: index.php');
      	}
		else
		{
		  header('Location: login.php');
		}
      }
   ?>