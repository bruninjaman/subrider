<section id="banner">
    <div class="content">
        <h2>Ordem de Serviço: <?php echo $_GET['ordem'] ?></h2>

        <div class="table-wrapper">
            <div class="table-wrapper">
                <?php
                // $items_ordem_query = 'SELECT 1 as type, pecas.grupo grupo, pecas.parte parte, pecas.item item, pecas.foto foto, pecas.valor, pecas.quantidade,pecas.pecaId ID FROM pecas ';
                // $items_ordem_query .= "WHERE pecas.ordem = '" . $_GET['ordem'] . "' ";
                // $items_ordem_query .= "UNION ";
                // $items_ordem_query .= "SELECT 2, Null, Null, servicos.item, Null, servicos.valor, 1, servicos.servicoId FROM servicos ";
                // $items_ordem_query .= "WHERE servicos.ordem = '" . $_GET['ordem'] . "' ";
                // $items_ordem_query .= "UNION ";
                // $items_ordem_query .= "SELECT 3, null, null, adiantamento.descricao, Null, adiantamento.valor, 1, adiantamento.IDadiantamento FROM adiantamento ";
                // $items_ordem_query .= "WHERE adiantamento.ordem = '" . $_GET['ordem'] . "' ";
                $items_ordem_query = "SELECT * FROM item_ordem ";
                $items_ordem_query .= "WHERE item_ordem.Ordem = '" . $_GET['ordem'] . "' ";
                $result = mysqli_query($conn, $items_ordem_query);
                ?>
                <table class="table alt">
                    <thead>
                        <tr>
                            <th colspan=3>Descrição</th>
                            <th>Quantidade</th>
                            <th>Valor unitário</th>
                            <th>Valor Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        $adiantamento = 0;
                        while ($item = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td colspan=3>
                                    <!-- <?php
                                    if ($item["Foto"] != 0) {
                                    ?> 
                                        <img src="./<?php echo $item["Foto"] ?>" style="width: 70px; height: 70px">
                                    <?php
                                    }
                                    ?> -->
                                    <?php echo $item['Grupo'] != 0 ? "" . $item['Grupo'] : ""; ?>
                                    <?php echo $item['Parte'] != 0 ? "/" . $item['Parte'] . "/" : ""; ?>
                                    <?php echo $item['Item'] != 0 ? "" . $item['Item'] : ""; ?>
                                    <?php echo $item['Descricao'] != 0 ? "" . $item['Descricao'] : ""; ?>
                                </td>
                                <td><?php echo $item['Quantidade']; ?></td>
                                <td><?php echo realFormat($item['Valor']); ?></td>
                                <td><?php echo realFormat($item['Valor'] * $item['Quantidade']); ?></td>
                                <td>
                                    <?php
                                    if ($item["Descricao"] == '0') {
                                        $total = $total + $item['Valor'] * $item['Quantidade'];
                                        //$typeAndId = "&type=".$item["type"] ."&id=".$item["ID"];
                                    } else {
                                        $adiantamento = $adiantamento  + $item['Valor'] * $item['Quantidade'];
                                    }
                                    ?>
                                    <!-- <button onclick="location.href='servicesAdd.php?ordem=<?php echo $_GET['ordem'] ?>'"><i class="fa-solid fa-plus me-2"></i> Adicionar </button> -->
                                    <!-- <button onclick="location.href='servicesEdit.php?ordem=<?php echo $_GET['ordem'] . $typeAndId ?>'"><i class="fa-solid fa-user-pen me-2"></i> Editar </button> -->
                                    <!-- <button onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $item["type"] ?>,<?php echo $item["ID"] ?>,<?php echo "'" . $_GET["ordem"] . "'" ?>)"><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button> -->
                                    <button onclick="return confirm('Deseja realmente excluir este item?')"><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>
                                </td>
                            </tr>
                        <?php
                        }
                        $subtotal = $total - $adiantamento;
                        if ($subtotal <= 0 ) {
                            $subtotal = 0;
                        }
                        ?>
                        <tr class="total">
                            <td colspan="3"></td>
                            <td>Subtotal:</td>
                            <td><?php echo realFormat($subtotal) ?></td>
                            <td>Total:</td>
                            <td><?php echo realFormat($total) ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="buttons-table">
                    <a class='button secondary' href="servicesAdd.php?ordem=<?php echo $_GET['ordem'] ?>">Adicionar item</a>
                    <a class='button primary' href="pdf/download.php?ordem=<?php echo $_GET['ordem'] ?>">Baixar como PDF</a>
                </div>
            </div>
        </div>
    </div>
</section>