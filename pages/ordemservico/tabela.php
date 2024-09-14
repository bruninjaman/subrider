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
        <h2>Ordem de Serviço: <?php echo $_GET['ordem'] ?></h2>

        <h1 id="editableData">Data: <span id="dateValue"><?php echo ($ordem_servicos["Data"] != null) ? date("d/m/Y", strtotime($ordem_servicos["Data"])) : "dd/mm/aaaa"; ?></span></h1>

        <!-- definir proprietário -->
        <style>
            #editableproprietario {
                font-size: 25px;
            }
        </style>
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
        <h1>Proprietário: <span id="editableproprietario"><?php echo ($ordem_servicos["proprietario_ordem"] != null) ? $ordem_servicos["proprietario_ordem"] : "Não definido"; ?></span></h1>
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
        <h1>Quilometragem: <span><?php echo $quilometragem; ?></span></h1>

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
                                    <button style="background: none; border: none;" onclick="location.href='ordem_edit_item.php?item_ordemID=<?php echo $item['item_ordemID'] ?>&ordem=<?php echo $_GET['ordem'] ?>'"><img src="./assets\css\images\edit.png" style="height: 30x; width: 30px;"> </button>
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
                <!-- Botões -->
                <div class="buttons-table">
                    <a class='button secondary' href="ordem_add_item.php?ordem=<?php echo $_GET['ordem'] ?>">Adicionar item</a>
                    <!-- <a class='button secondary' href="javascript:void(0)" onclick="showModal()">Medições</a> -->

                    <a id="openModal" class="button secondary">Medições</a>

                    <!-- Modal Structure -->
                    <div id="modal" class="modal">
                        <div class="modal-content content form">
                            <!-- Modal Pages -->
                            <div class="modal-page" id="page2">
                                <?php
                                include("menu_cadastro.php");
                                ?>
                            </div>

                            <div class="modal-page" id="page3" style="display:none;">
                                <?php
                                include("menu_escolha.php");
                                ?>
                            </div>

                            <div class="modal-page" id="cabecotePage" style="display:none;">
                                <?php
                                include("menu_cabecote.php");
                                ?>
                            </div>
                            <div class="modal-page" id="motorPage" style="display:none;">
                                <?php
                                include("menu_motor.php");
                                ?>
                            </div>

                            <div class="modal-page" id="virabrequimPage" style="display:none;">
                                <?php
                                include("menu_virabrequim.php");
                                ?>
                            </div>

                            <div class="modal-page" id="embreagemPage" style="display:none;">
                                <?php
                                include("menu_embreagem.php");
                                ?>
                            </div>

                            <div class="modal-page" id="bombasPage" style="display:none;">
                                <?php
                                include("menu_bomba.php");
                                ?>
                            </div>
                        </div>
                    </div>

                    <style>
                        @font-face {
                            font-family: 'Inter-SemiBold';
                            src: url(fonts/Inter-SemiBold.ttf);
                        }

                        @font-face {
                            font-family: 'Inter-Bold';
                            src: url(fonts/Inter-Bold.ttf);
                        }

                        @font-face {
                            font-family: 'Inter-Medium';
                            src: url(fonts/Inter-Medium.ttf);
                        }

                        label {
                            color: gray;
                            display: block;
                            font-family: 'Inter-Medium';
                        }

                        h1 {
                            font-family: 'Inter-SemiBold';
                            color: white;
                            text-align: center;
                            background-color: #181921;
                            padding: 10px;
                            border-radius: 5px;
                        }

                        input {
                            background: #17171e;
                            color: white;
                            height: 3em;
                            border: 3px solid #2c2d34;
                            border-radius: 5px;
                            padding: 5px;
                            font-family: 'Inter-Medium';
                        }

                        input[type="number"] {
                            width: 120px;
                        }

                        input[type="number"]:focus {
                            border: 3px solid #fb4545;
                        }

                        input[type="submit"] {
                            font-family: 'Inter-SemiBold';
                            background-color: #00c063;
                            border-color: #00c063;
                            font-size: 1.2em;
                            width: 250px;
                            color: white;
                            padding: 10px;
                            border-radius: 5px;
                            cursor: pointer;
                        }

                        input[type="submit"]:hover {
                            background-color: #e44c65;
                            border-color: #e44c65;
                        }

                        input:focus {
                            border-color: #fb4545;
                        }

                        input::-webkit-outer-spin-button,
                        input::-webkit-inner-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                        }

                        input[type="file"] {
                            background: transparent;
                            border: none;
                        }
                    </style>
                    <!-- modal -->
                    <div id="medicoes1" class="modal">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <iframe src="medicoes.php?ordem=<?php echo $_GET['ordem'] ?>" width="100%" height="500px"></iframe>
                        </div>
                    </div>

                    <a class='button secondary' href="relatorio.php?ordem=<?php echo $_GET['ordem'] ?>">Relatorio</a>
                    <a class='button primary' href="pdf/download.php?ordem=<?php echo $_GET['ordem'] ?>">Baixar como PDF</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
echo "<script>";
echo file_get_contents('pages/ordemservico/modal.js');
echo "</script>";
?>