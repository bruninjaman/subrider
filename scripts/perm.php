<?php
if (!isset($_SESSION["type"])) {
    header("location: index.php");

} else {
    if($_SESSION["type"]  < 1) {
        header("location: index.php");
    }
}