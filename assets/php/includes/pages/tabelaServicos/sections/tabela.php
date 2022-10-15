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
                        $page = 1;
                        $sql_query = "SELECT * FROM servicos ";
                        $sql_query .= "LIMIT " . $page * 5;
                        $result = mysqli_query($conn, $sql_query);

                        while ($servico = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $servico['item']; ?></td>
                                <td><?php echo $servico['ordem']; ?></td>
                                <td><?php echo $servico['tipo']; ?></td>
                                <td><?php echo $servico['valor']; ?></td>
                                <td>
                                    <button onclick="location.href='tabelaServicosEdit.php?servicoID=<?php echo $servico['servicoId']?>'" ><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <button onclick="location.href='tabelaServicosDeletar.php?servicoID=<?php echo $servico['servicoId']?>'" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="w3-twothird">
                    <ul class="pagination">
                        <li><a href="javascript:void(0)">«</a></li>
                        <li><a class="w3-red" href="javascript:void(0)">1</a></li>
                        <li><a href="javascript:void(0)">2</a></li>
                        <li><a href="javascript:void(0)">3</a></li>
                        <li><a href="javascript:void(0)">4</a></li>
                        <li><a href="javascript:void(0)">5</a></li>
                        <li><a href="javascript:void(0)">6</a></li>
                        <li><a href="javascript:void(0)">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>