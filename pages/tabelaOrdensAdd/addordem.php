<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action=".\scripts\tabelaOrdensAdd\create-service.php">
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
                    <label for="moto">Choose a motorcycle:</label>
                    <input type="text" name="modelo" list="motolist" required>
                    <datalist id="motolist">
                        <?php while ($moto = mysqli_fetch_assoc($result)) { ?>
                            <option value="<?php echo $moto["modelo"] ?>" data-id="<?php echo $moto["motoId"] ?>"><?php echo $moto["modelo"] . " - " . $moto["proprietario"] ?></option>
                        <?php } ?>
                    </datalist>
                    <input type="hidden" name="motoid" id="motoid">
                    <script>
                        const input = document.querySelector('input[name="modelo"]');
                        const motoid = document.querySelector('#motoid');

                        input.addEventListener('input', () => {
                            const selectedOption = document.querySelector(`datalist#${input.getAttribute('list')} option[value="${input.value}"]`);
                            if (selectedOption) {
                                motoid.value = selectedOption.getAttribute('data-id');
                            } else {
                                motoid.value = '';
                            }
                        });
                    </script>

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