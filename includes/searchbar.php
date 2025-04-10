<!-- search bar -->
<form id="form-pesquisa" onsubmit="return false;">
    <div class="row">
        <div class="col-3">
            <select name="selectPesquisa" id="selectPesquisa">
                <?php
                // Itera sobre o array $colunasPesquisa passado pelo include
                // A filtragem de colunas indesejadas já foi feita antes
                if (isset($colunasPesquisa) && is_array($colunasPesquisa)) {
                    foreach ($colunasPesquisa as $coluna) {
                        // Não precisa mais da verificação aqui
                        // A coluna já vem sanitizada de tabela.php
                        ?>
                        <option value="<?php echo $coluna; ?>"><?php echo ucfirst($coluna); ?></option>
                        <?php
                    }
                } else {
                    // Fallback caso $colunasPesquisa não esteja definido ou não seja um array
                    echo '<option value="">Erro ao carregar colunas</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-4 search">
            <input type="text" name="pesquisa" id="input-pesquisa" placeholder="Pesquisar..">
            <button type="button" id="btn-pesquisar"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<!-- fim da barra de pesquisa -->
