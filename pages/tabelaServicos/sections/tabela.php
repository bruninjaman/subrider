<section id="banner">
    <div class="content">
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table" style="width: 800px;">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>tipo</th>
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
                                <td><?php echo $servico['tipo']; ?></td>
                                <td>
                                    <button onclick="location.href='tabelaServicosEdit.php?servicoID=<?php echo $servico['servicoId'] ?>'"><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <button onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $servico['servicoId'] ?>)"><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>
                                </td>
                            </tr>
                        <?php
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
                        $sql_query = "SELECT * FROM servicos ";
                        pagination($conn, $sql_query);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>