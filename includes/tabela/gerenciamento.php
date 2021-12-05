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

        //redirect to edit/remove/add page
        $URLedit = "edit.php?motoID=".$row["motoID"];
        $URLremove= "delete.php?motoID=".$row["motoID"];
        $URLadd = "add.php?motoID=".$row["motoID"];

        echo "<th> <i class='fas fa-plus-square'></i><a href='".$URLedit."'> Add</a>";
        echo " <i class='fas fa-minus-circle'></i> <a href='".$URLremove."' >Delete</a>";
        echo " <i class='fas fa-pencil-alt'></i> <a href='".$URLadd."'>Edit</a> </th><break>";
        echo "</tr>";
    }
    echo "</table>";
?>