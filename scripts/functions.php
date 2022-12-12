<?php

function pagination($conn, $sql_query, $results_per_page = 5)
{
    // Check for "page" parameter in query string
    if (isset($_GET['page'])) {
        $current_page = intval($_GET['page']);
    } else {
        $current_page = 1;
    }

    // Execute SQL query and get result
    $result = mysqli_query($conn, $sql_query);

    // Calculate number of pages needed
    $num_rows = mysqli_num_rows($result);
    $num_pages = ceil($num_rows / $results_per_page);

    // Calculate start and end page numbers for pagination interface
    $start_page = max($current_page - 2, 1);
    $end_page = min($current_page + 2, $num_pages);

    // Limit results based on current page
    $offset = ($current_page - 1) * $results_per_page;
    $limited_sql_query = $sql_query . " LIMIT $offset, $results_per_page";
    $limited_result = mysqli_query($conn, $limited_sql_query);

    // Check for errors
    if (!$result || !$limited_result) {
        // Handle errors
    }

    // Generate pagination interface
    ?>
    <link rel="stylesheet" href="./assets/css/pagination.css">
    <div class="pagination-style">
        <ul class="pagination">
            <?php if ($current_page > 1): ?>
                <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $current_page - 1; ?>">«</a></li>
            <?php endif; ?>
            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <?php if ($current_page < $num_pages): ?>
                <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $current_page + 1; ?>">»</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php
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

function uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path)
{
    //ADD UNIQUE ID
    $tmp_name = "";
    $tmp_name = uniqid($tmp_name, true);
    $tmp_name = $tmp_name . $fotoName;

    //GET FILE DESTINATION
    $file_destination = $file_path . "" . $tmp_name;

    // if (file_exists($file_destination)) {
    //     echo "Sorry, file already exists.";
    // } else if ($fotoSize > 500000) {
    //     echo "Sorry, your file is too large.";
    // } else {
    //MOVE FILE TO DIRECTORY
    move_uploaded_file($fotoTmpname, $file_destination);
    return $file_destination;
    // };
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