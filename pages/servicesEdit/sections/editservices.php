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
            <form method="post" action="scripts\addservices\editservice.php?ordem=<?php echo $_GET['ordem'] . "&item_ordemID=" . $_GET['item_ordemID'] ?>">
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
                                        <option <?php
                                                
                                                    echo $result2['Item'] == $peca["item"] ? "selected" : "";
                                                ?> value="<?php echo $peca["pecaId"] ?>"><?php echo $peca["grupo"] . " - " . $peca["item"] . " - " . $peca["parte"] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
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
                                        <option <?php
                                                
                                                echo $result2['Tipo'] == $servico["tipo"] ? "selected" : "";
                                            ?>
                                            value="<?php echo $servico["servicoId"] ?>"><?php echo $servico["tipo"] . " - " . $servico["item"] ?></option>
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
                                <input value="<?php echo $result2['Valor'] ?>" type="number" name="svalor" step=".01">
                            </div>
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input value="<?php echo $result2['Quantidade'] ?>" type="number" name="squantidade">
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