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
                            <th>Valor Unitário</th>
                            <th>Valor Total</th>
                            <th>Açoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT * FROM pecas ";
                        $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] : 0) * 5) . ", 5";
                        $result = mysqli_query($conn, $sql_query);

                        while ($peca = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><img src='<?php echo $peca['foto']; ?>' style="height:100px;width:100px;"></td>
                                <td><?php echo $peca['grupo']; ?></td>
                                <td><?php echo $peca['item']; ?></td>
                                <td><?php echo $peca['ordem'] != null ? $peca['ordem'] : "Não definido"; ?></td>
                                <td><?php echo $peca['quantidade']; ?></td>
                                <td><?php echo $peca['parte']; ?></td>
                                <td><?php echo realFormat($peca['valor']); ?></td>
                                <td><?php echo realFormat($peca['valor']*$peca['quantidade']); ?></td>
                                <td>
                                    <button onclick="location.href='tabelaPecasAdd.php?pecaID=<?php echo $peca['pecaId']?>'" ><i class="fa-solid fa-plus me-2"></i> Adicionar </button>
                                    <button onclick="location.href='tabelaPecasEdit.php?pecaID=<?php echo $peca['pecaId']?>'" ><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <!-- <button onclick="location.href='tabelaPecasDeletar.php?pecaID=<?php echo $peca['pecaId']?>'" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button> -->
                                    <button onclick="return confirm('Deseja realmente excluir este item?')" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="pagination-style">
                    <ul class="pagination">
                        <li><a href="tabelaPecas.php?page=<?php echo isset($_GET['page']) && $_GET['page'] > 0  ? $_GET['page']-1 : 0 ?>">«</a></li>
                        <li><a href="tabelaPecas.php?page=<?php echo 1 ?>">1</a></li>
                        <li><a href="tabelaPecas.php?page=<?php echo 2 ?>">2</a></li>
                        <li><a href="tabelaPecas.php?page=<?php echo 3 ?>">3</a></li>
                        <li><a href="tabelaPecas.php?page=<?php echo 4 ?>">4</a></li>
                        <li><a href="tabelaPecas.php?page=<?php echo 5 ?>">5</a></li>
                        <li><a href="tabelaPecas.php?page=<?php echo 6 ?>">6</a></li>
                        <li><a href="tabelaPecas.php?page=<?php echo isset($_GET['page']) ? $_GET['page']+1 : 0 ?>">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>