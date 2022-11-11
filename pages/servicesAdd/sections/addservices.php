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
                                <select name="pecaid">
                                <!-- <select name="pecaid" onchange='ChangePecaSelect(this.value)'> -->
                                    <?php
                                    while ($peca = mysqli_fetch_assoc($result)) {
                                    ?>
                                        <option value="<?php echo $peca["pecaId"] ?>"><?php echo $peca["grupo"] . " - " . $peca["item"] . " - " . $peca["parte"] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input type="number" name="pquantidade" value="<?php echo isset($_POST["pecaid"]) ? 1 : 0 ?>">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="pvalor">
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
                                <select name="servicoid">
                                    <?php
                                    while ($servico = mysqli_fetch_assoc($result)) {
                                    ?>
                                        <option value="<?php echo $servico["servicoId"] ?>"><?php echo $servico["item"] . " - " . $servico["tipo"] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="svalor">
                            </div>
                            <div class="col-3">
                            </div>
                            <div class="col-7">
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
                                <input type="number" name="avalor">
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