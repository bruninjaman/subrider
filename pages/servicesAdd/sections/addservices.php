<?php
$mysqli_query = "SELECT motoID FROM ordem_servicos ";
$mysqli_query .= " WHERE Codigo = '" . $_GET['ordem'] . "'";

$motoid = mysqli_query($conn, $mysqli_query);
$motoid = mysqli_fetch_assoc($motoid);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <center>
            <form method="post" action="scripts\addservices\addservice.php?ordem=<?php echo $_GET['ordem'] . "&motoID=" . $motoid['motoID'] ?>">
                <!-- <form method="POST" action="addservices.php"> -->
                <div>
                    <div class="row">
                        <div class="col-12">
                            <input onchange="tipo_item_onchange()" type="radio" id="pecas" name="tipo_item" value="pecas">
                            <label for="pecas">
                                <h4>Peça</h4>
                            </label>
                            <input onchange="tipo_item_onchange()" type="radio" id="service" name="tipo_item" value="service">
                            <label for="service">
                                <h4>Serviço</h4>
                            </label>
                            <input onchange="tipo_item_onchange()" type="radio" id="adiantamento" name="tipo_item" value="adiantamento">
                            <label for="adiantamento">
                                <h4>Adiantamento</h4>
                            </label>
                        </div>
                    </div>

                    <div id="form_pecas" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <h3>Selecione uma peça</h3>
                                <?php
                                $sql_query = "SELECT * FROM pecas ";
                                $sql_query .= "ORDER BY pecas.grupo ";
                                $result = mysqli_query($conn, $sql_query);
                                ?>
                                <input type="text" list="pecaid" name="pecaid_display" onchange="updatePecaid()">
                                <input type="hidden" name="pecaid" id="pecaid_input">
                                <datalist id="pecaid">
                                    <?php while ($peca = mysqli_fetch_assoc($result)) { ?>
                                        <option value="<?php echo $peca["grupo"] . " - " . $peca["item"] . " - " . $peca["parte"] ?>" data-id="<?php echo $peca["pecaId"] ?>"></option>
                                    <?php } ?>
                                </datalist>

                                <script>
                                    function updatePecaid() {
                                        var selectedOption = document.querySelector('#pecaid option[value="' + document.querySelector('[name=pecaid_display]').value + '"]');
                                        document.querySelector('#pecaid_input').value = selectedOption.getAttribute('data-id');
                                    }
                                </script>


                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-8">
                                <label>Código do Produto</label>
                                <input type="text" name="scode">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input type="number" name="pquantidade" value="1">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="pvalor" step=".01" value="0">
                            </div>
                            <div class="col-1">
                            </div>
                            <div class="col-6">
                                <br>
                                <input id="submit" class="button primary" type="submit" value="Adicionar">
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
                                $sql_query = "SELECT * FROM servicos ";
                                $sql_query .= "ORDER BY servicos.tipo ";
                                $result = mysqli_query($conn, $sql_query);
                                ?>
                                <input type="hidden" name="servicoid" id="servicoid" value="">
                                <input type="text" name="servico" list="servicoid_list" onchange="updateServicoid()">
                                <datalist id="servicoid_list">
                                    <?php while ($servico = mysqli_fetch_assoc($result)) { ?>
                                        <option value="<?php echo $servico["tipo"] . " - " . $servico["item"] ?>" data-id="<?php echo $servico["servicoId"] ?>"></option>
                                    <?php } ?>
                                </datalist>
                                <script>
                                    function updateServicoid() {
                                        var selectedOption = document.querySelector('#servicoid_list option[value="' + document.querySelector('input[name="servico"]').value + '"]');
                                        if (selectedOption) {
                                            document.querySelector('#servicoid').value = selectedOption.getAttribute('data-id');
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="svalor" step=".01" value="0">
                            </div>
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input type="number" name="squantidade" value="1">
                            </div>
                            <div class="col-8">
                                <br>
                                <input id="submit" class="button primary" type="submit" value="Adicionar">
                            </div>
                        </div>

                    </div>
                    <div id="form_adiantamento" class="row gtr-uniform gtr-50" style="display: none;">
                        <div class="row">
                            <div class="col-10">
                                <label>Descrição</label>
                                <input type="text" name="aitem">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="avalor" step=".01" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1">
                            </div>
                            <div class="col-10">
                                <br>
                                <input id="submit" class="button primary" type="submit" value="Adicionar">
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
<!-- <script>
    function ChangePecaSelect(ID) {
        var pecaID = ID;
        console.log(pecaID)
    }
</script> -->