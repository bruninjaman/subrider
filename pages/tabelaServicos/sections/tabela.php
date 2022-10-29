<section id="banner">
    <div class="content">
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Ordem de serviço</th>
                            <th>tipo</th>
                            <th>valor</th>
                            <th>Açoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT * FROM servicos ";
                        $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                        $result = mysqli_query($conn, $sql_query);

                        while ($servico = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $servico['item']; ?></td>
                                <td><?php echo $servico['ordem']; ?></td>
                                <td><?php echo $servico['tipo']; ?></td>
                                <td><?php echo $servico['valor']; ?></td>
                                <td>
                                    <button onclick="location.href='tabelaServicosAdd.php?servicoID=<?php echo $servico['servicoId']?>'" ><i class="fa-solid fa-plus me-2"></i> Adicionar </button>
                                    <button onclick="location.href='tabelaServicosEdit.php?servicoID=<?php echo $servico['servicoId']?>'" ><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <button onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $servico['servicoId'] ?>)" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Pagination -->
                <link rel="stylesheet" href="./assets/css/pagination.css">
                <?php
                $sql_query = "SELECT * FROM servicos ";
                $result = mysqli_query($conn, $sql_query);
                $number_of_page = floor(mysqli_num_rows($result) / 5) + 1;
                ?>
                <div class="pagination-style">
                    <ul class="pagination">
                        <?php
                        if (isset($_GET['page']) ? $_GET['page'] : 0 > 1) {
                        ?>
                            <li><a href="tabelaServicos.php?page=<?php echo isset($_GET['page']) && $_GET['page'] > 0  ? $_GET['page'] - 1 : 0 ?>">«</a></li>
                        <?php
                        }
                        //display the link of the pages in URL  
                        for ($_GET['page'] = 1; $_GET['page'] <= $number_of_page; $_GET['page']++) {
                            echo '<li><a href = "tabelaServicos.php?page=' . $_GET['page'] . '">' . $_GET['page'] . ' </a></li>';
                        }
                        ?>
                        <?php
                        if (isset($_GET['page']) ? $_GET['page'] : 0 <= $number_of_page) {
                        ?>
                            <li><a href="tabelaServicos.php?page=<?php echo isset($_GET['page']) ? $_GET['page'] + 1 : 0 ?>">»</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>