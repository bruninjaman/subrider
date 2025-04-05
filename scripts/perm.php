<?php
if (!isset($_SESSION["type"])) {
    header("Location: /subrider/login.php");
    exit();
} else {
    if($_SESSION["type"] < 1) {
        header("Location: /subrider/login.php");
        exit();
    }
}