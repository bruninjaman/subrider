<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action=".\scripts\tabelaMotoServices\create-service.php">
            <div class="row">
                <div class="col-12">
                    <?php
                    //CREATE NEW CODE
                    $mysqli_query = "SELECT Codigo FROM ordem_servicos ";
                    $ordem_result = mysqli_query($conn, $mysqli_query);

                    //CHECK ORDEMS
                    $existem_ordems = mysqli_num_rows($ordem_result) > 0 ? true : false;

                    //CREATE A NEW CODE
                    $novo_codigo = 100;
                    if ($existem_ordems) {
                        while ($ordem = mysqli_fetch_assoc($ordem_result)) {
                            $ordem_splited = explode("/", $ordem['Codigo']);
                            if ($ordem_splited[0] > $novo_codigo)
                                $novo_codigo = $ordem_splited[0];
                        }
                    } else {
                        $novo_codigo = '100' . '/' . date("Y");
                    }
                    $novo_codigo = $novo_codigo + 1 . '/' . date("Y");
                    ?>
                    <h2>Ordem <?php echo $novo_codigo ?></h2>
                    <h3>Selecione uma moto</h3>
                    <?php
                    $sql_query = "SELECT * FROM motocicletas";
                    $result = mysqli_query($conn, $sql_query);
                    ?>
                    <select name="motoid" required>
                        <?php
                        while ($moto = mysqli_fetch_assoc($result)) {
                        ?>
                            <option value="<?php echo $moto["motoId"] ?>"><?php echo $moto["modelo"] . " - " . $moto["proprietario"] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label>Data: </label>
                    <input type="date" name="data">
                </div>
                <div class="col-4">
                    <label>Quilometragem:</label>
                    <input type="number" name="km" style="width:250px;">
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Abrir Ordem de Serviço" style="width: 100%;">
        </form>
    </div>
</section>