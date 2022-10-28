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
    <div class="content">
        <center>
            <form method="post" action="scripts\editservices\editservice.php?ordem=<?php echo $_GET['ordem'] . "&id=" . $_GET["id"] ?>">
                <h2>Escolha o tipo de item:</h2>
                <?php
                switch ($_GET["type"]) {
                    case 1:
                ?>
                        <input type="button" class="button primary fit" id="pecas" name="tipo_item" value="pecas">
                        <input type="hidden" name="type_pecas">
                    <?php
                        break;
                    case 2:
                    ?>
                        <input type="button" class="button primary fit" id="service" name="tipo_item" value="service">
                        <input type="hidden" name="type_servicos">
                    <?php
                        break;
                    case 3:
                    ?>
                        <input type="button" class="button primary fit" id="adiantamento" name="tipo_item" value="adiantamento">
                        <input type="hidden" name="type_adiantamento">
                <?php
                        break;
                }
                ?>

                <?php
                switch ($_GET["type"]) {
                    case 1:
                ?>

                        <div id="form_pecas">
                            <label>grupo</label><br>
                            <input value="<?php echo $item["grupo"] ?>" type="text" name="pgrupo"><br>
                            <label>item</label><br>
                            <input value="<?php echo $item["item"] ?>" type="text" name="pitem"><br>
                            <label>parte</label><br>
                            <input value="<?php echo $item["parte"] ?>" type="text" name="pparte"><br>
                            <label>quantidade</label><br>
                            <input value="<?php echo $item["quantidade"] ?>" type="text" name="pquantidade"><br>
                            <label>valor</label><br>
                            <input value="<?php echo $item["valor"] ?>" type="text" name="pvalor"><br>
                        </div>
                    <?php
                        break;
                    case 2:
                    ?>
                        <div id="form_services">
                            <label>Tipo</label><br>
                            <input value="<?php echo $item["tipo"] ?>" type="text" name="sgrupo"><br>
                            <label>Item</label><br>
                            <input value="<?php echo $item["item"] ?>" type="text" name="sitem"><br>
                            <label>Valor</label><br>
                            <input value="<?php echo $item["valor"] ?>" type="text" name="svalor"><br>

                        </div>
                    <?php
                        break;
                    case 3:
                    ?>
                        <div id="form_adiantamento">
                            <label>Descrição</label><br>
                            <input value="<?php echo $item["descricao"] ?>" type="text" name="aitem"><br>
                            <label>Valor</label><br>
                            <input value="<?php echo $item["valor"] ?>" type="text" name="avalor"><br>
                        </div>
                <?php
                        break;
                }
                ?>
                <br>
                <input class="button primary" type="submit" value="Editar">
            </form>
        </center>
    </div>
</section>