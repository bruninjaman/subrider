<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        //Categorias de pesquisa
        $categoriasPesquisa = "SHOW COLUMNS FROM pecas";
        $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
        include_once("includes\searchbar.php");
        ?>
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Grupo</th>
                            <th>Item</th>
                            <th>Parte</th>
                            <th>Açoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT * FROM pecas ";
                        $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                        $result = mysqli_query($conn, $sql_query);

                        while ($peca = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><img src='<?php echo $peca['foto']; ?>' style="height:100px;width:100px;"></td>
                                <td><?php echo $peca['grupo']; ?></td>
                                <td><?php echo $peca['item']; ?></td>
                                <td><?php echo $peca['parte']; ?></td>
                                <td>
                                    <button style="background: none; border: none;" onclick="location.href='tabelaPecasEdit.php?pecaID=<?php echo $peca['pecaId'] ?>'"><img src="./assets\css\images\edit.png" style="height: 30x; width: 30px;"></button>
                                    <!-- <button onclick="location.href='tabelaPecasDeletar.php?pecaID=<?php echo $peca['pecaId'] ?>'" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button> -->
                                    <button style="background: none; border: none;" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $peca['pecaId'] ?>)"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-3">
                        <a class="button primary" href='tabelaPecasAdd.php'>Adicionar Item</a>
                    </div>
                    <div class="col-9">
                        <?php
                        $sql_query = "SELECT * FROM pecas ";
                        pagination($conn, $sql_query);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>