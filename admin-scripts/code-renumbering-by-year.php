<!-- <?php
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
// PERM
require_once("./../scripts/perm.php");

// CONNECTION
require_once("./../connection/connection.php");

echo "Starting script...\n";

// Check if the script has already been run for the current year
$current_year = date('Y');
$check_query = "SELECT * FROM ordem_servicos WHERE Codigo LIKE '100/$current_year'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    die("Error: The script has already been run for the year $current_year. Aborting to prevent duplication.\n");
}

echo "Proceeding with renumbering for the year $current_year.\n";

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Fetch all records grouped by year
    $query = "SELECT * FROM ordem_servicos ORDER BY Codigo";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($conn));
    }

    echo "Fetched all records from ordem_servicos table.\n";

    // Initialize variables
    $current_year = '';
    $current_number = 100;

    // Loop through all records
    while ($ordem = mysqli_fetch_assoc($result)) {
        // Split the 'Codigo' to get the number and year
        $codigo_split = explode("/", $ordem['Codigo']);
        $numero = $codigo_split[0];
        $year = $codigo_split[1];

        echo "Processing record ID: " . $ordem['servID'] . " - Current Code: " . $ordem['Codigo'] . "\n";

        // If the year changes, reset the numbering to 100
        if ($current_year != $year) {
            echo "Year changed to " . $year . ". Resetting number to 100.\n";
            $current_year = $year;
            $current_number = 100; // Reset numbering to 100
        }

        // Construct the new code
        $novo_codigo = $current_number . '/' . $year;
        echo "New Code for ID " . $ordem['servID'] . ": " . $novo_codigo . "\n";

        // Update the record in the database
        $update_query = "UPDATE ordem_servicos SET Codigo = '$novo_codigo' WHERE servID = " . $ordem['servID'];
        if (!mysqli_query($conn, $update_query)) {
            throw new Exception("Error updating record ID " . $ordem['servID'] . ": " . mysqli_error($conn));
        }

        // Increment the number for the next record of the same year
        $current_number++;
    }

    // Commit transaction
    mysqli_commit($conn);
    echo "Database updated successfully!\n";

} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    echo "Transaction rolled back due to an error: " . $e->getMessage() . "\n";
}

// Close connection
mysqli_close($conn);

?> -->
