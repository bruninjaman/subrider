<style>
    .search input[type=text] {
        padding: 10px;
        font-size: 17px;
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
        float: left;
        width: 80%;
        height: 100%;
    }

    .search button[type=submit] {
        float: left;
        width: 20%;
        height: 100%;
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
        padding: 10px;
        font-size: 17px;
        border-left: none;
        /* Prevent double borders */
        cursor: pointer;
        background-color: #e44c65;
    }

    .search button[type=submit]:hover {
        background-color: #e76278;
    }
</style>
<!-- search bar -->
<?php
//Categorias de pesquisa
$categoriasPesquisa = "SHOW COLUMNS FROM motocicletas";
$resultCategorias = mysqli_query($conn, $categoriasPesquisa);
?>
<form action="tabelaMotos.php" method="get">
    <?php
    if (isset($_GET["page"])) {
        echo "<input type=hidden name='page' value='" . $_GET["page"] . "'>";
    }
    ?>
    <div class="row">
        <div class="col-3">
            <select name="selectPesquisa" id="pesquisa">
                <!-- <option value="Todos">Todos</option> -->
                <?php

                //this while is getting all columns from our table
                while ($categorias = mysqli_fetch_assoc($resultCategorias)) {
                    //black list is used to get rid of tables that we dont want
                    $blacklist = array("Id", "foto","telefone");

                    //this for loop is removing items from our blacklist
                    for ($i = 0; $i < count($blacklist); $i++) {
                        if (str_contains($categorias['Field'], $blacklist[$i]))
                            continue 2;
                    }

                ?>
                    <option value="<?php echo $categorias['Field'] ?>"><?php echo ucfirst($categorias['Field']) ?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="col-4 search">
            <input type="text" name="pesquisa" placeholder="Pesquisar..">
            <button type="submit"><i class="fa fa-search"></i></button>
        </div>
        <div class="col-5">

            <?php
            if (isset($_GET['page'])) {
            ?>
                <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
            <?php
            }
            ?>
        </div>
    </div>
</form>
<!-- fim da barra de pesquisa -->