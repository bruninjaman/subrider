<?php
    $query = "SELECT * ";
    $query .= "FROM moto ";

    $result = mysqli_query($conn,$query);

    echo "<table>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<th>" .$row["cor"] . "\n</th>";
        echo "<th>" .$row["km"] . "\n </th>";
        echo "<th>" .$row["marca"] . "\n </th>";
        echo "<th>" .$row["placa"] . "\n </th>";
        echo "<th>" .$row["modelo"] . " </th>";
        echo "<th> <a> <i class='fas fa-plus-square'></i> Add</a> <i class='fas fa-minus-circle'></i> <a>Delete</a> <i class='fas fa-pencil-alt'></i> <a>Alter</a> </th><break>";
        echo "</tr>";
    }
    echo "</table>";
?>