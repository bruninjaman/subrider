<?php
$mysqli_query = "SELECT motoID FROM ordem_servicos ";
$mysqli_query .= " WHERE Codigo = '" . $_GET['ordem'] . "'";

$motoid = mysqli_query($conn, $mysqli_query);
$motoid = mysqli_fetch_assoc($motoid);

$mysql_query = "SELECT * FROM item_ordem ";
$mysql_query .= "WHERE item_ordem.item_ordemID = '" . $_GET['item_ordemID'] . "'";

$result2 = mysqli_query($conn, $mysql_query);
$result2 = mysqli_fetch_assoc($result2);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <center>
            <form method="post" action="scripts\editservices\editservice.php?ordem=<?php echo $_GET['ordem'] . "&item_ordemID=" . $_GET['item_ordemID'] ?>">
                <div>
                    <div class="row">
                        <div class="col-12">
                            <input onchange="tipo_item_onchange()" type="radio" id="pecas" name="tipo_item" <?php
                                                                                                            if ($result2["Categoria"] == 2) {
                                                                                                                echo "checked";
                                                                                                            }
                                                                                                            ?> value="pecas">
                            <label for="pecas">
                                <h4>Peça</h4>
                            </label>
                            <input onchange="tipo_item_onchange()" type="radio" id="service" name="tipo_item" <?php
                                                                                                                if ($result2["Categoria"] == 1) {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                                ?> value="service">
                            <label for="service">
                                <h4>Serviço</h4>
                            </label>
                            <input onchange="tipo_item_onchange()" type="radio" id="adiantamento" name="tipo_item" <?php
                                                                                                                    if ($result2["Categoria"] == 3) {
                                                                                                                        echo "checked";
                                                                                                                    }
                                                                                                                    ?> value="adiantamento">
                            <label for="adiantamento">
                                <h4>Adiantamento</h4>
                            </label>
                            <script>
                                window.onload = function() {
                                    tipo_item_onchange();
                                }
                            </script>

                        </div>
                    </div>

                    <div id="form_pecas" style="display: none;">
                    <div class="row">
        <div class="col-12">
            <h3>Selecione uma peça</h3>
            <?php
            $sql_query = "SELECT * FROM pecas ORDER BY pecas.grupo";
            $result = mysqli_query($conn, $sql_query);
            $initialPecaId = '';
            $initialPecaValue = $result2['Grupo'] . ' - ' . $result2['Item'] . ' - ' . $result2['Parte'];
            while ($peca = mysqli_fetch_assoc($result)) {
                $optionValue = $peca["grupo"] . " - " . $peca["item"] . " - " . $peca["parte"];
                if ($optionValue == $initialPecaValue) {
                    $initialPecaId = $peca["pecaId"];
                }
            }
            ?>
            <input type="hidden" name="pecaid" id="pecaid" value="<?php echo $initialPecaId; ?>">
            <input type="text" list="pecaList" name="pecaInput" id="pecaInput" value="<?php echo $initialPecaValue; ?>">
            <datalist id="pecaList">
                <?php
                mysqli_data_seek($result, 0); // Reset the result pointer to the beginning
                while ($peca = mysqli_fetch_assoc($result)) { ?>
                    <option value="<?php echo $peca["grupo"] . " - " . $peca["item"] . " - " . $peca["parte"]; ?>" data-id="<?php echo $peca["pecaId"]; ?>"></option>
                <?php } ?>
            </datalist>
            <script>
                const pecaInput = document.querySelector('#pecaInput');
                const pecaIdInput = document.querySelector('#pecaid');
                const pecaList = document.querySelector('#pecaList');

                function updatePecaId() {
                    const selectedOption = [...pecaList.options].find(option => option.value === pecaInput.value);
                    if (selectedOption) {
                        pecaIdInput.value = selectedOption.dataset.id;
                    }
                }

                pecaInput.addEventListener('input', updatePecaId);
                pecaInput.addEventListener('change', updatePecaId);
            </script>
        </div>
    </div>
                        <br>
                        <div class="row">
                            <div class="col-8">
                                <label>Código do Produto</label>
                                <input value="<?php echo $result2['Codigo'] ?>" type="text" name="scode">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input value="<?php echo $result2['Quantidade'] ?>" type="number" name="pquantidade" value="<?php echo isset($_POST["pecaid"]) ? 1 : 0 ?>">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input value="<?php echo $result2['Valor'] ?>" type="number" name="pvalor" step=".01">
                            </div>
                            <div class="col-1">
                            </div>
                            <div class="col-6">
                                <br>
                                <input id="submit" class="button primary" type="submit" value="Editar Peça">
                            </div>
                            <div class="col-1">
                            </div>
                        </div>
                    </div>
                    <div id="form_services" class="row gtr-uniform gtr-50" style="display: none;">
                    <div class="row">
        <div class="col-12">
            <h3>Selecione um serviço</h3>
            <?php
            $sql_query = "SELECT * FROM servicos ORDER BY servicos.tipo";
            $result = mysqli_query($conn, $sql_query);
            $initialServicoId = '';
            $initialServicoValue = $result2['Tipo'] . " - " . $result2['Item'];
            while ($servico = mysqli_fetch_assoc($result)) {
                $optionValue = $servico["tipo"] . " - " . $servico["item"];
                if ($optionValue == $initialServicoValue) {
                    $initialServicoId = $servico["servicoId"];
                }
            }
            ?>
            <input type="hidden" name="servicoid" id="servicoid" value="<?php echo $initialServicoId; ?>">
            <input type="text" list="servicoList" name="servicoInput" id="servicoInput" value="<?php echo $initialServicoValue; ?>">
            <datalist id="servicoList">
                <?php
                mysqli_data_seek($result, 0); // Reset the result pointer to the beginning
                while ($servico = mysqli_fetch_assoc($result)) { ?>
                    <option value="<?php echo $servico["tipo"] . " - " . $servico["item"]; ?>" data-id="<?php echo $servico["servicoId"]; ?>"></option>
                <?php } ?>
            </datalist>
            <script>
                const servicoInput = document.querySelector('#servicoInput');
                const servicoIdInput = document.querySelector('#servicoid');
                const servicoList = document.querySelector('#servicoList');

                function updateServicoId() {
                    const selectedOption = [...servicoList.options].find(option => option.value === servicoInput.value);
                    if (selectedOption) {
                        servicoIdInput.value = selectedOption.dataset.id;
                    }
                }

                servicoInput.addEventListener('input', updateServicoId);
                servicoInput.addEventListener('change', updateServicoId);
            </script>
        </div>
    </div>
                        <br>
                        <div class="row">
                            <div class="col-2">
                                <label>Valor</label>
                                <input value="<?php echo $result2['Valor'] ?>" type="number" name="svalor" step=".01">
                            </div>
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input value="<?php echo $result2['Quantidade'] ?>" type="number" name="squantidade">
                            </div>
                            <div class="col-8">
                                <br>
                                <input id="submit" class="button primary" type="submit" value="Editar Serviço">
                            </div>
                        </div>

                    </div>
                    <div id="form_adiantamento" class="row gtr-uniform gtr-50" style="display: none;">
                        <div class="row">
                            <div class="col-10">
                                <label>Descrição</label>
                                <input value="<?php echo $result2['Descricao'] ?>" type="text" name="aitem">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input value="<?php echo $result2['Valor'] ?>" type="number" name="avalor" step=".01">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1">
                            </div>
                            <div class="col-10">
                                <br>
                                <input id="submit" class="button primary" type="submit" value="Editar Adiantamento">
                            </div>
                            <div class="col-1">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </center>
    </div>
</section>