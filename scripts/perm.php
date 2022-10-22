<?php
if (!isset($_SESSION["type"])) {
    header("location: login.php");

} else {
    if($_SESSION["type"]  < 1) {
        header("location: login.php");
    }
}