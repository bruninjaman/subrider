<?php
    $query = "SELECT * ";
    $query += "FROM moto ";

    $result = mysqli_query($connection,$query);
?>
<?php 
    while($row = $result) {
        echo $result["cor"]; 
    }
?>