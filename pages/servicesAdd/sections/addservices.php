<?php 
    $mysqli_query = "SELECT motoID FROM ordem_servicos ";
    $mysqli_query .= " WHERE Codigo = '". $_GET['ordem'] . "'";

    $motoid = mysqli_query($conn, $mysqli_query);
    $motoid = mysqli_fetch_assoc($motoid);
?>
<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="scripts\addservices\addservice.php?ordem=<?php echo $_GET['ordem'] . "&motoID=" . $motoid['motoID'] ?>">
                <h2>Escolha o tipo de item:</h2>

                <input onchange="tipo_item_onchange()" type="radio" id="pecas" name="tipo_item" value="pecas">
                <label for="pecas">Peça</label>
                <input onchange="tipo_item_onchange()" type="radio" id="service" name="tipo_item" value="service">
                <label for="service">Serviço</label>
                <input onchange="tipo_item_onchange()" type="radio" id="adiantamento" name="tipo_item" value="adiantamento">
                <label for="adiantamento">Adiantamento</label>

                <div id="form_pecas" style="display: none;">
                    <label>grupo</label><br>
                    <input type="text" name="pgrupo"><br>
                    <label>item</label><br>
                    <input type="text" name="pitem"><br>
                    <label>parte</label><br>
                    <input type="text" name="pparte"><br>
                    <label>quantidade</label><br>
                    <input type="text" name="pquantidade"><br>
                    <label>valor</label><br>
                    <input type="text" name="pvalor"><br>
                </div>
                <div id="form_services" style="display: none;">
                    <label>Tipo</label><br>
                    <input type="text" name="sgrupo"><br>
                    <label>Item</label><br>
                    <input type="text" name="sitem"><br>
                    <label>Valor</label><br>
                    <input type="text" name="svalor"><br>

                </div>
                <div id="form_adiantamento" style="display: none;">
                    <label>Descrição</label><br>
                    <input type="text" name="aitem"><br>
                    <label>Valor</label><br>
                    <input type="text" name="avalor"><br>
                </div>
                <br>
                <input class="button primary" type="submit" value="Adicionar">
            </form>
        </center>
    </div>
</section>