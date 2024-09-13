<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        //Categorias de pesquisa
        $categoriasPesquisa = "SHOW COLUMNS FROM motocicletas";
        $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
        include_once("./includes/searchbar.php");
        ?>
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <form action="" method="get">
                                <th><button class="sort" type=submit name="orderby" value="endereco">Endereço <i class="fa-solid fa-sort"></i></button> </th>
                                <th><button class="sort" type=submit name="orderby" value="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type=submit name="orderby" value="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type=submit name="orderby" value="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type=submit name="orderby" value="placa">Placa <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type=submit name="orderby" value="km">KM <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type=submit name="orderby" value="proprietario">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                            </form>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT * FROM motocicletas ";

                        if(isset($_GET["pesquisa"])){
                            $sql_query .= " WHERE ".strtolower($_GET['selectPesquisa'])." LIKE '%".$_GET["pesquisa"]."%' ";
                        }
                        if(isset($_GET["orderby"])){
                            $sql_query .= " ORDER BY  ".$_GET["orderby"]."  ";
                        } else {
                            $sql_query .= " ORDER BY motocicletas.motoId DESC "; // Default ordering by latest added (motoId)
                        }

                        $sql_query_without_limit = $sql_query;
                        $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                        $result = mysqli_query($conn, $sql_query);
                        while ($moto = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td class="img-table"><img src='<?php echo $moto['foto']; ?>'></td>
                                <td data-cell="Endereço"><?php echo $moto['endereco']; ?></td>
                                <td data-cell="Ano"><?php echo $moto['ano']; ?></td>
                                <td data-cell="Modelo"><?php echo $moto['modelo']; ?></td>
                                <td data-cell="Marca"><?php echo $moto['marca']; ?></td>
                                <td data-cell="Placa"><?php echo $moto['placa']; ?></td>
                                <td data-cell="Km"><?php echo KMFormat($moto['km']); ?></td>
                                <td data-cell="Proprietario"><?php echo $moto['proprietario']; ?></td>
                                <td>
                                    <!-- <button onclick="location.href='tabelaMotoServices.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-toolbox me-2"></i></i> Serviços</button> -->
                                    <!-- <button onclick="location.href='tabelaMotoProprietario.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-address-book me-2"></i> Proprietarios</button> -->
                                    <button style="background: none; border: none;" onclick="location.href='editmotos.php?motoID=<?php echo $moto['motoId'] ?>'"><img src="./assets\css\images\edit.png" style="height: 30x; width: 30px;"> </button>
                                    <button style="background: none; border: none;" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $moto['motoId'] ?>)"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-3">
                        <a class="button primary" href='addmotos.php'>Adicionar Motocicleta</a>
                    </div>
                    <div class="col-9">
                        <?php
                        $sql_query = $sql_query_without_limit;
                        pagination($conn, $sql_query);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>