<?php
if (!isset($_SESSION["type"])) {
    header("location: nopermission.php");

} else {
    if($_SESSION["type"]  < 1) {
        header("location: nopermission.php");
    }
}