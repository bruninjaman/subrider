<?php
require_once("scripts/config.php");

$query = "DESCRIBE ordem_servicos";
$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
    echo "\n";
}

mysqli_close($conn);
?>