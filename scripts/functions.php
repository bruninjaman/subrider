<?php
function pagination($conn, $sql_query)
{
?>
    <link rel="stylesheet" href="./assets/css/pagination.css">
    <?php
    $result = mysqli_query($conn, $sql_query);
    $number_of_page = ceil(mysqli_num_rows($result) / 5);
    ?>
    <div class="pagination-style">
        <ul class="pagination">
            <form method="get" class="pagination">
                <?php
                if (isset($_GET['page']) ? $_GET['page'] > 1 : false) {
                ?>
                    <form method="get" class="pagination">
                        <?php
                        if (isset($_GET['page'])) {
                        ?>
                            <input type="hidden" name="page" value="<?php echo $_GET['page'] - 1 ?>">
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['selectPesquisa'])) {
                        ?>
                            <input type="hidden" name="selectPesquisa" value="<?php echo $_GET['selectPesquisa'] ?>">
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['pesquisa'])) {
                        ?>
                            <input type="hidden" name="pesquisa" value="<?php echo $_GET['pesquisa'] ?>">
                        <?php
                        }
                        ?>
                        <li><a onclick="this.closest('form').submit();return false;">«</a></li>
                    </form>
                <?php
                }
                //display the link of the pages in URL  
                $page = isset($_GET['page']) ? $_GET['page'] : 0;

                //maxpagenumber
                $maxPagenumber = 0;

                for ($page = $_GET["page"]; $page <= $number_of_page; $page++) {
                    if($maxPagenumber >= 5)
                        break;
                    //echo '<li><a href = "' . $_SERVER['PHP_SELF'] . '?page=' . $page . '">' . $page . ' </a></li>';
                    //another form for page
                    //echo '<form method="get" action=""><input type="hidden" name="page" value='.$page.'><li><a onclick="this.closest('."'form'".').submit();return false;">'.$page.' </a></li></form>';
                ?>
                    <form method="get" class="pagination">
                        <?php
                        if (isset($page)) {
                        ?>
                            <input type="hidden" name="page" value="<?php echo $page ?>">
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['selectPesquisa'])) {
                        ?>
                            <input type="hidden" name="selectPesquisa" value="<?php echo $_GET['selectPesquisa'] ?>">
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['pesquisa'])) {
                        ?>
                            <input type="hidden" name="pesquisa" value="<?php echo $_GET['pesquisa'] ?>">
                        <?php
                        }
                        ?>
                        <li><a onclick="this.closest('form').submit();return false;"><?php echo $page ?></a></li>
                    </form>
                <?php
                    $maxPagenumber++; 
                }
                ?>
                <?php
                if (!isset($_GET['page']) || $_GET['page'] < $number_of_page) {
                ?>
                    <form method="get" class="pagination">
                        <?php
                        if (isset($_GET['page'])) {
                        ?>
                            <input type="hidden" name="page" value="<?php echo $_GET['page'] + 1 ?>">
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['selectPesquisa'])) {
                        ?>
                            <input type="hidden" name="selectPesquisa" value="<?php echo $_GET['selectPesquisa'] ?>">
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['pesquisa'])) {
                        ?>
                            <input type="hidden" name="pesquisa" value="<?php echo $_GET['pesquisa'] ?>">
                        <?php
                        }
                        ?>
                        <li><a onclick="this.closest('form').submit();return false;">»</a></li>
                    </form>
                <?php
                }
                ?>
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