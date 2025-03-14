<?php
echo "<style>";
echo file_get_contents('pages/ordemservico/modal.css');
echo "</style>";
?>

<?php
$get_ordemservicos = "SELECT * FROM ordem_servicos ";
$get_ordemservicos .= "WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "' ";
$ordem_servicos = mysqli_query($conn, $get_ordemservicos);
$ordem_servicos = mysqli_fetch_assoc($ordem_servicos);
?>
<section id="banner">
    <div class="content">
        <!-- definir proprietário -->
        <style>
            #editableproprietario {
                font-size: 25px;
            }

            .headers-tabela {
                background-color: #181921;
            }
        </style>

        <h2 class="headers-tabela">Ordem de Serviço: <?php echo $_GET['ordem'] ?></h2>

        <h1 class="headers-tabela" id="editableData">Data: <span id="dateValue"><?php echo ($ordem_servicos["Data"] != null) ? date("d/m/Y", strtotime($ordem_servicos["Data"])) : "dd/mm/aaaa"; ?></span></h1>

        <?php
        if ($ordem_servicos["proprietario_ordem"] == null) {
            // pegar o proprietario
            $get_proprietario_query = "SELECT proprietario ";
            $get_proprietario_query .= "FROM motocicletas ";
            $get_proprietario_query .= "WHERE (SELECT ordem_servicos.motoID FROM ordem_servicos WHERE ordem_servicos.Codigo  = '" . $_GET['ordem'] . "') = motocicletas.motoId";
            $proprietario = mysqli_query($conn, $get_proprietario_query);
            $proprietario = mysqli_fetch_assoc($proprietario);

            // modificar proprietario da ordem
            $atualizar_proprietario = "UPDATE ordem_servicos ";
            $atualizar_proprietario .= "SET ordem_servicos.proprietario_ordem = '" . $proprietario['proprietario'] . "' ";
            $atualizar_proprietario .= "WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "' ";
            mysqli_query($conn, $atualizar_proprietario);


            // atualizar array ordem servicos
            $ordem_servicos = mysqli_query($conn, $get_ordemservicos);
            $ordem_servicos = mysqli_fetch_assoc($ordem_servicos);
        }
        ?>
        <h1 class="headers-tabela">Proprietário: <span id="editableproprietario"><?php echo ($ordem_servicos["proprietario_ordem"] != null) ? $ordem_servicos["proprietario_ordem"] : "Não definido"; ?></span></h1>
        <?php
        $query = "SELECT KM FROM `ordem_servicos` WHERE '" . $_GET['ordem'] . "' = `ordem_servicos`.`Codigo`;";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $quilometragem = $row['KM'];
        } else {
            $quilometragem = "Não encontrado";
        }
        if ($quilometragem == null) {
            $quilometragem = "Não encontrado";
        }
        ?>
        <h1 class="headers-tabela">Quilometragem: <span><?php echo $quilometragem; ?></span></h1>

        <p id="errorMessage" style="color: red; display: none;"></p>

        <?php
        $items_ordem_query = "SELECT * FROM item_ordem ";
        $items_ordem_query .= "WHERE item_ordem.Ordem = '" . $_GET['ordem'] . "' ";
        $result = mysqli_query($conn, $items_ordem_query);
        ?>

        <div class="table-wrapper">
            <div class="table-wrapper">
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
                            if ($item["Categoria"] != '3') {
                                $total = $total + $item['Valor'] * $item['Quantidade'];
                            } else {
                                $adiantamento = $adiantamento  + $item['Valor'] * $item['Quantidade'];
                            }

                            if ($item['Categoria'] == '3')
                                continue;
                        ?>
                            <tr>
                                <td colspan=3>
                                    <?php echo $item['Tipo'] != '0' ? "" . $item['Tipo'] . " - " : ""; ?>
                                    <?php echo $item['Grupo'] != '0' ? "" . $item['Grupo'] . " - " : ""; ?>
                                    <?php echo $item['Item'] != '0' ? "" . $item['Item'] . "" : ""; ?>
                                    <?php echo $item['Parte'] != '0' ? " / " . $item['Parte'] : ""; ?>
                                    <?php
                                    echo $item['Descricao'] != '0' ? "" . $item['Descricao'] : "";
                                    if ($item['Categoria'] == '2')
                                        echo $item['Codigo'] != '0' ? "(" . $item['Codigo'] . ")" : "";
                                    ?>

                                </td>
                                <td data-cell="Quantidade"><?php echo $item['Quantidade']; ?></td>
                                <td data-cell="Valor Unitário"><?php echo ($item['Valor'] <= 0) ? 'N/D' : realFormat($item['Valor']); ?></td>
                                <td data-cell="Valor Total"><?php echo realFormat($item['Valor'] * $item['Quantidade']); ?></td>
                                <td>
                                    <button style="background: none; border: none;" onclick="location.href='ordem_edit_item.php?item_ordemID=<?php echo $item['item_ordemID'] ?>&ordem=<?php echo $_GET['ordem'] ?>'"><img src="./assets\css\images\edit.png" style="height: 30px; width: 30px;"> </button>
                                    <button style="background: none; border: none;" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $item['item_ordemID'] ?>,'<?php echo $_GET['ordem'] ?>')"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                </td>
                            </tr>
                        <?php
                        }
                        $subtotal = $total - $adiantamento;
                        ?>
                        <tr class="total">
                            <td colspan="4"></td>
                            <td>Total:</td>
                            <td><?php echo realFormat($total) ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table class="table">
                    <tbody>

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
                                <td>
                                    <button style="background: none; border: none;" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $item['item_ordemID'] ?>,'<?php echo $_GET['ordem'] ?>')"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td class="totalbold">Saldo:</td>
                            <td class="totalbold" colspan="2"><?php echo realFormat($subtotal) ?></td>
                        </tr>
                    </tbody>
                </table>
                <form action="scripts\ordemservico\register_medicoes.php?ordem=<?php echo (string)$_GET['ordem'] ?>" method="POST">
                    <?php 
                        include('menu_medicoes.php');
                    ?>
                </form>
            </div>
        </div>
    </div>
</section>
<?php
echo "<script>";
echo file_get_contents('pages/ordemservico/modal.js');
echo "</script>";
?>