<?php
if (!isset($_SESSION["type"])) {
    header("location: nopermission.php");

} else {
    if($_SESSION["type"]  != "adm") {
        header("location: nopermission.php");
    }
}