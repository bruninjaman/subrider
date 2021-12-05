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
        echo "<th> <a>Add</a> <a>Delete</a> <a>Alter</a> </th><break>";
        echo "</tr>";
    }
    echo "</table>";
?>