<?php
    require_once("./../connection/connection.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newData = $_POST["newData"];
        $dateArray = explode('/', $newData);
        $day = $dateArray[0];
        $month = $dateArray[1];
        $year = $dateArray[2];
        $newData = $year . "-" . $month . "-" . $day;
        $ordem = $_POST["ordem"];
    
        $update_query = "UPDATE ordem_servicos SET Data = '{$newData}' WHERE Codigo = '{$ordem}'";
        $update_result = mysqli_query($conn, $update_query);
    
        if (!$update_result) {
            echo "Error updating data";
        } else {
            echo "Data updated successfully";
        }
    }
    
?>