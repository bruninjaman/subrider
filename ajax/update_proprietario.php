<?php
    require_once("./../connection/connection.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newProprietario = $_POST["newProprietario"];
        $ordem = $_POST["ordem"];
    
        $update_query = "UPDATE ordem_servicos SET proprietario_ordem = '{$newProprietario}' WHERE Codigo = '{$ordem}'";
        $update_result = mysqli_query($conn, $update_query);
    
        if (!$update_result) {
            echo "Error updating data";
        } else {
            echo "Data updated successfully";
        }
    }
    
?>