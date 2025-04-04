<!-- search bar -->
<form id="form-pesquisa" action="" method="get">
    <input type="hidden" name="page" value="1">
    <div class="row">
        <div class="col-3">
            <select name="selectPesquisa" id="selectPesquisa">
                <?php
                //Categorias de pesquisa
                $categoriasPesquisa = "SHOW COLUMNS FROM motocicletas";
                $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
                //this while is getting all columns from our table
                while ($categorias = mysqli_fetch_assoc($resultCategorias)) {
                    if ($categorias['Field'] == "km" || $categorias['Field'] == "Id" || $categorias['Field'] == "foto" || $categorias['Field'] == "motoId" || $categorias['Field'] == "ordem" || $categorias['Field'] == "valor" || $categorias['Field'] == "servID" || $categorias['Field'] == "Aberto" || $categorias['Field'] == "pecaId" || $categorias['Field'] == "motoID" || $categorias['Field'] == "quantidade" || $categorias['Field'] == "servicoId" || $categorias['Field'] == "proprietario") {
                        continue;
                    }
                ?>
                    <option value="<?php echo $categorias['Field'] ?>"><?php echo ucfirst($categorias['Field']) ?></option>
                <?php
                }
                ?>

                <!-- Table 2 -->
                <?php
                //Categorias de pesquisa
                $categoriasPesquisa = "SHOW COLUMNS FROM ordem_servicos";
                $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
                //this while is getting all columns from our table
                while ($categorias = mysqli_fetch_assoc($resultCategorias)) {
                    if ($categorias['Field'] == "servID" || $categorias['Field'] == "motoID" || $categorias['Field'] == "KM" || $categorias['Field'] == "data" || $categorias['Field'] == "Status" || $categorias['Field'] == "Codigo") {
                        continue;
                    }
                ?>
                    <option value="<?php echo $categorias['Field'] ?>">
                        <?php echo $categorias['Field'] == "proprietario_ordem" ? "Proprietario" : ucfirst($categorias['Field']) ?>
                    </option>

                <?php
                }
                ?>
            </select>
        </div>
        <div class="col-4 search">
            <input type="text" name="pesquisa" id="input-pesquisa" placeholder="Pesquisar..">
            <button type="submit" id="btn-pesquisar"><i class="fa fa-search"></i></button>
        </div>

    </div>
</form>
<!-- fim da barra de pesquisa -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar event listener para o formulário de pesquisa
    var formPesquisa = document.getElementById('form-pesquisa');
    if (formPesquisa) {
        formPesquisa.addEventListener('submit', function(event) {
            event.preventDefault();
            
            var pesquisa = document.getElementById('input-pesquisa').value;
            var selectPesquisa = document.getElementById('selectPesquisa').value;
            
            // Verifica se existe a função carregarTabela (definida em tabela.php)
            if (typeof window.carregarTabela === 'function') {
                window.carregarTabela(1, pesquisa, selectPesquisa);
            } else {
                console.error('Função carregarTabela não encontrada. Verifique se o script em tabela.php foi carregado corretamente.');
            }
        });
    }
});
</script>