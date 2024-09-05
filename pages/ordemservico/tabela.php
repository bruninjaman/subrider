<style>
    /* Modal container */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        z-index: 1000;
        /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        /* Black background with opacity */
    }

    /* Modal content */
    .modal-content {
        background-color: #181921;
        margin: 15% auto;
        /* 15% from the top and centered */
        padding: 10px;
        border-radius: 25px;
        width: 80%;
        /* Could be more or less, depending on screen size */
        max-width: 900px;
        box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    /* Close button (X) */
    .close {
        color: #aaa;
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<?php
$getdate = "SELECT * FROM ordem_servicos ";
$getdate .= "WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "' ";
$data = mysqli_query($conn, $getdate);
$data = mysqli_fetch_assoc($data);
?>
<section id="banner">
    <div class="content">
        <h2>Ordem de Serviço: <?php echo $_GET['ordem'] ?></h2>

        <h1 id="editableData">Data: <span id="dateValue"><?php echo ($data["Data"] != null) ? date("d/m/Y", strtotime($data["Data"])) : "dd/mm/aaaa"; ?></span></h1>

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
                                    <!-- <?php
                                            if ($item["Foto"] != 0) {
                                            ?> 
                                        <img src="./<?php echo $item["Foto"] ?>" style="width: 70px; height: 70px">
                                    <?php
                                            }
                                    ?> -->
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
                                    <!-- <button onclick="location.href='servicesAdd.php?ordem=<?php echo $_GET['ordem'] ?>'"><i class="fa-solid fa-plus me-2"></i> Adicionar </button> -->

                                    <!-- <button onclick="location.href='servicesEdit.php?ordem=<?php echo $_GET['ordem'] . $typeAndId ?>'"><i class="fa-solid fa-user-pen me-2"></i> Editar </button> -->
                                    <button style="background: none; border: none;" onclick="location.href='ordem_edit_item.php?item_ordemID=<?php echo $item['item_ordemID'] ?>&ordem=<?php echo $_GET['ordem'] ?>'"><img src="./assets\css\images\edit.png" style="height: 30x; width: 30px;"> </button>
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
                <div class="buttons-table">
                    <a class='button secondary' href="ordem_add_item.php?ordem=<?php echo $_GET['ordem'] ?>">Adicionar item</a>
                    <!-- "Medições" Button -->
                    <a class='button secondary' href="javascript:void(0)" onclick="showModal()">Medições</a>

                    <!-- Modal Structure -->
                    <div id="parametroModal" class="modal">
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
<script>
    function showModal() {
        document.getElementById("parametroModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("parametroModal").style.display = "none";
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        var modal = document.getElementById("parametroModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>