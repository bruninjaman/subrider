<section id="banner">
    <div class="content">
        <h2>Ordem de Serviço: <?php echo $_GET['ordem'] ?></h2>

        <div class="table-wrapper">
            <div class="table-wrapper">
                <?php
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
                            if ($item["Categoria"] == '3') {
                                $total = $total + $item['Valor'] * $item['Quantidade'];
                                //$typeAndId = "&type=".$item["type"] ."&id=".$item["ID"];
                            } else {
                                $adiantamento = $adiantamento  + $item['Valor'] * $item['Quantidade'];
                            }

                            if ($item['Categoria'] == '3')
                                continue;
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
                                    <?php echo $item['Grupo'] != '0' ? "" . $item['Grupo'] . " - " : ""; ?>
                                    <?php echo $item['Item'] != '0' ? "" . $item['Item'] . "" : ""; ?>
                                    <?php echo $item['Parte'] != '0' ? " / " . $item['Parte'] : ""; ?>
                                    <?php echo $item['Descricao'] != '0' ? "" . $item['Descricao'] : ""; ?>
                                </td>
                                <td><?php echo $item['Quantidade']; ?></td>
                                <td><?php echo realFormat($item['Valor']); ?></td>
                                <td><?php echo realFormat($item['Valor'] * $item['Quantidade']); ?></td>
                                <td>
                                    <!-- <button onclick="location.href='servicesAdd.php?ordem=<?php echo $_GET['ordem'] ?>'"><i class="fa-solid fa-plus me-2"></i> Adicionar </button> -->
                                    <!-- <button onclick="location.href='servicesEdit.php?ordem=<?php echo $_GET['ordem'] . $typeAndId ?>'"><i class="fa-solid fa-user-pen me-2"></i> Editar </button> -->
                                    <button style="background: none; border: none;" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $item['item_ordemID'] ?>,'<?php echo $_GET['ordem'] ?>')"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                    <!-- <button onclick="return confirm('Deseja realmente excluir este item?')"><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button> -->
                                </td>
                            </tr>
                        <?php
                        }
                        $subtotal = $total - $adiantamento;
                        ?>
                        <!-- <tr class="total">
                            <td colspan="5"></td>
                            <td>Subtotal:</td>
                            <td><?php echo realFormat($subtotal) ?></td>
                        </tr> -->
                        <tr class="total">
                            <td colspan="4"></td>
                            <td>Total:</td>
                            <td><?php echo realFormat($total) ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table>
                    <?php
                    $result = mysqli_query($conn, $items_ordem_query);
                    while ($item = mysqli_fetch_assoc($result)) {
                        if ($item['Categoria'] != '3')
                            continue;
                    ?>
                        <tr>
                            <td>
                                <?php echo $item['Descricao'] ?>
                            </td>
                            <td>
                                <?php echo realFormat($item['Valor'] * $item['Quantidade']) ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="total">
                        <td>Saldo:</td>
                        <td><?php echo realFormat($subtotal) ?></td>
                    </tr>
                </table>
                <div class="buttons-table">
                    <a class='button secondary' href="servicesAdd.php?ordem=<?php echo $_GET['ordem'] ?>">Adicionar item</a>
                    <a class='button primary' href="pdf/download.php?ordem=<?php echo $_GET['ordem'] ?>">Baixar como PDF</a>
                </div>
            </div>
        </div>
    </div>
</section>