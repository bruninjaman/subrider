<?php

require("subfunctions.php");

function login($user, $password, $conn) {
    $mysqli_query = "SELECT * FROM login WHERE username = '{$user}' AND password = '{$password}'";
    $result = mysqli_query($conn, $mysqli_query);
    session_start();
    $rows = mysqli_num_rows($result);
    $result = mysqli_fetch_assoc($result);
    if($rows > 0) {
        $_SESSION["user"] = $result["username"];
	    $_SESSION["type"] = $result["userType"];
        header("location: ../../../index.php");
    } else {
        header("location: ../../../login.php");
    }
}

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

function addServico($conn)
{
    if (isset($_POST["addItem"])) {

        $mysqli_query = "INSERT INTO servicos (item,tipo) ";
        $mysqli_query .= "VALUES ('{$_POST['addItem']}','{$_POST['addTipo']}')";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaServicos.php?page='.$_GET["page"]);
    }
}

function addPeca($conn)
{
    if (isset($_POST["addParte"])) {
        $file_destination = uploadFoto($_FILES['addFoto']['name'],$_FILES['addFoto']['size'],$_FILES['addFoto']['tmp_name'],'fotos/pecas/'); 

        $mysqli_query = "INSERT INTO pecas (grupo, item, parte, foto)";
        $mysqli_query .= " VALUES ('{$_POST['addGrupo']}','{$_POST['addItem']}','{$_POST['addParte']}' ,'{$file_destination}')";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaPecas.php?page='.$_GET["page"]);
    }
}

function addMoto($conn)
{
    if (isset($_POST["addProp"])) {
        
        $file_destination = uploadFoto($_FILES['addFoto']['name'],$_FILES['addFoto']['size'],$_FILES['addFoto']['tmp_name'],'fotos/motos/'); 

        $mysqli_query = "INSERT INTO motocicletas (endereco, proprietario, marca, placa, ano, modelo, km, foto)";
        $mysqli_query .= " VALUES ('{$_POST['addEnder']}','{$_POST['addProp']}' ";
        $mysqli_query .= " ,'{$_POST['addMarca']}','{$_POST['addPlaca']}','{$_POST['addAno']}','{$_POST['addModel']}' ";
        $mysqli_query .= " ,'{$_POST['addKm']}','{$file_destination}')";

        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaMotos.php?page='.$_GET["page"]);
    }
}

function editServico($conn)
{
    if (isset($_POST["editItem"])) {
        $mysqli_query = "UPDATE servicos";
        $mysqli_query .= " SET item = '{$_POST['editItem']}', tipo = '{$_POST['editTipo']}' ";
        $mysqli_query .= " WHERE ";
        $mysqli_query .= " servicoId = {$_POST['editServicoId']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaServicos.php?page='.$_GET["page"]);
    }
}

function editMoto($conn)
{
    if (isset($_POST["editProp"])) {
        if($_FILES['editFoto']['name'] != null)
            $file_destination = uploadFoto($_FILES['editFoto']['name'],$_FILES['editFoto']['size'],$_FILES['editFoto']['tmp_name'],'fotos/motos/');
        $mysqli_query = "UPDATE motocicletas";
        $mysqli_query .= " SET endereco = '{$_POST['editEnder']}', proprietario = '{$_POST['editProp']}',marca = '{$_POST['editMarca']}',placa = '{$_POST['editPlaca']}',";
        $mysqli_query .= " ano = '{$_POST['editAno']}', modelo = '{$_POST['editModel']}',km = '{$_POST['editKm']}'";

        if($_FILES['editFoto']['name'] != null)
            $mysqli_query .= ", foto = '{$file_destination}' ";

        $mysqli_query .= " WHERE ";
        $mysqli_query .= " motoId = {$_POST['editMotoId']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaMotos.php?page='.$_GET["page"]);
    }
}

function editPeca($conn)
{
    if (isset($_POST["editParte"])) {
        if($_FILES['editFoto']['name'] != null)
            $file_destination = uploadFoto($_FILES['editFoto']['name'],$_FILES['editFoto']['size'],$_FILES['editFoto']['tmp_name'],'fotos/pecas/'); 

        $mysqli_query = "UPDATE pecas";
        $mysqli_query .= " SET grupo = '{$_POST['editGrupo']}', item = '{$_POST['editItem']}',parte = '{$_POST['editParte']}'";
        if($_FILES['editFoto']['name'] != null)
            $mysqli_query .= ", foto = '{$file_destination}' ";
        $mysqli_query .= " WHERE ";
        $mysqli_query .= " pecaId = {$_POST['editPecaId']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaPecas.php?page='.$_GET["page"]);
    }
}

function deleteServico($conn)
{
    if (isset($_POST["removeServicoId"])) {
        $mysqli_query = "DELETE FROM servicos WHERE";
        $mysqli_query .= " servicoId = {$_POST['removeServicoId']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaServicos.php?page='.$_GET["page"]);
    }
}

function deleteMoto($conn)
{
    if (isset($_POST["removeMotoId"])) {

        $mysqli_query = "DELETE FROM motocicletas WHERE";
        $mysqli_query .= " motoId = {$_POST['removeMotoId']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaMotos.php?page='.$_GET["page"]);
    }
}

function deletePeca($conn)
{
    if (isset($_POST["removePecaId"])) {

        $mysqli_query = "DELETE FROM pecas WHERE";
        $mysqli_query .= " pecaId = {$_POST['removePecaId']}";
        mysqli_query($conn, $mysqli_query);
        header('Location: tabelaPecas.php?page='.$_GET["page"]);
    }
}

function showMotos($conn, $page)
{
    $mysqli_query = "SELECT * FROM motocicletas limit " . (($page - 1) * 5) . ",5";

    $result = mysqli_query($conn, $mysqli_query);

    while ($row = mysqli_fetch_assoc($result)) {
        require("./assets/php/includes/motos/tabela_moto.php");
    }
}

function showPecas($conn, $page)
{
    $mysqli_query = "SELECT * FROM pecas limit " . (($page - 1) * 5) . ",5";

    $result = mysqli_query($conn, $mysqli_query);

    while ($row = mysqli_fetch_assoc($result)) {
        require("./assets/php/includes/pecas/tabela_pecas.php");
    }
}

function showServicos($conn, $page)
{
    $mysqli_query = "SELECT * FROM servicos limit " . (($page - 1) * 5) . ",5";

    $result = mysqli_query($conn, $mysqli_query);

    while ($row = mysqli_fetch_assoc($result)) {
        require("./assets/php/includes/servicos/tabela_servicos.php");
    }
}

function showServicosMain($table_count, $page)
{
    pageCarousel($page,$table_count);
}

function showMotosMain($table_count, $page)
{
    pageCarousel($page,$table_count);
}

function showPecasMain($table_count,$page)
{
    pageCarousel($page,$table_count);
}

?>