<?php

function getPage()
{
    if (!isset($_GET["page"])) {
        $page = 1;
    } else {
        $page = $_GET["page"];
    }
    return $page;
}

function getTableCount($conn, $mysqli_query)
{
    $result = mysqli_query($conn, $mysqli_query);
    return mysqli_num_rows($result);
}

function addPeca($conn)
{
    if (isset($_POST["parte"])) {
        //UPLOAD
        $tmp_name = "";
        $tmp_name = uniqid($tmp_name, true);
        $tmp_name = $tmp_name . $_FILES['foto']['name'];
        $file_destination = 'fotos/pecas/' . $tmp_name;

        if (file_exists($file_destination)) {
            echo "Sorry, file already exists.";
        } else if ($_FILES['foto']['size'] > 500000) {
            echo "Sorry, your file is too large.";
        } else {
            move_uploaded_file($_FILES['foto']['tmp_name'], $file_destination);
        };



        $mysqli_query = "INSERT INTO pecas (grupo, item, parte, foto)";
        $mysqli_query .= " VALUES ('{$_POST['grupo']}','{$_POST['item']}','{$_POST['parte']}' ,'{$file_destination}')";

        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaPecas.php');
    }
}

function addMoto($conn)
{
    if (isset($_POST["addProp"])) {
        //upload image
        $tmp_name = "";
        $tmp_name = uniqid($tmp_name, true);
        $tmp_name = $tmp_name . $_FILES['foto']['name'];
        $file_destination = 'fotos/motos/' . $tmp_name;

        if (file_exists($file_destination)) {
            echo "Sorry, file already exists.";
        } else if ($_FILES['foto']['size'] > 500000) {
            echo "Sorry, your file is too large.";
        } else {
            move_uploaded_file($_FILES['foto']['tmp_name'], $file_destination);
        };



        $mysqli_query = "INSERT INTO motocicletas (endereco, proprietario, marca, placa, ano, modelo, km, foto)";
        $mysqli_query .= " VALUES ('{$_POST['endereco']}','{$_POST['proprietario']}' ";
        $mysqli_query .= " ,'{$_POST['marca']}','{$_POST['placa']}','{$_POST['ano']}','{$_POST['modelo']}' ";
        $mysqli_query .= " ,'{$_POST['km']}','{$file_destination}')";

        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaMotos.php');
    }
}

function editMoto($conn)
{
    if (isset($_POST["proprietario2"])) {
        //upload image
        $tmp_name = "";
        $tmp_name = uniqid($tmp_name, true);
        $tmp_name = $tmp_name . $_FILES['foto2']['name'];
        $file_destination = 'fotos/motos/' . $tmp_name;

        if (file_exists($file_destination)) {
            echo "Sorry, file already exists.";
        } else if ($_FILES['foto2']['size'] > 500000) {
            echo "Sorry, your file is too large.";
        } else {
            move_uploaded_file($_FILES['foto2']['tmp_name'], $file_destination);
        };



        $mysqli_query = "UPDATE motocicletas";
        $mysqli_query .= " SET endereco = '{$_POST['endereco2']}', proprietario = '{$_POST['proprietario2']}',marca = '{$_POST['marca2']}',placa = '{$_POST['placa2']}',";
        $mysqli_query .= " ano = '{$_POST['ano2']}', modelo = '{$_POST['modelo2']}',km = '{$_POST['km2']}', foto = '{$file_destination}'";
        $mysqli_query .= " WHERE ";
        $mysqli_query .= " motoID = {$_POST['motoid2']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaMotos.php');
    }
}

function editPeca($conn)
{
    if (isset($_POST["parte2"])) {
        //UPLOAD
        $tmp_name = "";
        $tmp_name = uniqid($tmp_name, true);
        $tmp_name = $tmp_name . $_FILES['foto2']['name'];
        $file_destination = 'fotos/pecas/' . $tmp_name;

        if (file_exists($file_destination)) {
            echo "Sorry, file already exists.";
        } else if ($_FILES['foto2']['size'] > 500000) {
            echo "Sorry, your file is too large.";
        } else {
            move_uploaded_file($_FILES['foto2']['tmp_name'], $file_destination);
        };



        $mysqli_query = "UPDATE pecas";
        $mysqli_query .= " SET grupo = '{$_POST['grupo2']}', item = '{$_POST['item2']}',parte = '{$_POST['parte2']}',foto = '{$file_destination}'";
        $mysqli_query .= " WHERE ";
        $mysqli_query .= " pecaId = {$_POST['pecaId2']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaPecas.php');
    }
}

function deleteMoto($conn)
{
    if (isset($_POST["motoid3"])) {

        $mysqli_query = "DELETE FROM motocicletas WHERE";
        $mysqli_query .= " motoID = {$_POST['motoid3']}";
        var_dump($mysqli_query);
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaMotos.php');
    }
}

function deletePeca($conn)
{
    if (isset($_POST["pecaId3"])) {

        $mysqli_query = "DELETE FROM pecas WHERE";
        $mysqli_query .= " pecaId = {$_POST['pecaId3']}";
        var_dump($mysqli_query);
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaPecas.php');
    }
}

function showMotos($conn, $page)
{
    $mysqli_query = "SELECT * FROM motocicletas limit " . (($page - 1) * 5) . ",5";

    $result = mysqli_query($conn, $mysqli_query);

    while ($row = mysqli_fetch_assoc($result)) {
        include("./scripts/php/tabelas/tabela_moto.php");
    }
}

function showPecas($conn, $page)
{
    $mysqli_query = "SELECT * FROM pecas limit " . (($page - 1) * 5) . ",5";

    $result = mysqli_query($conn, $mysqli_query);

    while ($row = mysqli_fetch_assoc($result)) {
        include("./scripts/php/tabelas/tabela_pecas.php");
    }
}

function showMotosMain($table_count, $page)
{
    $items_showing = ($page * 5);
    if ($items_showing > $table_count)
        $items_showing = $table_count;
    echo '<div class="hint-text">Mostrando <b><?php echo $items_showing ?></b> de <b><?php echo $table_count ?></b> resultados.</div>';
    echo '<ul class="pagination">';

    $table_current_page = $page;

    //previous next
    if ($page <= 1) {
        echo '<li class="page-item disabled"><a href="#">Anterior</a></li>';
    } else {
        echo '<li class="page-item"><a href="?page=' . $page - 1 . '">Anterior</a></li>';
    }

    $i = 0;
    while ($i <= 10) {
        $table_page_index = ($i - 5);

        //continue loop if page is not positive number
        if ($table_page_index <= 0) {
            $i++;
            continue;
        }

        //determine the current active page
        if ($table_current_page == $table_page_index) {
            $addclass = ' active';
        } else {
            $addclass = '';
        }

        //disable pages that are empty
        if (ceil($table_count / 5) < $table_page_index) {
            $addclass .= ' disabled';
            echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $page . '" class="page-link"> ' . $table_page_index . ' </a></li>';
        } else
            echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $table_page_index . '" class="page-link"> ' . $table_page_index . ' </a></li>';

        $i++;
    }

    //disable next
    if ($page >= $table_count / 5) {
        echo '<li class="page-item disabled"><a href="#" class="page-link">Proximo</a></li>';
    } else {
        $next_prev = "";
        echo '<li class="page-item"><a href="?page=' . $page + 1 . '" class="page-link">Proximo</a></li>';
    }
}

function showPecasMain($table_count,$page)
{
    $items_showing = ($page * 5);
    if ($items_showing > $table_count)
        $items_showing = $table_count;
    echo '<div class="hint-text">Mostrando <b><?php echo $items_showing ?></b> de <b><?php echo $table_count ?></b> resultados.</div>';
    echo '<ul class="pagination">';

    $table_current_page = $page;

    //previous next
    if ($page <= 1) {
        echo '<li class="page-item disabled"><a href="#">Anterior</a></li>';
    } else {
        echo '<li class="page-item"><a href="?page=' . $page - 1 . '">Anterior</a></li>';
    }

    $i = 0;
    while ($i <= 10) {
        $table_page_index = ($i - 5);

        //continue loop if page is not positive number
        if ($table_page_index <= 0) {
            $i++;
            continue;
        }

        //determine the current active page
        if ($table_current_page == $table_page_index) {
            $addclass = ' active';
        } else {
            $addclass = '';
        }

        //disable pages that are empty
        if (ceil($table_count / 5) < $table_page_index) {
            $addclass .= ' disabled';
            echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $page . '" class="page-link"> ' . $table_page_index . ' </a></li>';
        } else
            echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $table_page_index . '" class="page-link"> ' . $table_page_index . ' </a></li>';

        $i++;
    }

    //disable next
    if ($page >= $table_count / 5) {
        echo '<li class="page-item disabled"><a href="#" class="page-link">Proximo</a></li>';
    } else {
        $next_prev = "";
        echo '<li class="page-item"><a href="?page=' . $page + 1 . '" class="page-link">Proximo</a></li>';
    }
}
