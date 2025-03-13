<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action=".\scripts\tabelaOrdensAdd\create-service.php">
            <div class="row">
                <div class="col-12">
                    <?php
                    //CREATE NEW CODE
                    $mysqli_query = "SELECT Codigo FROM ordem_servicos";
                    $ordem_result = mysqli_query($conn, $mysqli_query);

                    //CHECK ORDEMS
                    $existem_ordems = mysqli_num_rows($ordem_result) > 0 ? true : false;

                    // Get the current year
                    $current_year = date("Y");

                    // CREATE A NEW CODE
                    $novo_codigo = 100; // Default starting code
                    $last_year = null; // Variable to store the last year's code

                    if ($existem_ordems) {
                        while ($ordem = mysqli_fetch_assoc($ordem_result)) {
                            $ordem_splited = explode("/", $ordem['Codigo']);
                            $last_code = $ordem_splited[0]; // The numeric part

                            // Only try to access the year part if it exists
                            if (isset($ordem_splited[1])) {
                                $last_year = $ordem_splited[1]; // The year part

                                // Check if the year is the same as the current year
                                if ($last_year == $current_year && $last_code > $novo_codigo) {
                                    $novo_codigo = $last_code; // Update to the highest code for this year
                                }
                            }
                        }
                    }

                    // If no codes exist for the current year, start from 100
                    if ($last_year != $current_year || !$existem_ordems) {
                        $novo_codigo = 100;
                    }

                    // Increment the code and add the year
                    $novo_codigo = ($novo_codigo + 1) . '/' . $current_year;

                    ?>
                    <h2>Ordem <?php echo $novo_codigo ?></h2>
                    <?php
                    $sql_query = "SELECT * FROM motocicletas";
                    $result = mysqli_query($conn, $sql_query);
                    ?>
                    <label for="moto">Selecione uma moto:</label>
                    <input type="text" name="modelo" list="motolist" required>
                    <datalist id="motolist">
                        <?php while ($moto = mysqli_fetch_assoc($result)) { ?>
                            <option value="<?php echo $moto["modelo"] ?>" data-id="<?php echo $moto["motoId"] ?>"><?php echo $moto["modelo"] . " - " . $moto["proprietario"] ?></option>
                        <?php } ?>
                    </datalist>
                    <input type="hidden" name="motoid" id="motoid">

                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label>Data: </label>
                    <input type="date" name="data" id="dataInput">
                </div>
                <div class="col-4">
                    <label>Quilometragem:</label>
                    <input type="number" name="km" style="width:250px;">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Proprietario: </label>
                    <input type="text" name="proprietario_ordem" id="proprietario_ordem" required>
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Abrir Ordem de Serviço" style="width: 100%;">
        </form>
    </div>
    <script>
        // Set today's date in the date input field
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dataInput').value = today;
        });

        const input = document.querySelector('input[name="modelo"]');
        const motoid = document.querySelector('#motoid');
        const proprietarioInput = document.querySelector('#proprietario_ordem');

        input.addEventListener('input', () => {
            const selectedOption = document.querySelector(`datalist#${input.getAttribute('list')} option[value="${input.value}"]`);
            if (selectedOption) {
                motoid.value = selectedOption.getAttribute('data-id');
                proprietarioInput.value = selectedOption.textContent.split(' - ')[1]; // Extract the owner name
            } else {
                motoid.value = '';
                proprietarioInput.value = ''; // Clear the owner name if no selection
            }
        });
    </script>
</section>