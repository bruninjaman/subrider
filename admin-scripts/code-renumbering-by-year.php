<?php
/**
 * This script renumbers the "Codigo" field in the "ordem_servicos" table,
 * ensuring that for each year, the numbering starts from 100 and increments sequentially.
 *
 * The script performs the following steps:
 * 1. Fetches all records from the "ordem_servicos" table, ordered by the "Codigo" field.
 * 2. For each record, it splits the "Codigo" field into two parts: the numeric code and the year.
 * 3. If the year changes, the numeric code is reset to 100 for the new year.
 * 4. The script updates the "Codigo" field for each record, ensuring the correct sequence.
 *
 * Important: Make sure to back up your data before running this script as it directly modifies the "Codigo" field in the database.
 */

 session_start();
//PERM
require_once("./../scripts/perm.php");

//CONNECTION
require_once("./../connection/connection.php");

// Fetch all records grouped by year
$query = "SELECT * FROM ordem_servicos ORDER BY Codigo";
$result = mysqli_query($conn, $query);

// Initialize variables
$current_year = '';
$current_number = 100;
$ordem_servicos = [];

// Loop through all records
while ($ordem = mysqli_fetch_assoc($result)) {
    // Split the 'Codigo' to get the number and year
    $codigo_split = explode("/", $ordem['Codigo']);
    $numero = $codigo_split[0];
    $year = $codigo_split[1];

    // If the year changes, reset the numbering to 100
    if ($current_year != $year) {
        $current_year = $year;
        $current_number = 100; // Reset numbering to 100
    }

    // Construct the new code
    $novo_codigo = $current_number . '/' . $year;

    // Update the record in the database
    $update_query = "UPDATE ordem_servicos SET Codigo = '$novo_codigo' WHERE servID = " . $ordem['servID'];
    mysqli_query($conn, $update_query);

    // Increment the number for the next record of the same year
    $current_number++;
}

echo "Database updated successfully!";
?>