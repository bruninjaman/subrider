<section id="banner">
    <div class="content">
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Grupo</th>
                            <th>Item</th>
                            <th>Ordem de Serviço</th>
                            <th>Parte</th>
                            <th>Quantidade</th>
                            <th>Valor</th>
                            <th>Açoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $page = 1;
                        $sql_query = "SELECT * FROM pecas ";
                        $sql_query .= "LIMIT " . $page * 5;
                        $result = mysqli_query($conn, $sql_query);

                        while ($peca = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><img src='<?php echo $peca['foto']; ?>' style="height:100px;width:100px;"></td>
                                <td><?php echo $peca['grupo']; ?></td>
                                <td><?php echo $peca['item']; ?></td>
                                <td><?php echo $peca['ordem']; ?></td>
                                <td><?php echo $peca['quantidade']; ?></td>
                                <td><?php echo $peca['parte']; ?></td>
                                <td><?php echo $peca['valor']; ?></td>
                                <td>
                                    <button onclick="location.href='tabelaPecasEdit.php?motoID=<?php echo $moto['motoId']?>'" ><i class="fa-solid fa-plus me-2"></i> Adicionar </button>
                                    <button onclick="location.href='tabelaPecasEdit.php?pecaID=<?php echo $peca['pecaId']?>'" ><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <!-- <button onclick="location.href='tabelaPecasDeletar.php?pecaID=<?php echo $peca['pecaId']?>'" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button> -->
                                    <button onclick="return confirm('Deseja realmente excluir este item?')" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>                                </td>
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