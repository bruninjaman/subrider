<!-- search bar -->
<form id="form-pesquisa" action="" method="get">
    <input type="hidden" name="page" value="1">
    <div class="row">
        <div class="col-3">
            <select name="selectPesquisa" id="selectPesquisa">
                <?php
                //Categorias de pesquisa
                $categoriasPesquisa = "SHOW COLUMNS FROM pecas";
                $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
                //this while is getting all columns from our table
                while ($categorias = mysqli_fetch_assoc($resultCategorias)) {
                    if ($categorias['Field'] == "foto" || $categorias['Field'] == "pecaId") {
                        continue;
                    }
                ?>
                    <option value="<?php echo $categorias['Field'] ?>"><?php echo ucfirst($categorias['Field']) ?></option>
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