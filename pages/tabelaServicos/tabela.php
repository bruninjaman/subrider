<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        //Categorias de pesquisa
        $categoriasPesquisa = "SHOW COLUMNS FROM servicos";
        $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
        include_once("./includes/searchbar.php");
        ?>
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table servicetable">
                    <thead>
                        <tr>
                            <form action="" method="get">
                                <th><button class="sort" type=submit name="orderby" value="item">Item <i class="fa-solid fa-sort"></i></button> </th>
                                <th><button class="sort" type=submit name="orderby" value="tipo">Tipo <i class="fa-solid fa-sort"></i></button></th>
                            </form>
                            <th>Açoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT * FROM servicos ";
                        if (isset($_GET["pesquisa"])) {
                            $sql_query .= " WHERE " . strtolower($_GET['selectPesquisa']) . " LIKE '%" . $_GET["pesquisa"] . "%' ";
                        }
                        if (isset($_GET["orderby"])) {
                            $sql_query .= " ORDER BY  " . $_GET["orderby"] . "  ";
                        } else {
                            $sql_query .= " ORDER BY servicos.servicoId DESC "; // Default ordering by latest added
                        }

                        $sql_query_without_limit = $sql_query;
                        $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                        $result = mysqli_query($conn, $sql_query);

                        if (!$result) {
                            // Exibe uma mensagem genérica ao usuário
                            echo "<tr><td colspan='7'>Nenhum resultado encontrado.</td></tr>";
                        } elseif (mysqli_num_rows($result) === 0) {
                            // Se não houver resultados
                            echo "<tr><td colspan='7'>Nenhum resultado encontrado.</td></tr>";
                        } else {
                            while ($servico = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td data-cell="Item"><?php echo $servico['item']; ?></td>
                                    <td data-cell="Tipo"><?php echo $servico['tipo']; ?></td>
                                    <td>
                                        <button style="background: none; border: none;" onclick="location.href='tabelaServicosEdit.php?servicoID=<?php echo $servico['servicoId'] ?>'"><img src="./assets\css\images\edit.png" style="height: 30x; width: 30px;"> </button>
                                        <button style="background: none; border: none;" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $servico['servicoId'] ?>)"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-3">
                        <a class="button primary" href='tabelaServicosAdd.php'>Adicionar Serviço</a>
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