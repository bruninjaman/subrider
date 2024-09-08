<?php

function pagination($conn, $sql_query, $results_per_page = 5)
{
    try {
        // Check for "page" parameter in query string
        if (isset($_GET['page'])) {
            $current_page = intval($_GET['page']);
        } else {
            $current_page = 1; // Default to first page
        }

        // Execute SQL query and get result
        $result = mysqli_query($conn, $sql_query);

        // Check for errors in database query
        if (!$result) {
            throw new Exception("Error executing database query: " . mysqli_error($conn));
        }

        // Calculate number of pages needed
        $num_rows = mysqli_num_rows($result);
        $num_pages = ceil($num_rows / $results_per_page);

        // Adjust current page if it's out of bounds
        if ($current_page < 1) $current_page = 1;
        if ($current_page > $num_pages) $current_page = $num_pages;

        // Limit results based on current page
        $offset = ($current_page - 1) * $results_per_page;
        $limited_sql_query = $sql_query . " LIMIT $offset, $results_per_page";
        $limited_result = mysqli_query($conn, $limited_sql_query);

        // Check for errors in database query (for limited results)
        if (!$limited_result) {
            throw new Exception("Error executing limited database query: " . mysqli_error($conn));
        }

        // Generate pagination interface
        ?>
        <div class="pagination-style">
            <?php if ($current_page > 1) : ?>
                <!-- Move to the first page -->
                <button type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=1'">« First</button>
                <button type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $current_page - 1; ?>'">‹ Prev</button>
            <?php endif; ?>

            <!-- Show page numbers -->
            <?php for ($i = max(1, $current_page - 2); $i <= min($num_pages, $current_page + 2); $i++) : ?>
                <button type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $i; ?>'"
                    <?php if ($i == $current_page) echo 'style="font-weight:bold;"'; ?>>
                    <?php echo $i; ?>
                </button>
            <?php endfor; ?>

            <?php if ($current_page < $num_pages) : ?>
                <!-- Move to the last page -->
                <button type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $current_page + 1; ?>'">Next ›</button>
                <button type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $num_pages; ?>'">Last »</button>
            <?php endif; ?>
        </div>
        <?php

    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}





function login($user, $password, $conn)
{
    //check if password and name exist on db
    $mysqli_query = "SELECT * FROM login WHERE username = '{$user}' AND password = '{$password}'";
    $result = mysqli_query($conn, $mysqli_query);
    session_start();
    $rows = mysqli_num_rows($result);
    $result = mysqli_fetch_assoc($result);
    if ($rows > 0) {
        //set sesions
        $_SESSION["user"] = $result["username"];
        $_SESSION["type"] = $result["userType"];
        header("location: ../index.php");
    } else {
        // NO LOGIN
        header("location: ../login.php");
    }
}

function uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path) {
    // Ensure file size is not too large
    if ($fotoSize > 800000) {
        echo "Sorry, your file is too large.";
        return false;
    }

    // Ensure file name is unique by adding a unique ID
    $tmp_name = "";
    $tmp_name = uniqid($tmp_name, true);
    $tmp_name = $tmp_name . $fotoName;

    // Get file destination
    $file_destination = $file_path . "" . $tmp_name;

    // Check if file already exists at destination
    if (file_exists($file_destination)) {
        echo "Sorry, file already exists.";
        return false;
    }

    // Attempt to move uploaded file to destination
    if (move_uploaded_file($fotoTmpname, $file_destination)) {
        return $file_destination;
    } else {
        echo "Sorry, there was an error uploading your file.";
        return false;
    }
}

function realFormat($valor)
{ //Formato Real
    return 'R$' . number_format($valor, 2, ',', '.');
}
function KMFormat($valor)
{ //Formato Real
    return number_format($valor, 0, ',', '.') . "km";
}
?>