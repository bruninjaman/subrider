<?php
$mysqli_query = "SELECT motoID FROM ordem_servicos ";
$mysqli_query .= " WHERE Codigo = '" . $_GET['ordem'] . "'";

$motoid = mysqli_query($conn, $mysqli_query);
$motoid = mysqli_fetch_assoc($motoid);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <h2></h2>
        <center>
            <form method="post" action="scripts\addservices\addservice.php?ordem=<?php echo $_GET['ordem'] . "&motoID=" . $motoid['motoID'] ?>">
                <div>
                    <div class="row">
                        <div class="col-12">
                            <input onchange="tipo_item_onchange()" type="radio" id="pecas" name="tipo_item" value="pecas">
                            <label for="pecas">Peça</label>
                            <input onchange="tipo_item_onchange()" type="radio" id="service" name="tipo_item" value="service">
                            <label for="service">Serviço</label>
                            <input onchange="tipo_item_onchange()" type="radio" id="adiantamento" name="tipo_item" value="adiantamento">
                            <label for="adiantamento">Adiantamento</label>
                        </div>
                    </div>

                    <div id="form_pecas" style="display: none;">
                        <div class="row">
                            <div class="col-4">
                                <label>Grupo</label>
                                <input type="text" name="pgrupo">
                            </div>
                            <div class="col-4">
                                <label>Item</label>
                                <input type="text" name="pitem">
                            </div>
                            <div class="col-4">
                                <label>Parte</label>
                                <input type="text" name="pparte">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input type="number" name="pquantidade">
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
                            <div class="col-6">
                                <label>Tipo</label>
                                <input type="text" name="sgrupo">
                            </div>
                            <div class="col-6">
                                <label>Item</label>
                                <input type="text" name="sitem">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="svalor">
                            </div>
                            <div class="col-1">
                            </div>
                            <div class="col-7">
                                <br>
                                <input id="submit" class="button primary" type="submit" value="Adicionar">
                            </div>
                        </div>

                    </div>
                    <div id="form_adiantamento" class="row gtr-uniform gtr-50" style="display: none;">
                        <div class="row">
                            <div class="col-1">
                            </div>
                            <div class="col-7">
                                <label>Descrição</label>
                                <input type="text" name="aitem">
                            </div>
                            <div class="col-3">
                                <label>Valor</label><br>
                                <input type="number" name="avalor">
                            </div>
                            <div class="col-1">
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