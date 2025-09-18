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
            <form method="post" action="scripts\ordem_add_item\ordem_add_item.php?ordem=<?php echo $_GET['ordem'] . "&motoID=" . $motoid['motoID'] ?>">
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
                        <div class="row" style="position: relative;">
                            <div class="col-12">
                                <h3>Selecione uma peça</h3>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1;">
                                        <input type="text" list="pecaid" name="pecaid_display" style="width: 100%;">
                                        <datalist id="pecaid">
                                            <?php
                                            $sql_query = "SELECT * FROM pecas ";
                                            $sql_query .= "ORDER BY pecas.grupo ";
                                            $result = mysqli_query($conn, $sql_query);
                                            while ($peca = mysqli_fetch_assoc($result)) { ?>
                                                <option value="<?php echo $peca["grupo"] . " - " . $peca["item"] . " - " . $peca["parte"] ?>" data-id="<?php echo $peca["pecaId"] ?>"></option>
                                            <?php } ?>
                                        </datalist>
                                        <!-- Hidden input for pecaid -->
                                        <input type="hidden" id="pecaid_input" name="pecaid">
                                    </div>
                                    <div>
                                        <a href="tabelaPecasAdd.php" target="_blank" class="button small" title="Criar nova peça">
                                            + Criar Peça
                                        </a>
                                    </div>
                                </div>

                                <script>
                                    // Autocomplete para peças
                                    const pecaInput = document.querySelector('[name="pecaid_display"]');
                                    const pecaDatalist = document.getElementById('pecaid');
                                    const pecaOptions = Array.from(pecaDatalist.options).map(option => ({
                                        value: option.value,
                                        id: option.getAttribute('data-id')
                                    }));

                                    pecaInput.addEventListener('input', function(e) {
                                        const searchTerms = e.target.value.toLowerCase().split(' ');

                                        // Filtrar opções
                                        const filtered = pecaOptions.filter(option => {
                                            const optionText = option.value.toLowerCase();
                                            return searchTerms.every(term => optionText.includes(term));
                                        });

                                        // Atualizar datalist
                                        pecaDatalist.innerHTML = filtered.map(option =>
                                            `<option value="${option.value}" data-id="${option.id}"></option>`
                                        ).join('');

                                        // Atualizar ID correspondente
                                        const match = filtered.find(opt => opt.value === e.target.value);
                                        document.getElementById('pecaid_input').value = match ? match.id : '';
                                    });
                                </script>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-8">
                                <label>Código do Produto</label>
                                <input type="text" name="scode">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input type="number" name="pquantidade" value="1">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="pvalor" step=".01" value="0">
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
                        <div class="row" style="position: relative;">
                            <div class="col-12">
                                <h3>Selecione um serviço</h3>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1;">
                                        <input type="text" name="servico" list="servicoid_list" style="width: 100%;">
                                        <datalist id="servicoid_list">
                                            <?php
                                            $sql_query = "SELECT * FROM servicos ";
                                            $sql_query .= "ORDER BY servicos.tipo ";
                                            $result = mysqli_query($conn, $sql_query);
                                            while ($servico = mysqli_fetch_assoc($result)) { ?>
                                                <option value="<?php echo $servico["tipo"] . " - " . $servico["item"] ?>" data-id="<?php echo $servico["servicoId"] ?>"></option>
                                            <?php } ?>
                                        </datalist>
                                        <!-- Hidden input for servicoid -->
                                        <input type="hidden" id="servicoid" name="servicoid">
                                    </div>
                                    <div>
                                        <a href="tabelaServicosAdd.php" target="_blank" class="button small" title="Criar novo serviço">
                                            + Criar Serviço
                                        </a>
                                    </div>
                                </div>

                                <script>
                                    // Autocomplete para serviços
                                    const servicoInput = document.querySelector('[name="servico"]');
                                    const servicoDatalist = document.getElementById('servicoid_list');
                                    const servicoOptions = Array.from(servicoDatalist.options).map(option => ({
                                        value: option.value,
                                        id: option.getAttribute('data-id')
                                    }));

                                    servicoInput.addEventListener('input', function(e) {
                                        const searchTerms = e.target.value.toLowerCase().split(' ');
                                        const filtered = servicoOptions.filter(option => {
                                            const optionText = option.value.toLowerCase();
                                            return searchTerms.every(term => optionText.includes(term));
                                        });

                                        servicoDatalist.innerHTML = filtered.map(option =>
                                            `<option value="${option.value}" data-id="${option.id}"></option>`
                                        ).join('');

                                        const match = filtered.find(opt => opt.value === e.target.value);
                                        document.getElementById('servicoid').value = match ? match.id : '';
                                    });
                                </script>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="svalor" step=".01" value="0">
                            </div>
                            <div class="col-2">
                                <label>Quantidade</label>
                                <input type="number" name="squantidade" value="1">
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
                                <input type="text" value="Pagamento <?php echo date('d/m/Y'); ?>" name="aitem">
                            </div>
                            <div class="col-2">
                                <label>Valor</label>
                                <input type="number" name="avalor" step=".01" value="0">
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