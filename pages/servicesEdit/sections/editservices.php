<?php
$mysqli_query = "SELECT motoID FROM ordem_servicos ";
$mysqli_query .= " WHERE Codigo = '" . $_GET['ordem'] . "'";

$motoid = mysqli_query($conn, $mysqli_query);
$motoid = mysqli_fetch_assoc($motoid);


switch ($_GET["type"]) {
    case 1:
        $mysqli_query = "SELECT * FROM pecas";
        $mysqli_query .= " WHERE pecaId = " . $_GET["id"];
        break;
    case 2:
        $mysqli_query = "SELECT * FROM servicos";
        $mysqli_query .= " WHERE servicoId = " . $_GET["id"];;
        break;
    case 3:
        $mysqli_query = "SELECT * FROM adiantamento";
        $mysqli_query .= " WHERE IDadiantamento = " . $_GET["id"];;
        break;
}
$item = mysqli_query($conn, $mysqli_query);
$item = mysqli_fetch_assoc($item);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts\editservices\editservice.php?ordem=<?php echo $_GET['ordem'] . "&id=" . $_GET["id"] ?>">
            <?php
            switch ($_GET["type"]) {
                case 1:
            ?>
                    <div class="row">
                        <div class="col-3">
                            <h2>Peça</h2>
                            <input type="hidden" id="pecas" name="tipo_item" value="Peça">
                            <input type="hidden" name="type_pecas">
                        </div>
                    </div>
                <?php
                    break;
                case 2:
                ?>
                    <div class="row">
                        <div class="col-3">
                            <h2>Serviço</h2>
                            <input type="hidden" id="service" name="tipo_item" value="Serviço">
                            <input type="hidden" name="type_servicos">
                        </div>
                    </div>
                <?php
                    break;
                case 3:
                ?>
                    <div class="row">
                        <div class="col-3">
                            <h2>Adiantamento</h2>
                            <input type="hidden" id="adiantamento" name="tipo_item" value="Adiantamento">
                            <input type="hidden" name="type_adiantamento">
                        </div>
                    </div>
            <?php
                    break;
            }
            ?>

            <?php
            switch ($_GET["type"]) {
                case 1:
            ?>

                    <div id="form_pecas">
                        <div class="row">
                            <div class="col-8">
                                <label>Especificação do Produto</label>
                                <input value="<?php echo $item["Codigo"] ?>" type="text" name="scode">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label>Grupo</label><br>
                                <input value="<?php echo $item["grupo"] ?>" type="text" name="pgrupo"><br>
                            </div>
                            <div class="col-4">
                                <label>Item</label><br>
                                <input value="<?php echo $item["item"] ?>" type="text" name="pitem"><br>
                            </div>
                            <div class="col-4">
                                <label>Parte</label><br>
                                <input value="<?php echo $item["parte"] ?>" type="text" name="pparte"><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <label>Quantidade</label><br>
                                <input value="<?php echo $item["quantidade"] ?>" type="text" name="pquantidade"><br>
                            </div>
                            <div class="col-4">
                                <label>Valor</label><br>
                                <input value="<?php echo $item["valor"] ?>" type="text" name="pvalor"><br>
                            </div>
                        </div>
                    </div>
                <?php
                    break;
                case 2:
                ?>
                    <div id="form_services">
                        <br>
                        <div class="row">
                            <div class="col-5">
                                <label>Tipo</label><br>
                                <input value="<?php echo $item["tipo"] ?>" type="text" name="sgrupo"><br>
                            </div>
                            <div class="col-5">
                                <label>Item</label><br>
                                <input value="<?php echo $item["item"] ?>" type="text" name="sitem"><br>
                            </div>
                            <div class="col-2">
                                <label>Valor</label><br>
                                <input value="<?php echo $item["valor"] ?>" type="text" name="svalor"><br>
                            </div>
                        </div>

                    </div>
                <?php
                    break;
                case 3:
                ?>
                    <div id="form_adiantamento">
                        <div class="row">
                            <div class="col-6">
                                <label>Descrição</label><br>
                                <input value="<?php echo $item["descricao"] ?>" type="text" name="aitem"><br>
                            </div>
                            <div class="col-6">
                                <label>Valor</label><br>
                                <input value="<?php echo $item["valor"] ?>" type="text" name="avalor"><br>
                            </div>
                        </div>
                    </div>
            <?php
                    break;
            }
            ?>
            <hr>
            <input class="button primary" type="submit" value="Salvar">
        </form>
    </div>
</section>