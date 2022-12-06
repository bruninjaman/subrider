<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action=".\scripts\tabelaMotoServices\edit-service.php?ordem=<?php echo $_GET['ordem']?>">
            <?php
            $mysql_query = "SELECT * FROM ordem_servicos ";
            $mysql_query .= "WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "'";

            $result2 = mysqli_query($conn, $mysql_query);
            $result2 = mysqli_fetch_assoc($result2);
            ?>
            <div class="row">
                <div class="col-12">
                    <h2>Ordem <?php echo $result2["Codigo"] ?></h2>
                    <h3>Selecione uma moto</h3>
                    <?php
                    $sql_query = "SELECT * FROM motocicletas";
                    $result = mysqli_query($conn, $sql_query);
                    ?>
                    <select name="motoid" required>
                        <?php
                        while ($moto = mysqli_fetch_assoc($result)) {
                        ?>
                            <option 
                            <?php 
                            echo $moto["motoId"] == $result2["motoID"] ? "selected " : " ";
                            ?>
                            value="<?php echo $moto["motoId"] ?>"><?php echo $moto["modelo"] . " - " . $moto["proprietario"] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label>Data: </label>
                    <input type="date" name="data" value="<?php echo $result2['Data']?>">
                </div>
                <div class="col-4">
                    <label>Quilometragem:</label>
                    <input type="number" name="km" style="width:250px;" value="<?php echo $result2['KM']?>">
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Salvar Ordem de Serviço" style="width: 100%;">
        </form>
    </div>
</section>