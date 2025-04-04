<!-- search bar -->
<form id="form-pesquisa" onsubmit="return false;">
    <div class="row">
        <div class="col-3">
            <select name="selectPesquisa" id="selectPesquisa">
                <?php
                //this while is getting all columns from our table
                while ($categorias = mysqli_fetch_assoc($resultCategorias)) {
                    if ($categorias['Field'] == "km" ||$categorias['Field'] == "Id" || $categorias['Field'] == "foto" || $categorias['Field'] == "motoId" || $categorias['Field'] == "ordem" || $categorias['Field'] == "valor" || $categorias['Field'] == "servID" || $categorias['Field'] == "Aberto" || $categorias['Field'] == "pecaId" || $categorias['Field'] == "motoID" || $categorias['Field'] == "quantidade" || $categorias['Field'] == "servicoId") {
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
            <button type="button" id="btn-pesquisar"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<!-- fim da barra de pesquisa -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Evento para o botão de pesquisa
    document.getElementById('btn-pesquisar').addEventListener('click', function() {
        var pesquisa = document.getElementById('input-pesquisa').value;
        var selectPesquisa = document.getElementById('selectPesquisa').value;
        var orderby = document.querySelector('.sort.active') ? 
                     document.querySelector('.sort.active').getAttribute('data-orderby') : '';
        
        window.carregarTabela(1, pesquisa, selectPesquisa, orderby);
    });

    // Evento para pressionar Enter no campo de pesquisa
    document.getElementById('input-pesquisa').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btn-pesquisar').click();
        }
    });
});
</script>
