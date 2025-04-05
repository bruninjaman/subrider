<?php
require_once(__DIR__ . "/../config.php");

mysqli_close($conn);
header('location: ../../ordemservico.php?ordem='. $_GET['ordem']);
?>