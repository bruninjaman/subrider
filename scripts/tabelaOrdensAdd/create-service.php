<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

//CREATE NEW CODE
$mysqli_query = "SELECT Codigo FROM ordem_servicos";
$ordem_result = mysqli_query($conn, $mysqli_query);

//CHECK ORDEMS
$existem_ordems = mysqli_num_rows($ordem_result) > 0 ? true : false;

// Get the current year
$current_year = date("Y");

// CREATE A NEW CODE
$novo_codigo = 100; // Default starting code
$last_year = null; // Variable to store the last year's code

if ($existem_ordems) {
    while ($ordem = mysqli_fetch_assoc($ordem_result)) {
        $ordem_splited = explode("/", $ordem['Codigo']);
        $last_code = $ordem_splited[0]; // The numeric part

        // Only try to access the year part if it exists
        if (isset($ordem_splited[1])) {
            $last_year = $ordem_splited[1]; // The year part

            // Check if the year is the same as the current year
            if ($last_year == $current_year && $last_code > $novo_codigo) {
                $novo_codigo = $last_code; // Update to the highest code for this year
            }
        }
    }
}

// If no codes exist for the current year, start from 100
if ($last_year != $current_year || !$existem_ordems) {
    $novo_codigo = 100;
}

// Increment the code and add the year
$novo_codigo = ($novo_codigo + 1) . '/' . $current_year;

//$novo_codigo = $novo_codigo+1 . '/' . $current_year;


$mysqli_query = "INSERT INTO ordem_servicos ";
$mysqli_query .= "(Codigo, motoID, KM, Data, proprietario_ordem) ";
$mysqli_query .= "VALUES ('" . $novo_codigo . "', '" . $_POST["motoid"] . "', '" . $_POST["km"] . "', '" . $_POST["data"] . "', '" . $_POST["proprietario_ordem"] . "') ";
//CREATE SERVICE

mysqli_query($conn, $mysqli_query);
mysqli_close($conn);
header('location: ../../tabelaOrdens.php?ordem=' . $novo_codigo);
